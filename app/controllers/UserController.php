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

    private function putUserMain(Toddish\Verify\Models\User $user = null)
    {
        $group = Input::get('group');
        $password = Input::get('password');

        Validator::extend('validategroup', function ($attribute, $value, $parameters) {
            try {
                Toddish\Verify\Models\Role::findOrFail($value);
                return true;
            } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return false;
            }
        });

        Validator::extend('passcheck', function($attribute, $value, $parameters) {
            return Hash::check(Auth::user()->salt.$value, Auth::user()->getAuthPassword());
        });

        $input = array('email' => Input::get('email'),
            'group' => $group);
        $rules = array('email' => 'required|email|unique:users',
            'group' => 'required|numeric|validategroup');
        $messages = array('validategroup' => 'Please select a valid group');

        if ($user->email == Input::get('email')) {
            unset($input['email']);
            unset($rules['email']);
        }

        if ($group == null) {
            unset($input['group']);
            unset($rules['group']);
        }

        $npassword = Input::get('npassword');
        if (strlen($npassword) > 0) {
            $input['new password'] = $npassword;
            $input['new password_confirmation'] = Input::get('npassword_confirmation');
            $rules['new password'] = 'required|confirmed';
            $rules['new password_confirmation'] = 'required';
        }

        if (Auth::user()->id == $user->id) {
            $input['password'] = $password;
            $rules['password'] = 'required|passcheck';
            $messages['passcheck'] = 'Your current password is invalid';
        }

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return View::make('user')->with('user', $user)->with('error', $validator->messages());
        } else {
            $user->email = Input::get('email');
            $user->disabled = Input::get('disabled');
            if (strlen($npassword) > 0) {
                $user->password = $npassword;
            }

            $user->save();

            if ($group != null) {
                $user->roles()->sync(array(Toddish\Verify\Models\Role::find($group)->first()->id));
            }
            return View::make('user')->with('user', $user)->with('success', 'Successfully updated ' . $user->username . '\'s account info.');
        }
    }

    public function putUser(Toddish\Verify\Models\User $user = null) {
        if ($user == null) {
            return Redirect::to('/users')->with('error', 'Unknown user Id');
        }

        if (Auth::user()->id != $user->id) {
            if (Auth::user()->can('update_user')) {
                return $this->putUserMain($user);
            } else {
                return Redirect::to('/users')->with('error', 'You do not have permission to update users.');
            }
        }

        return $this->putUserMain($user);

    }

}