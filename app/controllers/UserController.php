<?php


class UserController extends BaseController {

    public function getRegister() {
        return View::make('register');
    }

    public function postRegister() {
        $user = new Toddish\Verify\Models\User;
        $user->email = Input::get('email');
        $user->username = Input::get('username');
        $user->password = Input::get('password');
        $user->verified = 1;
        $user->disabled = 1;

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

        if ($validator->fails()) {
            return View::make('register')->with('error', $validator->messages());
        } else {
            if (App::environment() =='demo') {
                return View::make('register')->with('success', 'Please login with the email demo@minestack.io and password demo');
            }

            $first = false;

            if (Toddish\Verify\Models\User::enabled()->get()->count() == 0) {
                $user->verified = 1;
                $user->disabled = 0;
                $first = true;
            }

            $user->save();

            $theEmail = Input::get('email');
            if ($first == true) {
                $role = Toddish\Verify\Models\Role::where('name', '=', 'Super Admin')->firstOrFail();
                $user->roles()->sync(array($role->id));

                return View::make('register')->with('success', 'Thank you '.$theEmail.' for registering. This is the first user so it was granted the Super Admin Permission');
            } else {
                return View::make('register')->with('success', 'Thank you '.$theEmail.' for registering.');
            }

        }
    }

    public function getLogin() {
        return View::make('login');
    }

    public function postLogin() {
        $email = Input::get('email');
        $password = Input::get('password');

        try {
            Auth::attempt(array('identifier'=>$email, 'password'=>$password));
            return Redirect::intended('/');
        } catch(Toddish\Verify\UserPasswordIncorrectException $e) {
            return View::make('login')->with('error', 'Invalid Email/Username or password');
        } catch (Toddish\Verify\UserNotFoundException  $e) {
            return View::make('login')->with('error', 'Invalid Email/Username or password');
        } catch (Toddish\Verify\UserDisabledException  $e) {
            return View::make('login')->with('error', 'User is disabled');
        } catch (Toddish\Verify\UserUnverifiedException  $e) {
            return View::make('login')->with('error', 'User is not verified');
        }
    }

    public function getLogout() {
        Auth::logout();

        return View::make('logout');
    }

    public function getUsers() {
        if (Auth::user()->can('read_user') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the users page');
        }

        return View::make('users');
    }

    public function getUser(Toddish\Verify\Models\User $user = null) {
        if ($user == null) {
            return Redirect::to('/users')->with('error', 'Unknown user Id');
        }

        if (Auth::user()->id != $user->id) {
            if (Auth::user()->can('read_user')) {
                return View::make('user')->with('user', $user);
            } else {
                return Redirect::intended('/users/' . Auth::user()->id);
            }
        }

        return View::make('user')->with('user', $user);
    }

    public function putUser(Toddish\Verify\Models\User $user = null) {
        if ($user == null) {
            return Redirect::to('/users')->with('error', 'Unknown user Id');
        }

        if (Auth::user()->id != $user->id) {
            if (Auth::user()->can('update_user')) {

                $validator = Validator::make(
                    array('email'=>Input::get('email'),
                        'group'=>Input::get('group')),
                    array('email'=>'required|email|unique:users',
                        'group'=>'required|numeric')
                );

                if ($user->email == Input::get('email')) {
                    $validator = Validator::make(
                        array('group'=>Input::get('group')),
                        array('group'=>'required|numeric')
                    );
                }

                if ($validator->fails()) {
                    return View::make('user')->with('user', $user)->with('error', $validator->messages());
                } else {

                    $user->email = Input::get('email');
                    $user->disabled = Input::get('disabled');
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
                        $user->password = Input::get('npassword');
                    }

                    $user->save();

                    $user->roles()->sync(array(Toddish\Verify\Models\Role::find(Input::get('group'))->first()->id));
                    return View::make('user')->with('user', $user)->with('success', 'Successfully updated ' . $user->username . '\'s account info.');
                }
            } else {
                return Redirect::to('/users')->with('error', 'You do not have permission to update users.');
            }
        }

        Validator::extend('passcheck', function($attribute, $value, $params) {
            return Hash::check(Auth::user()->salt.$value, Auth::user()->getAuthPassword());
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
                $user->password = Input::get('npassword');
            }
            $user->save();
        }

        return View::make('user')->with('user', $user)->with('success', 'Successfully updated your account info.');
    }

}