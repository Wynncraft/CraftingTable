<?php

class UserController extends BaseController {

    public function getRegister() {
        return View::make('register');
    }

    public function postRegister() {
        $user = new User;
        $user->email = Input::get('email');
        $user->username = Input::get('username');
        $user->password = Hash::make(Input::get('password'));

        $validator = Validator::make(
            array('email'=>$user->email,
                'username'=>$user->username,
                'password'=>Input::get('password')),
            array('email'=>'required|email|unique:users',
                'username'=>'required|unique:users',
                'password'=>'required')
        );

        if (App::environment() =='demo') {
            return View::make('register')->with('success', 'Please login with the email demo@minestack.io and password demo');
        }

        if ($validator->fails()) {
            return View::make('register')->with('error', $validator->messages());
        } else {
            $user->save();
            $theEmail = Input::get('email');
            return View::make('register')->with('success', 'Thank you '.$theEmail.' for registering.');
        }
    }

    public function getLogin() {
        return View::make('login');
    }

    public function postLogin() {
        $email = Input::get('email');
        $password = Input::get('password');

        if (Auth::attempt(array('email'=>$email, 'password'=>$password))) {
            return Redirect::intended('/');
        }

        return View::make('login')->with('error', 'Invalid Username or password');
    }

    public function getLogout() {
        Auth::logout();

        return View::make('logout');
    }

    public function getUsers() {
        return View::make('users');
    }

    public function getUser(User $user = null) {
        if ($user == null) {
            return Redirect::intended('/users')->with(array('code' => '404', 'message' => 'Unknown user Id'));
        }

        if (Auth::user()->id != $user->id) {
            return Redirect::intended('/users/'.Auth::user()->id);
        }

        return View::make('user')->with('user', $user);
    }

    public function putUser(User $user = null) {
        if ($user == null) {
            return Redirect::intended('/users')->with('error', 'Unknown user Id');
        }

        if (Auth::user()->id != $user->id) {
            return Redirect::intended('/users/'.Auth::user()->id);
        }

        Validator::extend('passcheck', function($attribute, $value, $params) {
            return Hash::check($value, Auth::user()->getAuthPassword());
        });

        $validator = Validator::make(
            array('email'=>Input::get('email'),
                'password'=>Input::get('password')),
            array('email'=>'required|email|unique:users',
                'password'=>'required|passcheck'),
            array('passcheck'=>'Your current password is invalid')
        );

        if (Auth::user()->email == Input::get('email')) {
            $validator = Validator::make(
                array('password'=>Input::get('password')),
                array('password'=>'required|passcheck'),
                array('passcheck'=>'Your current password is invalid')
            );
        }

        if (App::environment() =='demo') {
            return View::make('user')->with('user', $user)->with('success', 'Cannot update user info while in demo mode.');
        }

        if ($validator->fails()) {
            return View::make('user')->with('user', $user)->with('error', $validator->messages());
        } else {
            $user->email = Input::get('email');
            $password = Input::get('npassword');
            if (strlen($password) > 0) {
                $user->password = Hash::make(Input::get('npassword'));
            }
            $user->save();
        }

        return View::make('user')->with('user', $user)->with('success', 'Successfully updated your account info.');
    }

}