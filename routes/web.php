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


Route::get('/home', 'HomeController@index')->name('home');



Route::group(['middleware' => 'auth'], function() {
    // ROLE
    Route::get('/roles', 'RoleController@index')->name('roles.index')->middleware('auth');
    Route::get('/roles/{id}', 'RoleController@show')->name('roles.show');
    Route::get('/roles/{id}/edit', 'RoleController@edit')->name('roles.edit');
    Route::put('/roles/{id}', 'RoleController@update')->name('roles.update');
    Route::delete('/roles/{roleId}/users/{userId}', 'RoleController@removeUser')->name('roles.removeUser');

    // USER
    Route::get('/users/{id}', 'UserController@show')->name('users.show');
    Route::delete('/users/{userId}/roles/{roleId}', 'RoleController@removeUser')->name('users.removeRole');
});





// Routes in the following group can only be access from inside the hackspace (as defined by the ip range in .env)
Route::group(['middleware' => 'ipcheck'], function () {
    Route::get('/register-interest', 'RegisterInterestController@index')->name('registerInterest');
    Route::post('/register-interest', 'RegisterInterestController@registerInterest');
});
