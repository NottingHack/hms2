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

Route::get('/', 'HomeController@welcome')->name('index');

// Auth Routes
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::get('register/{token}', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

// Static Pages
Route::view('credits', 'pages.credits')->name('credits');
Route::view('company-information', 'pages.companyInformation')->name('companyInformation');
Route::view('contact-us', 'pages.contactUs')->name('contactUs');

// Unrestricted pages
Route::get('links', 'LinksController@index')->name('links.index');
Route::get('instrumentation/status', 'Instrumentation\ServiceController@status')
    ->name('instrumentation.status');
Route::get('instrumentation/{service}/events/', 'Instrumentation\ServiceController@eventsForService')
    ->name('instrumentation.service.events');

// Routes in the following group can only be access from inside the hackspace (as defined by the ip range in .env)
Route::middleware(['ipcheck'])->group(function () {
    Route::get('register-interest', 'RegisterInterestController@index')->name('registerInterest');
    Route::post('register-interest', 'RegisterInterestController@registerInterest');
});

// Routes in the following group can only be access once logged-in
Route::middleware(['auth'])->group(function () {
    Route::view('registration-complete', 'pages.registrationComplete')->name('registrationComplete');

    // Users (show, edit, update) to allow users to update there email if they can't verify it
    Route::resource(
        'users',
        'UserController',
        [
            'except' => ['index', 'store', 'create', 'destroy'],
        ]
    );
});

// Routes in the following group can only be access once logged-in and have verified your email address
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('access-codes', 'HomeController@accessCodes')->name('accessCodes');

    // ROLE
    Route::get('roles', 'RoleController@index')->name('roles.index');
    Route::get('roles/{role}', 'RoleController@show')->name('roles.show');
    Route::get('roles/{role}/edit', 'RoleController@edit')->name('roles.edit');
    Route::put('roles/{role}', 'RoleController@update')->name('roles.update');
    Route::delete('roles/{role}/users/{user}', 'RoleController@removeUser')->name('roles.removeUser');

    // USER
    Route::get('admin/users/{user}', 'AdminController@userOverview')->name('users.admin.show');
    Route::get('admin/users/{user}/edit', 'UserController@editAdmin')->name('users.admin.edit');
    Route::get('admin/users/{user}/edit_email', 'UserController@editEmail')->name('users.admin.edit-email');
    Route::patch('admin/users/{user}', 'UserController@updateEmail')->name('users.admin.update-email');
    Route::get('users-by-role/{role}', 'UserController@listUsersByRole')->name('users.byRole');
    Route::get('users', 'UserController@index')->name('users.index');
    Route::get('change-password', 'Auth\ChangePasswordController@edit')->name('users.changePassword');
    Route::put('change-password', 'Auth\ChangePasswordController@update')->name('users.changePassword.update');

    // Admin
    Route::get('admin', 'AdminController@admin')->name('admin');
    Route::view('admin/invites', 'pages.invite_search')
        ->middleware('can:search.invites')
        ->name('admin.invites');
    Route::post('admin/invites/{invite}', 'AdminController@invitesResend')->name('admin.invites.resend');

    // Meta area covers various setting for HMS
    Route::resource(
        'metas',
        'MetaController',
        [
            'except' => ['show', 'store', 'create', 'destroy'],
        ]
    );

    // Usefull links editing (index is no auth)
    Route::resource(
        'links',
        'LinksController',
        [
            'except' => ['index', 'show'], // index is done above outside auth
        ]
    );

    // Rfid Tags
    Route::get('users/{user}/rfid-tags', 'GateKeeper\RfidTagsController@index')->name('users.rfid-tags');
    Route::resource(
        'rfid-tags',
        'GateKeeper\RfidTagsController',
        [
            'except' => ['create', 'store', 'show'],
            'parameters' => [
                'rfid-tags' => 'rfidTag',
            ],
        ]
    );
    Route::patch('pins/{pin}/reactivate', 'GateKeeper\RfidTagsController@reactivatePin')->name('pins.reactivate');

    // Label printer template admin
    Route::get('labels/{label}/print', 'LabelTemplateController@showPrint')->name('labels.showPrint');
    Route::post('labels/{label}/print', 'LabelTemplateController@print')->name('labels.print');
    Route::resource('labels', 'LabelTemplateController');

    // Membership
    Route::get('membership/approval/{user}', 'MembershipController@showDetailsForApproval')
        ->name('membership.approval');
    Route::post('membership/approve-details/{user}', 'MembershipController@approveDetails')
        ->name('membership.approve');
    Route::post('membership/reject-details/{user}', 'MembershipController@rejectDetails')
        ->name('membership.reject');
    Route::get('membership/update-details/{user}', 'MembershipController@editDetails')->name('membership.edit');
    Route::put('membership/update-details/{user}', 'MembershipController@updateDetails')->name('membership.update');

    // Members Projects and DNH labels
    Route::get('users/{user}/projects', 'Members\ProjectController@index')->name('users.projects');
    Route::patch('projects/{project}/markActive', 'Members\ProjectController@markActive')->name('projects.markActive');
    Route::patch('projects/{project}/markAbandoned', 'Members\ProjectController@markAbandoned')
        ->name('projects.markAbandoned');
    Route::patch('projects/{project}/markComplete', 'Members\ProjectController@markComplete')
        ->name('projects.markComplete');
    Route::get('projects/{project}/print', 'Members\ProjectController@printLabel')->name('projects.print');
    Route::resource(
        'projects',
        'Members\ProjectController',
        [
            'except' => ['destroy'],
        ]
    );
    // hms1 link
    Route::get('memberProjects/view/{project}', 'Members\ProjectController@show');

    // Members Boxes and labels
    Route::get('users/{user}/boxes', 'Members\BoxController@index')->name('users.boxes');
    Route::get('users/{user}/boxes/issue', 'Members\BoxController@issue')->name('users.boxes.issue');
    Route::patch('boxes/{box}/markInUse', 'Members\BoxController@markInUse')->name('boxes.markInUse');
    Route::patch('boxes/{box}/markAbandoned', 'Members\BoxController@markAbandoned')->name('boxes.markAbandoned');
    Route::patch('boxes/{box}/markRemoved', 'Members\BoxController@markRemoved')->name('boxes.markRemoved');
    Route::get('boxes/{box}/print', 'Members\BoxController@printLabel')->name('boxes.print');
    Route::resource(
        'boxes',
        'Members\BoxController',
        [
            'except' => ['edit', 'update', 'destroy'],
        ]
    );
    // hms1 link
    Route::get('memberBoxes/view/{box}', 'Members\BoxController@show');

    // Accounts
    Route::get('accounts/list-joint', 'Banking\AccountController@listJoint')->name('banking.accounts.listJoint');
    Route::get('accounts/{account}', 'Banking\AccountController@show')->name('banking.accounts.show');
    Route::patch('accounts/{account}/link-user/', 'Banking\AccountController@linkUser')
        ->name('banking.accounts.linkUser');
    Route::patch('accounts/{account}/unlink-user/', 'Banking\AccountController@unlinkUser')
        ->name('banking.accounts.unlinkUser');

    // Bank Transactions
    Route::get('bank-transactions/unmatched', 'Banking\BankTransactionsController@listUnmatched')
        ->name('bank-transactions.unmatched');
    Route::get('users/{user}/bank-transactions', 'Banking\BankTransactionsController@index')
        ->name('users.bank-transactions');
    Route::resource(
        'bank-transactions',
        'Banking\BankTransactionsController',
        [
            'except' => ['show', 'create', 'store', 'destroy'],
            'parameters' => [
                'bank-transactions' => 'bankTransaction',
            ],
        ]
    );

    // Snackspace
    Route::namespace('Snackspace')->group(function () {
        Route::get(
            'users/{user}/snackspace/transactions',
            'TransactionsController@index'
        )->name('users.snackspace.transactions');
        Route::get(
            'users/{user}/snackspace/transactions/create',
            'TransactionsController@create'
        )->name('users.snackspace.transactions.create');
        Route::post(
            'users/{user}/snackspace/transactions',
            'TransactionsController@store'
        )->name('users.snackspace.transactions.store');

        Route::prefix('snackspace')->name('snackspace.')->group(function () {
            Route::get(
                'transactions',
                'TransactionsController@index'
            )->name('transactions.index');

            // Snackspace Vending Machine
            Route::resource(
                'products',
                'ProductController',
                [
                    'except' => ['destroy'],
                ]
            );
            Route::resource(
                'vending-machines',
                'VendingMachineController',
                [
                    'except' => ['create', 'store', 'destroy'],
                    'parameters' => [
                        'vending-machines' => 'vendingMachine',
                    ],
                ]
            );
            Route::get(
                'vending-machines/{vendingMachine}/locations',
                'VendingMachineController@locations'
            )->name('vending-machines.locations.index');
            Route::patch(
                'vending-machines/{vendingMachine}/locations/{vendingLocation}',
                'VendingMachineController@locationAssign'
            )->name('vending-machines.locations.assign');
        });
    });

    // Tools
    Route::resource('tools', 'Tools\ToolController');
    Route::resource(
        'tools/{tool}/bookings',
        'Tools\BookingController',
        [
            'except' => ['show', 'create', 'store', 'edit', 'update', 'destroy'], // turned off for now
        ]
    );

    // Teams
    Route::patch('teams/{role}/users', 'RoleController@addUsertoTeam')->name('roles.addUsertoTeam');
    Route::get('teams/how-to-join', 'TeamController@howToJoin')->name('teams.how-to-join');
    Route::resource(
        'teams',
        'TeamController',
        [
            'except' => ['create', 'store', 'destroy'],
        ]
    );
});
