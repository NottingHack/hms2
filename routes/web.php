<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Auth::routes(['register' => false, 'verify' => true]);
Route::get('register/{token}', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Static Pages
Route::view('credits', 'pages.credits')->name('credits');
Route::view('company-information', 'pages.companyInformation')->name('companyInformation');
Route::view('contact-us', 'pages.contactUs')->name('contactUs');
Route::view('privacy-and-terms', 'pages.privacy_and_terms')->name('privacy-and-terms');
Route::view('cookie-policy', 'pages.cookie_policy')->name('cookie-policy');
Route::view('donate', 'pages.donate')->name('donate');

// Unrestricted pages
Route::get('links', 'LinksController@index')->name('links.index');
// Instrumentation/Electric
Route::prefix('instrumentation')->namespace('Instrumentation')->name('instrumentation.')->group(function () {
    Route::get('status', 'ServiceController@status')
        ->name('status');
    Route::get('{service}/events/', 'ServiceController@eventsForService')
        ->name('service.events');
    Route::get('electric', 'ElectricController@index')->name('electric.index');
});

Route::prefix('statistics')->name('statistics.')->group(function () {
    Route::get('box-usage', 'StatisticsController@boxUsage')->name('box-usage');
    Route::get('laser-usage', 'StatisticsController@laserUsage')->name('laser-usage');
    Route::get('membership', 'StatisticsController@memberStats')->name('membership');
    Route::get('snackspace-monthly', 'StatisticsController@snackspaceMonthly')->name('snackspace-monthly');
    Route::get('zone-occupants', 'StatisticsController@zoneOccupancy')->name('zone-occupants');
    Route::get('tools', 'StatisticsController@tools')->name('tools');
});

Route::get('gatekeeper/b/{building}/u/{user}/have-left', 'Gatekeeper\AccessController@haveLeft')
    ->name('gatekeeper.building.user.have-left')
    ->middleware('signed');

// Routes in the following group can only be access from inside the hackspace (as defined by the ip range in .env)
Route::middleware(['ipcheck', 'throttle:6,1'])->group(function () {
    Route::get('register-interest', 'RegisterInterestController@index')->name('registerInterest');
    Route::post('register-interest', 'RegisterInterestController@registerInterest');
});

// Routes in the following group can only be access once logged-in
Route::middleware(['auth'])->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::view('registration-complete', 'pages.registrationComplete')->name('registrationComplete');

    // New users should be able to update details with out a verifed email
    Route::get('membership/update-details/{user}', 'MembershipController@editDetails')->name('membership.edit');
    Route::put('membership/update-details/{user}', 'MembershipController@updateDetails')->name('membership.update');

    // Users (show, edit, update) to allow users to update there email if they can't verify it
    Route::resource('users', 'UserController')
        ->except(['index', 'store', 'create', 'destroy']);
});

