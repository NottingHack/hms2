<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 * All urls should be hyphenated
 */

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }

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
Route::get('access-codes', 'HomeController@accessCodes')->name('accessCodes');

// Routes in the following group can only be access from inside the hackspace (as defined by the ip range in .env)
Route::group(['middleware' => 'ipcheck'], function () {
    Route::get('/register-interest', 'RegisterInterestController@index')->name('registerInterest');
    Route::post('/register-interest', 'RegisterInterestController@registerInterest');
});

// Routes in the following group can only be access once logged-in)
Route::group(['middleware' => 'auth'], function () {
    // ROLE
    Route::get('/roles', 'RoleController@index')->name('roles.index');
    Route::get('/roles/{role}', 'RoleController@show')->name('roles.show');
    Route::get('/roles/{role}/edit', 'RoleController@edit')->name('roles.edit');
    Route::put('/roles/{role}', 'RoleController@update')->name('roles.update');
    Route::delete('/roles/{role}/users/{user}', 'RoleController@removeUser')->name('roles.removeUser');

    // USER
    Route::get('users-by-role/{role}', 'UserController@listUsersByRole')->name('users.byRole');
    Route::resource('users', 'UserController',
        [
            'except' => ['store', 'create', 'destroy'],
        ]
    );

    // Admin
    Route::get('admin', 'HomeController@admin')->name('admin');

    // Meta area covers various setting for HMS
    Route::resource('metas', 'MetaController',
        [
            'except' => ['show', 'store', 'create', 'destroy'],
        ]
    );

    // Usefull links
    Route::resource('links', 'LinksController',
        [
            'except' => ['show'],
        ]
    );

    // Label printer template admin
    Route::get('labels/{label}/print', 'LabelTemplateController@showPrint')->name('labels.showPrint');
    Route::post('labels/{label}/print', 'LabelTemplateController@print')->name('labels.print');
    Route::resource('labels', 'LabelTemplateController');

    // Membership
    Route::get('/membership/approval/{user}', 'MembershipController@showDetailsForApproval')->name('membership.approval');
    Route::post('/membership/approve-details/{user}', 'MembershipController@approveDetails')->name('membership.approve');
    Route::post('/membership/reject-details/{user}', 'MembershipController@rejectDetails')->name('membership.reject');
    Route::get('/membership/update-details/{user}', 'MembershipController@editDetails')->name('membership.edit');
    Route::put('/membership/update-details/{user}', 'MembershipController@updateDetails')->name('membership.update');

    // Members Projects and DNH labels
    Route::patch('projects/{project}/markActive', 'Members\ProjectController@markActive')->name('projects.markActive');
    Route::patch('projects/{project}/markAbandoned', 'Members\ProjectController@markAbandoned')->name('projects.markAbandoned');
    Route::patch('projects/{project}/markComplete', 'Members\ProjectController@markComplete')->name('projects.markComplete');
    Route::get('projects/{project}/print', 'Members\ProjectController@printLabel')->name('projects.print');
    Route::resource('projects', 'Members\ProjectController',
        [
            'except' => ['destroy'],
        ]
    );

    // Members Boxes and labels
    Route::get('users/{user}/boxes', 'Members\BoxController@index')->name('user.boxes');
    Route::patch('boxes/{box}/markInUse', 'Members\BoxController@markInUse')->name('boxes.markInUse');
    Route::patch('boxes/{box}/markAbandoned', 'Members\BoxController@markAbandoned')->name('boxes.markAbandoned');
    Route::patch('boxes/{box}/markRemoved', 'Members\BoxController@markRemoved')->name('boxes.markRemoved');
    Route::get('boxes/{box}/print', 'Members\BoxController@printLabel')->name('boxes.print');
    Route::get('boxes/buy', 'Members\BoxController@buy')->name('boxes.buy');
    Route::get('boxes/issue', 'Members\BoxController@issue')->name('boxes.issue');
    Route::resource('boxes', 'Members\BoxController',
        [
            'except' => ['show', 'create', 'edit', 'update', 'destroy'],
        ]
    );

    // Snackspace
    Route::get('users/{user}/snackspace/transactions', 'Snackspace\TransactionsController@index')->name('users.snackspace.transactions');
    Route::resource('snackspace/transactions', 'Snackspace\TransactionsController',
        [
            'except' => ['show', 'store', 'create', 'edit', 'update', 'destroy'],
        ]
    );
});
