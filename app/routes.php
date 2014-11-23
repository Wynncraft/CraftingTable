<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before' => 'auth'), function() {
	Route::model('user', 'Toddish\Verify\Models\User', function() {
	});

	Route::get('/', function()
	{
		return View::make('index');
	});

	Route::get('/logout', 'UserController@getLogout');

	Route::get('/users', 'UserController@getUsers');

	Route::get('/users/{user}', 'UserController@getUser');

	Route::put('/users/{user}', 'UserController@putUser');
});

Route::group(array('before' => 'auth.guest'), function() {
	Route::get('/register', 'UserController@getRegister');

	Route::post('/register', 'UserController@postRegister');

	Route::get('/login', 'UserController@getLogin');

	Route::post('/login', 'UserController@postLogin');
});

