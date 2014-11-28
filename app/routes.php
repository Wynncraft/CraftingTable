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

	Route::model('group', 'Toddish\Verify\Models\Role', function() {
	});

	Route::model('network', 'Network', function() {
	});

	Route::model('node', 'Node', function() {
	});

	Route::get('/', function()
	{
		return View::make('index');
	});

	Route::get('/logout', 'UserController@getLogout');

	Route::get('/users', 'UserController@getUsers');

	Route::get('/users/{user}', 'UserController@getUser');

	Route::put('/users/{user}', 'UserController@putUser');

	Route::get('/groups', 'GroupController@getGroups');

	Route::get('/groups/add', 'GroupController@getAddGroup');

	Route::post('/groups/add', 'GroupController@postGroup');

	Route::get('/groups/{group}', 'GroupController@getGroup');

	Route::put('/groups/{group}', 'GroupController@putGroup');

	Route::delete('/groups/{group}', 'GroupController@deleteGroup');

	Route::post('/network/add', array('uses' => 'NetworkController@postNetwork'));

	Route::put('/network/{network}', array('uses' => 'NetworkController@putNetwork'));

});

Route::group(array('before' => 'auth.guest'), function() {
	Route::get('/register', 'UserController@getRegister');

	Route::post('/register', 'UserController@postRegister');

	Route::get('/login', 'UserController@getLogin');

	Route::post('/login', 'UserController@postLogin');
});

