<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// ROLE

Route::get('/roles', 'RoleController@index')->name('roles.index')->middleware('auth');
Route::get('/roles/{id}', 'RoleController@show')->name('roles.show')->middleware('auth');
Route::get('/roles/{id}/edit', 'RoleController@edit')->name('roles.edit')->middleware('auth');
Route::put('/roles/{id}', 'RoleController@update')->name('roles.update')->middleware('auth');

// USER
Route::get('/users/{id}', 'UserController@show')->name('users.show')->middleware('auth');

