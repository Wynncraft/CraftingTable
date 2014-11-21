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

Route::get('/', function()
{
	return View::make('index');
});

Route::get('/register', function()
{
	return View::make('register');
});

Route::post('/register', function()
{
	$user = new User;
	$user->email = Input::get('email');
	$user->username = Input::get('username');
	$user->password = Hash::make(Input::get('password'));
	$user->save();

	$theEmail = Input::get('email');
	return View::make('thanks')->with('theEmail',$theEmail);
});

Route::get('/login', function()
{
	return View::make('login');
});

Route::get('/logout', function()
{
	return View::make('logout');
});
