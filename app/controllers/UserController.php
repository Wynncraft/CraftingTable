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

    }

    public function getUser(User $user = null) {
        if ($user == null) {
            return Redirect::intended('/users')->with(array('code' => '404', 'message' => 'Unknown user Id'));
        }

        if (Auth::user()->id != $user->id) {
            return Redirect::intended('/users/'.Auth::user()->id);
        }

        return View::make('user');
    }

    public function putUser(User $user = null) {
        if ($user == null) {
            return Redirect::intended('/users')->with(array('code' => '404', 'message' => 'Unknown user Id'));
        }

        if (Auth::user()->id != $user->id) {
            return Redirect::intended('/users/'.Auth::user()->id);
        }

        return View::make('user');
    }

}