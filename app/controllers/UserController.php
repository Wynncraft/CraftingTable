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

        Validator::extend('passmatches', function($attribute, $value, $params) {
            return $value[0] == $value[1] && strlen($value[0]) > 0;
        });

        $validator = Validator::make(
            array('email'=>$user->email,
                'username'=>$user->username,
                'password'=>Input::get('password'),
                'password_confirmation'=>Input::get('password_confirmation')),
            array('email'=>'required|email|unique:users',
                'username'=>'required|unique:users',
                'password'=>'required|confirmed',
                'password_confirmation'=>'required|same:password')
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

        if ($validator->fails()) {
            return View::make('user')->with('user', $user)->with('error', $validator->messages());
        } else {
            if (App::environment() =='demo') {
                return View::make('user')->with('user', $user)->with('success', 'Cannot update user info while in demo mode.');
            }

            $user->email = Input::get('email');
            $password = Input::get('npassword');
            if (strlen($password) > 0) {
                $validator = Validator::make(
                    array('npassword'=>Input::get('npassword'),
                        'npassword_confirmation'=>Input::get('npassword_confirmation')),
                    array('npassword'=>'required|confirmed',
                        'npassword_confirmation'=>'required|same:npassword')
                );
                if ($validator->fails()) {
                    return View::make('user')->with('user', $user)->with('error', $validator->messages());
                }
                $user->password = Hash::make(Input::get('npassword'));
            }
            $user->save();
        }

        return View::make('user')->with('user', $user)->with('success', 'Successfully updated your account info.');
    }

}