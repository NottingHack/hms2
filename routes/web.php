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

/*
 * All urls should be hyphenated
 */

Route::get('/', function () {
    return view('welcome');
})->name('index');

// Auth Routes
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::get('register/{token}', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

Route::get('home', 'HomeController@index')->name('home');

// Routes in the following group can only be access from inside the hackspace (as defined by the ip range in .env)
Route::group(['middleware' => 'ipcheck'], function () {
    Route::get('/register-interest', 'RegisterInterestController@index')->name('registerInterest');
    Route::post('/register-interest', 'RegisterInterestController@registerInterest');
});

// Routes in the following group can only be access once logged-in)
Route::group(['middleware' => 'auth'], function () {
    // Meta area covers various setting for HMS
    Route::resource('metas', 'MetaController',
        [
            'except' => ['show', 'store', 'create', 'destroy'],
        ]
    );

    // Usefull links
    Route::resource('links', 'LinksController',
        [
            'except' => ['show']
        ]
    );
});