// Routes in the following group can only be access once logged-in and have verified your email address
Route::middleware(['auth', 'verified'])->group(function () {
    // ROLE
    Route::get('roles', 'RoleController@index')->name('roles.index');
    Route::get('roles/{role}', 'RoleController@show')->name('roles.show');
    Route::get('roles/{role}/edit', 'RoleController@edit')->name('roles.edit');
    Route::put('roles/{role}', 'RoleController@update')->name('roles.update');
    Route::patch('roles/{role}/users', 'RoleController@addUser')->name('roles.addUser');
    Route::delete('roles/{role}/users/{user}', 'RoleController@removeUser')->name('roles.removeUser');
    Route::get('admin/users/{user}/role-updates', 'RoleController@roleUpdates')
        ->name('users.admin.role-updates');
    Route::patch('admin/users/{user}/reinstate', 'RoleController@reinstateUser')
        ->name('users.admin.reinstate');
    Route::patch('admin/users/{user}/temporary-ban', 'RoleController@temporaryBanUser')
        ->name('users.admin.temporaryBan');
    Route::patch('admin/users/{user}/ban', 'RoleController@banUser')
        ->name('users.admin.ban');

    // USER (admin access only)
    Route::get('admin/users/{user}', 'AdminController@userOverview')->name('users.admin.show');
    Route::get('admin/users/{user}/edit', 'UserController@editAdmin')->name('users.admin.edit');
    Route::get('admin/users/{user}/edit-email', 'UserController@editEmail')->name('users.admin.edit-email');
    Route::patch('admin/users/{user}', 'UserController@updateEmail')->name('users.admin.update-email');
    Route::get('users-by-role/{role}', 'UserController@listUsersByRole')->name('users.byRole');

    // USER
    Route::get('users', 'UserController@index')->name('users.index');
    Route::get('change-password', 'Auth\ChangePasswordController@edit')->name('users.changePassword');
    Route::put('change-password', 'Auth\ChangePasswordController@update')->name('users.changePassword.update');

    // Meta area covers various setting for HMS
    Route::resource('metas', 'MetaController')
        ->except(['show', 'store', 'create', 'destroy']);
    Route::resource('content-blocks', 'ContentBlockController')
        ->except(['store', 'create', 'destroy'])
        ->parameters(['content-blocks' => 'contentBlock']);

    // Usefull links editing (index is no auth)
    Route::resource('links', 'LinksController')
        ->except(['index', 'show']); // index is done above outside auth

    // Rfid Tags
    Route::get('users/{user}/rfid-tags', 'Gatekeeper\RfidTagsController@index')->name('users.rfid-tags');
    Route::resource('rfid-tags', 'Gatekeeper\RfidTagsController')
        ->except(['create', 'store', 'show'])
        ->parameters(['rfid-tags' => 'rfidTag']);
    Route::patch('pins/{pin}/reactivate', 'Gatekeeper\RfidTagsController@reactivatePin')->name('pins.reactivate');

    // Access Logs
    Route::get('admin/users/{user}/access-logs', 'Gatekeeper\AccessLogController@indexByUser')
        ->name('users.admin.access-logs');
    Route::get('access-logs/{fromdate?}', 'Gatekeeper\AccessLogController@index')->name('access-logs.index');

    // Gatekeeper access
    Route::prefix('gatekeeper')->namespace('Gatekeeper')->name('gatekeeper.')->group(function () {
        // For members
        Route::get('space-access', 'AccessController@spaceAccess')->name('accessCodes');

        // For admins
        Route::get('access-state', 'AccessController@index')->name('access-state.index');
        Route::get('access-state/edit', 'AccessController@edit')->name('access-state.edit');
        Route::put('access-state', 'AccessController@update')->name('access-state.update');

        Route::resource('bookable-area', 'BookableAreaController')
            ->parameters(['bookable-area' => 'bookableArea']);

        Route::get('temporary-access', 'AccessController@accessCalendar')->name('temporary-access');
    });

    // Label printer template admin
    Route::get('labels/{label}/print', 'LabelTemplateController@showPrint')->name('labels.showPrint');
    Route::post('labels/{label}/print', 'LabelTemplateController@print')->name('labels.print');
    Route::resource('labels', 'LabelTemplateController');

    // Membership
    Route::get('membership', 'MembershipController@index')->name('membership.index');
    Route::get('membership/approval/{user}', 'MembershipController@showDetailsForApproval')
        ->name('membership.approval');
    Route::post('membership/approve-details/{user}', 'MembershipController@approveDetails')
        ->name('membership.approve');
    Route::post('membership/reject-details/{user}', 'MembershipController@rejectDetails')
        ->name('membership.reject');
    Route::view('membership/invites', 'pages.invite_search')
        ->middleware('can:search.invites')
        ->name('membership.invites');
    Route::post('membership/invites/{invite}', 'MembershipController@invitesResend')->name('membership.invites.resend');

    // Members Projects and DNH labels
    Route::get('users/{user}/projects', 'Members\ProjectController@index')->name('users.projects');
    Route::patch('projects/{project}/markActive', 'Members\ProjectController@markActive')->name('projects.markActive');
    Route::patch('projects/{project}/markAbandoned', 'Members\ProjectController@markAbandoned')
        ->name('projects.markAbandoned');
    Route::patch('projects/{project}/markComplete', 'Members\ProjectController@markComplete')
        ->name('projects.markComplete');
    Route::get('projects/{project}/print', 'Members\ProjectController@printLabel')->name('projects.print');
    Route::resource('projects', 'Members\ProjectController')
        ->except(['destroy']);
    // hms1 link
    Route::get('memberProjects/view/{project}', 'Members\ProjectController@show');

    // Members Boxes and labels
    Route::get('boxes/audit', 'Members\BoxController@audit')->name('boxes.audit');
    Route::get('users/{user}/boxes', 'Members\BoxController@index')->name('users.boxes');
    Route::get('users/{user}/boxes/issue', 'Members\BoxController@issue')->name('users.boxes.issue');
    Route::patch('boxes/{box}/markInUse', 'Members\BoxController@markInUse')->name('boxes.markInUse');
    Route::patch('boxes/{box}/markAbandoned', 'Members\BoxController@markAbandoned')->name('boxes.markAbandoned');
    Route::patch('boxes/{box}/markRemoved', 'Members\BoxController@markRemoved')->name('boxes.markRemoved');
    Route::get('boxes/{box}/print', 'Members\BoxController@printLabel')->name('boxes.print');
    Route::resource('boxes', 'Members\BoxController')
        ->except(['edit', 'update', 'destroy']);
    // hms1 link
    Route::get('memberBoxes/view/{box}', 'Members\BoxController@show');

    // Banking
    Route::namespace('Banking')->name('banking.')->group(function () {
        // Accounts
        Route::get('accounts/list-joint', 'AccountController@listJoint')
            ->name('accounts.listJoint');
        Route::get('accounts/{account}', 'AccountController@show')
            ->name('accounts.show');
        Route::patch('accounts/{account}/link-user/', 'AccountController@linkUser')
            ->name('accounts.linkUser');
        Route::patch('accounts/{account}/unlink-user/', 'AccountController@unlinkUser')
            ->name('accounts.unlinkUser');

        Route::resource('accounts.bank-transactions', 'Account\AccountBankTransactionController')
            ->only(['create', 'store'])
            ->parameters(['bank-transactions' => 'bankTransaction'])
            ->shallow();

        // Bank
        Route::resource('banks', 'BankController')
            ->except(['destroy']);

        // Bank Transactions;
        Route::get('banks/{bank}/bank-transactions/ofx-upload', 'Bank\BankBankTransactionController@createViaOfxUpload')
            ->name('banks.bank-transactions.ofx-upload');
        Route::post('banks/{bank}/bank-transactions/ofx-upload', 'Bank\BankBankTransactionController@storeOfx')
            ->name('banks.bank-transactions.store-ofx');
        Route::resource('banks.bank-transactions', 'Bank\BankBankTransactionController')
            ->only(['create', 'store'])
            ->parameters(['bank-transactions' => 'bankTransaction'])
            ->shallow();
        Route::get('bank-transactions/unmatched', 'BankTransactionController@listUnmatched')
            ->name('bank-transactions.unmatched');
        Route::get('bank-transactions/{bankTransaction}/reconcile', 'BankTransactionController@reconcile')
            ->name('bank-transactions.reconcile');
        Route::patch('bank-transactions/{bankTransaction}/match', 'BankTransactionController@match')
            ->name('bank-transactions.match');
        Route::resource('bank-transactions', 'BankTransactionController')
            ->except(['show', 'create', 'store', 'destroy'])
            ->parameters(['bank-transactions' => 'bankTransaction']);
    });

    // Snackspace
    Route::namespace('Snackspace')->group(function () {
        Route::get('users/{user}/snackspace/transactions', 'TransactionsController@index')
            ->name('users.snackspace.transactions');
        Route::get('users/{user}/snackspace/transactions/create', 'TransactionsController@create')
            ->name('users.snackspace.transactions.create');
        Route::post('users/{user}/snackspace/transactions', 'TransactionsController@store')
            ->name('users.snackspace.transactions.store');

        Route::prefix('snackspace')->name('snackspace.')->group(function () {
            Route::get('transactions', 'TransactionsController@index')
                ->name('transactions.index');

            // Snackspace Vending Machine
            Route::resource('products', 'ProductController')
                ->except(['destroy']);
            Route::get('vending-machines/{vendingMachine}/locations', 'VendingMachineController@locations')
                ->name('vending-machines.locations.index');
            Route::patch(
                'vending-machines/{vendingMachine}/locations/{vendingLocation}',
                'VendingMachineController@locationAssign'
            )->name('vending-machines.locations.assign');
            Route::get('vending-machines/{vendingMachine}/logs', 'VendingMachineController@showLogs')
                ->name('vending-machines.logs.show');
            Route::get('vending-machines/{vendingMachine}/logs/jams', 'VendingMachineController@showJams')
                ->name('vending-machines.logs.jams');
            Route::patch(
                'vending-machines/{vendingMachine}/logs/{vendLog}/reconcile',
                'VendingMachineController@reconcile'
            )->name('vending-machines.logs.reconcile');
            Route::resource('vending-machines', 'VendingMachineController')
                ->except(['create', 'store', 'destroy'])
                ->parameters(['vending-machines' => 'vendingMachine']);

            Route::get('debt-graph', 'DebtController@debtGraph')->name('debt-graph');
            Route::get('payment-report', 'PurchasePaymentController@paymentReport')->name('payment-report');
        });
    });

    // Tools
    Route::get('tools/{tool}/users/{grantType}', 'Tools\ToolController@showUsersForGrant')
        ->name('tools.users-for-grant');
    Route::patch('tools/{tool}/grant', 'Tools\ToolController@grant')->name('tools.grant');
    Route::delete('tools/{tool}/revoke/{grantType}/users/{user}', 'Tools\ToolController@revoke')
        ->name('tools.revoke.users');
    Route::post('tools/{tool}/free-time', 'Tools\ToolController@addFreeTime')->name('tools.add-free-time');
    Route::resource('tools', 'Tools\ToolController');
    Route::resource('tools.bookings', 'Tools\BookingController')
        ->only(['index'])
        ->shallow();

    // Teams
    Route::patch('teams/{role}/users', 'RoleController@addUserToTeam')->name('roles.addUserToTeam');
    Route::delete('teams/{role}/users/{user}', 'RoleController@removeUserFromTeam')->name('roles.removeUserFromTeam');
    Route::get('teams/how-to-join', 'TeamController@howToJoin')->name('teams.how-to-join');
    Route::resource('teams', 'TeamController')
        ->except(['destroy']);

    // Email to all Members
    Route::get('email-members', 'EmailController@draft')->name('email-members.draft');
    Route::post('email-members', 'EmailController@review')->name('email-members.review');
    Route::post('email-members/forget', 'EmailController@forget')->name('email-members.forget');
    Route::get('email-members/review', 'EmailController@reviewHtml')->name('email-members.preview');
    Route::put('email-members', 'EmailController@send')->name('email-members.send');

    // CSV downlaods
    Route::get('csv-download', 'CSVDownloadController@index')->name('csv-download.index');
    Route::get('csv-download/current-members', 'CSVDownloadController@currentMembers')->name('csv-download.current-members');
    Route::get('csv-download/opa-csv', 'CSVDownloadController@currentMemberEmails')->name('csv-download.opa-csv');
    Route::get('csv-download/low-payers', 'CSVDownloadController@lowPayers')->name('csv-download.low-payers');
    Route::get('csv-download/payment-change', 'CSVDownloadController@paymentChange')
        ->name('csv-download.payment-change');
    Route::get('csv-download/member-payments', 'CSVDownloadController@memberPayments')
        ->name('csv-download.member-payments');
    Route::get('csv-download/member-boxes', 'CSVDownloadController@memberBoxes')
        ->name('csv-download.member-boxes');

    // Instrumentation/Electric
    Route::prefix('instrumentation')->namespace('Instrumentation')->name('instrumentation.')->group(function () {
        // Route::get('electric', 'ElectricController@index')->name('electric.index');
        Route::post('electric/readings', 'ElectricController@store')->name('electric.readings.store');
    });

    // Governance
    Route::prefix('governance')->namespace('Governance')->name('governance.')->group(function () {
        // Meetings
        Route::get('meetings/{meeting}/attendees', 'MeetingController@attendees')
            ->name('meetings.attendees');
        Route::get('meetings/{meeting}/check-in', 'MeetingController@checkIn')
            ->name('meetings.check-in');
        Route::post('meetings/{meeting}/check-in', 'MeetingController@checkInUser')
            ->name('meetings.check-in-user');

        Route::get('meetings/{meeting}/absentees', 'MeetingController@absentees')
            ->name('meetings.absentees');
        Route::get('meetings/{meeting}/absence', 'MeetingController@absence')
            ->name('meetings.absence');
        Route::post('meetings/{meeting}/absence', 'MeetingController@recordAbsence')
            ->name('meetings.absence-record');
        Route::resource('meetings', 'MeetingController')
            ->except(['destroy']);

        // Proxies
        Route::get('meetings/{meeting}/proxies', 'ProxyController@index')
            ->name('proxies.index');
        Route::get('meetings/{meeting}/principles', 'ProxyController@indexForUser')
            ->name('proxies.index-for-user');
        Route::get('meetings/{meeting}/proxy', 'ProxyController@designateLink')
            ->name('proxies.link');
        Route::get('m/{meeting}/p/d/{principal}', 'ProxyController@designate')
            ->name('proxies.designate')
            ->middleware('signed');
        Route::post('meetings/{meeting}/proxy', 'ProxyController@store')
            ->name('proxies.store');
        Route::delete('meetings/{meeting}/proxy', 'ProxyController@destroy')
            ->name('proxies.destroy');

        // Voting
        Route::get('voting', 'VotingController@index')->name('voting.index');
        Route::patch('voting', 'VotingController@update')->name('voting.update');
    });
});
