<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Banking\Account\AccountBankTransactionController;
use App\Http\Controllers\Banking\AccountController;
use App\Http\Controllers\Banking\Bank\BankBankTransactionController;
use App\Http\Controllers\Banking\BankController;
use App\Http\Controllers\Banking\BankTransactionController;
use App\Http\Controllers\ContentBlockController;
use App\Http\Controllers\CSVDownloadController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Gatekeeper\AccessController;
use App\Http\Controllers\Gatekeeper\AccessLogController;
use App\Http\Controllers\Gatekeeper\BookableAreaController;
use App\Http\Controllers\Gatekeeper\RfidTagsController;
use App\Http\Controllers\Governance\MeetingController;
use App\Http\Controllers\Governance\ProxyController;
use App\Http\Controllers\Governance\VotingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Instrumentation\ElectricController;
use App\Http\Controllers\Instrumentation\ServiceController;
use App\Http\Controllers\LabelTemplateController;
use App\Http\Controllers\LinksController;
use App\Http\Controllers\Members\BoxController;
use App\Http\Controllers\Members\ProjectController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\RegisterInterestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Snackspace\DebtController;
use App\Http\Controllers\Snackspace\ProductController;
use App\Http\Controllers\Snackspace\PurchasePaymentController;
use App\Http\Controllers\Snackspace\TransactionsController;
use App\Http\Controllers\Snackspace\VendingMachineController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\Tools\BookingController;
use App\Http\Controllers\Tools\ToolController;
use App\Http\Controllers\UserController;
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

Route::get('/', [HomeController::class, 'welcome'])->name('index');

// Auth Routes
Auth::routes(['register' => false, 'verify' => true]);
Route::get('register/{token}', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Static Pages
Route::view('credits', 'pages.credits')->name('credits');
Route::view('company-information', 'pages.companyInformation')->name('companyInformation');
Route::view('contact-us', 'pages.contactUs')->name('contactUs');
Route::view('privacy-and-terms', 'pages.privacy_and_terms')->name('privacy-and-terms');
Route::view('cookie-policy', 'pages.cookie_policy')->name('cookie-policy');
Route::view('donate', 'pages.donate')->name('donate');

// Unrestricted pages
Route::get('links', [LinksController::class, 'index'])->name('links.index');
// Instrumentation/Electric
Route::prefix('instrumentation')->name('instrumentation.')->group(function () {
    Route::get('status', [ServiceController::class, 'status'])
        ->name('status');
    Route::get('{service}/events/', [ServiceController::class, 'eventsForService'])
        ->name('service.events');
    Route::get('electric', [ElectricController::class, 'index'])->name('electric.index');
});

Route::prefix('statistics')->name('statistics.')->group(function () {
    Route::get('box-usage', [StatisticsController::class, 'boxUsage'])->name('box-usage');
    Route::get('laser-usage', [StatisticsController::class, 'laserUsage'])->name('laser-usage');
    Route::get('membership', [StatisticsController::class, 'memberStats'])->name('membership');
    Route::get('current-members-graph', [StatisticsController::class, 'membershipGraph'])->name('membership-graph');
    Route::get('snackspace-monthly', [StatisticsController::class, 'snackspaceMonthly'])->name('snackspace-monthly');
    Route::get('zone-occupants', [StatisticsController::class, 'zoneOccupancy'])->name('zone-occupants');
    Route::get('tools', [StatisticsController::class, 'tools'])->name('tools');
});

Route::get('gatekeeper/b/{building}/u/{user}/have-left', [AccessController::class, 'haveLeft'])
    ->name('gatekeeper.building.user.have-left')
    ->middleware('signed');

// Routes in the following group can only be access from inside the hackspace (as defined by the ip range in .env)
Route::middleware(['ipcheck', 'throttle:6,1'])->group(function () {
    Route::get('register-interest', [RegisterInterestController::class, 'index'])->name('registerInterest');
    Route::post('register-interest', [RegisterInterestController::class, 'registerInterest']);
});

// Routes in the following group can only be access once logged-in
Route::middleware(['auth'])->group(function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::view('registration-complete', 'pages.registrationComplete')->name('registrationComplete');

    // New users should be able to update details with out a verifed email
    Route::get('membership/update-details/{user}', [MembershipController::class, 'editDetails'])
        ->name('membership.edit');
    Route::put('membership/update-details/{user}', [MembershipController::class, 'updateDetails'])
        ->name('membership.update');

    // A redirector to go to the currently logged in users edit user page.
    Route::get('profile', [UserController::class, 'editRedirect'])->name('redirector.user.edit');

    // Users (show, edit, update) to allow users to update there email if they can't verify it
    Route::resource('users', UserController::class)
        ->except(['index', 'store', 'create', 'destroy']);
});

// Routes in the following group can only be access once logged-in and have verified your email address
Route::middleware(['auth', 'verified'])->group(function () {
    // ROLE
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::patch('roles/{role}/users', [RoleController::class, 'addUser'])->name('roles.addUser');
    Route::delete('roles/{role}/users/{user}', [RoleController::class, 'removeUser'])->name('roles.removeUser');
    Route::get('admin/users/{user}/role-updates', [RoleController::class, 'roleUpdates'])
        ->name('users.admin.role-updates');
    Route::patch('admin/users/{user}/reinstate', [RoleController::class, 'reinstateUser'])
        ->name('users.admin.reinstate');
    Route::patch('admin/users/{user}/temporary-ban', [RoleController::class, 'temporaryBanUser'])
        ->name('users.admin.temporaryBan');
    Route::patch('admin/users/{user}/ban', [RoleController::class, 'banUser'])
        ->name('users.admin.ban');

    // USER (admin access only)
    Route::get('admin/users/{user}', [AdminController::class, 'userOverview'])->name('users.admin.show');
    Route::get('admin/users/{user}/edit', [UserController::class, 'editAdmin'])->name('users.admin.edit');
    Route::get('admin/users/{user}/edit-email', [UserController::class, 'editEmail'])->name('users.admin.edit-email');
    Route::patch('admin/users/{user}', [UserController::class, 'updateEmail'])->name('users.admin.update-email');
    Route::get('users-by-role/{role}', [UserController::class, 'listUsersByRole'])->name('users.byRole');

    // USER
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('change-password', [ChangePasswordController::class, 'edit'])->name('users.changePassword');
    Route::put('change-password', [ChangePasswordController::class, 'update'])->name('users.changePassword.update');

    // Meta area covers various setting for HMS
    Route::resource('metas', MetaController::class)
        ->except(['show', 'store', 'create', 'destroy']);
    Route::resource('content-blocks', ContentBlockController::class)
        ->except(['store', 'create', 'destroy'])
        ->parameters(['content-blocks' => 'contentBlock']);

    // Usefull links editing (index is no auth)
    Route::resource('links', LinksController::class)
        ->except(['index', 'show']); // index is done above outside auth

    // Rfid Tags
    Route::get('users/{user}/rfid-tags', [RfidTagsController::class, 'index'])->name('users.rfid-tags');
    Route::resource('rfid-tags', RfidTagsController::class)
        ->except(['create', 'store', 'show'])
        ->parameters(['rfid-tags' => 'rfidTag']);
    Route::patch('pins/{pin}/reactivate', [RfidTagsController::class, 'reactivatePin'])->name('pins.reactivate');

    // Access Logs
    Route::get('admin/users/{user}/access-logs', [AccessLogController::class, 'indexByUser'])
        ->name('users.admin.access-logs');
    Route::get('access-logs/{fromdate?}', [AccessLogController::class, 'index'])->name('access-logs.index');

    // Gatekeeper access
    Route::prefix('gatekeeper')->name('gatekeeper.')->group(function () {
        // For members
        Route::get('space-access', [AccessController::class, 'spaceAccess'])->name('accessCodes');

        // For admins
        Route::get('access-state', [AccessController::class, 'index'])->name('access-state.index');
        Route::get('access-state/edit', [AccessController::class, 'edit'])->name('access-state.edit');
        Route::put('access-state', [AccessController::class, 'update'])->name('access-state.update');

        Route::resource('bookable-area', BookableAreaController::class)
            ->parameters(['bookable-area' => 'bookableArea']);

        Route::get('temporary-access', [AccessController::class, 'accessCalendar'])->name('temporary-access');
    });

    // Label printer template admin
    Route::get('labels/{label}/print', [LabelTemplateController::class, 'showPrint'])->name('labels.showPrint');
    Route::post('labels/{label}/print', [LabelTemplateController::class, 'print'])->name('labels.print');
    Route::resource('labels', LabelTemplateController::class);

    // Membership
    Route::get('membership', [MembershipController::class, 'index'])->name('membership.index');
    Route::get('membership/approval/{user}', [MembershipController::class, 'showDetailsForApproval'])
        ->name('membership.approval');
    Route::post('membership/approve-details/{user}', [MembershipController::class, 'approveDetails'])
        ->name('membership.approve');
    Route::post('membership/reject-details/{user}', [MembershipController::class, 'rejectDetails'])
        ->name('membership.reject');
    Route::view('membership/invites', 'pages.invite_search')
        ->middleware('can:search.invites')
        ->name('membership.invites');
    Route::post('membership/invites/{invite}', [MembershipController::class, 'invitesResend'])
        ->name('membership.invites.resend');

    // Members Projects and DNH labels
    Route::get('users/{user}/projects', [ProjectController::class, 'index'])->name('users.projects');
    Route::patch('projects/{project}/markActive', [ProjectController::class, 'markActive'])
        ->name('projects.markActive');
    Route::patch('projects/{project}/markAbandoned', [ProjectController::class, 'markAbandoned'])
        ->name('projects.markAbandoned');
    Route::patch('projects/{project}/markComplete', [ProjectController::class, 'markComplete'])
        ->name('projects.markComplete');
    Route::get('projects/{project}/print', [ProjectController::class, 'printLabel'])->name('projects.print');
    Route::resource('projects', ProjectController::class)
        ->except(['destroy']);
    // hms1 link
    Route::get('memberProjects/view/{project}', [ProjectController::class, 'show']);

    // Members Boxes and labels
    Route::get('boxes/audit', [BoxController::class, 'audit'])->name('boxes.audit');
    Route::get('users/{user}/boxes', [BoxController::class, 'index'])->name('users.boxes');
    Route::get('users/{user}/boxes/issue', [BoxController::class, 'issue'])->name('users.boxes.issue');
    Route::patch('boxes/{box}/markInUse', [BoxController::class, 'markInUse'])->name('boxes.markInUse');
    Route::patch('boxes/{box}/markAbandoned', [BoxController::class, 'markAbandoned'])->name('boxes.markAbandoned');
    Route::patch('boxes/{box}/markRemoved', [BoxController::class, 'markRemoved'])->name('boxes.markRemoved');
    Route::get('boxes/{box}/print', [BoxController::class, 'printLabel'])->name('boxes.print');
    Route::resource('boxes', BoxController::class)
        ->except(['edit', 'update', 'destroy']);
    // hms1 link
    Route::get('memberBoxes/view/{box}', [BoxController::class, 'show']);

    // Banking
    Route::name('banking.')->group(function () {
        // Accounts
        Route::get('accounts/list-joint', [AccountController::class, 'listJoint'])
            ->name('accounts.listJoint');
        Route::get('accounts/{account}', [AccountController::class, 'show'])
            ->name('accounts.show');
        Route::patch('accounts/{account}/link-user/', [AccountController::class, 'linkUser'])
            ->name('accounts.linkUser');
        Route::patch('accounts/{account}/unlink-user/', [AccountController::class, 'unlinkUser'])
            ->name('accounts.unlinkUser');

        Route::resource('accounts.bank-transactions', AccountBankTransactionController::class)
            ->only(['create', 'store'])
            ->parameters(['bank-transactions' => 'bankTransaction'])
            ->shallow();

        // Bank
        Route::resource('banks', BankController::class)
            ->except(['destroy']);

        // Bank Transactions;
        Route::get('banks/{bank}/bank-transactions/ofx-upload', [BankBankTransactionController::class, 'createViaOfxUpload'])
            ->name('banks.bank-transactions.ofx-upload');
        Route::post('banks/{bank}/bank-transactions/ofx-upload', [BankBankTransactionController::class, 'storeOfx'])
            ->name('banks.bank-transactions.store-ofx');
        Route::resource('banks.bank-transactions', BankBankTransactionController::class)
            ->only(['create', 'store'])
            ->parameters(['bank-transactions' => 'bankTransaction'])
            ->shallow();
        Route::get('bank-transactions/unmatched', [BankTransactionController::class, 'listUnmatched'])
            ->name('bank-transactions.unmatched');
        Route::get('bank-transactions/{bankTransaction}/reconcile', [BankTransactionController::class, 'reconcile'])
            ->name('bank-transactions.reconcile');
        Route::patch('bank-transactions/{bankTransaction}/match', [BankTransactionController::class, 'match'])
            ->name('bank-transactions.match');
        Route::resource('bank-transactions', BankTransactionController::class)
            ->except(['show', 'create', 'store', 'destroy'])
            ->parameters(['bank-transactions' => 'bankTransaction']);
    });

    // Snackspace
    Route::get('users/{user}/snackspace/transactions', [TransactionsController::class, 'index'])
        ->name('users.snackspace.transactions');
    Route::get('users/{user}/snackspace/transactions/create', [TransactionsController::class, 'create'])
        ->name('users.snackspace.transactions.create');
    Route::post('users/{user}/snackspace/transactions', [TransactionsController::class, 'store'])
        ->name('users.snackspace.transactions.store');

    Route::prefix('snackspace')->name('snackspace.')->group(function () {
        Route::get('transactions', [TransactionsController::class, 'index'])
            ->name('transactions.index');

        // Snackspace Vending Machine
        Route::resource('products', ProductController::class)
            ->except(['destroy']);
        Route::get('vending-machines/{vendingMachine}/locations', [VendingMachineController::class, 'locations'])
            ->name('vending-machines.locations.index');
        Route::patch(
            'vending-machines/{vendingMachine}/locations/{vendingLocation}',
            [VendingMachineController::class, 'locationAssign']
        )->name('vending-machines.locations.assign');
        Route::get('vending-machines/{vendingMachine}/logs', [VendingMachineController::class, 'showLogs'])
            ->name('vending-machines.logs.show');
        Route::get('vending-machines/{vendingMachine}/logs/jams', [VendingMachineController::class, 'showJams'])
            ->name('vending-machines.logs.jams');
        Route::patch(
            'vending-machines/{vendingMachine}/logs/{vendLog}/reconcile',
            [VendingMachineController::class, 'reconcile']
        )->name('vending-machines.logs.reconcile');
        Route::resource('vending-machines', VendingMachineController::class)
            ->except(['create', 'store', 'destroy'])
            ->parameters(['vending-machines' => 'vendingMachine']);

        Route::get('debt-graph', [DebtController::class, 'debtGraph'])->name('debt-graph');
        Route::get('payment-report', [PurchasePaymentController::class, 'paymentReport'])->name('payment-report');
    });

    // Tools
    Route::get('tools/{tool}/users/{grantType}', [ToolController::class, 'showUsersForGrant'])
        ->name('tools.users-for-grant');
    Route::patch('tools/{tool}/grant', [ToolController::class, 'grant'])->name('tools.grant');
    Route::delete('tools/{tool}/revoke/{grantType}/users/{user}', [ToolController::class, 'revoke'])
        ->name('tools.revoke.users');
    Route::post('tools/{tool}/free-time', [ToolController::class, 'addFreeTime'])->name('tools.add-free-time');
    Route::resource('tools', ToolController::class);
    Route::resource('tools.bookings', BookingController::class)
        ->only(['index'])
        ->shallow();

    // Teams
    Route::patch('teams/{role}/users', [RoleController::class, 'addUserToTeam'])->name('roles.addUserToTeam');
    Route::delete('teams/{role}/users/{user}', [RoleController::class, 'removeUserFromTeam'])
        ->name('roles.removeUserFromTeam');
    Route::get('teams/how-to-join', [TeamController::class, 'howToJoin'])->name('teams.how-to-join');
    Route::resource('teams', TeamController::class)
        ->except(['destroy']);

    // Email to all Members
    Route::get('email-members', [EmailController::class, 'draft'])->name('email-members.draft');
    Route::post('email-members', [EmailController::class, 'review'])->name('email-members.review');
    Route::post('email-members/forget', [EmailController::class, 'forget'])->name('email-members.forget');
    Route::get('email-members/review', [EmailController::class, 'reviewHtml'])->name('email-members.preview');
    Route::put('email-members', [EmailController::class, 'send'])->name('email-members.send');

    // CSV downlaods
    Route::get('csv-download', [CSVDownloadController::class, 'index'])->name('csv-download.index');
    Route::get('csv-download/current-members', [CSVDownloadController::class, 'currentMembers'])
        ->name('csv-download.current-members');
    Route::get('csv-download/opa-csv', [CSVDownloadController::class, 'currentMemberEmails'])
        ->name('csv-download.opa-csv');
    Route::get('csv-download/low-payers', [CSVDownloadController::class, 'lowPayers'])->name('csv-download.low-payers');
    Route::get('csv-download/payment-change', [CSVDownloadController::class, 'paymentChange'])
        ->name('csv-download.payment-change');
    Route::get('csv-download/member-payments', [CSVDownloadController::class, 'memberPayments'])
        ->name('csv-download.member-payments');
    Route::get('csv-download/member-boxes', [CSVDownloadController::class, 'memberBoxes'])
        ->name('csv-download.member-boxes');

    // Instrumentation/Electric
    Route::prefix('instrumentation')->name('instrumentation.')->group(function () {
        // Route::get('electric', [ElectricController::class, 'index'])->name('electric.index');
        Route::post('electric/readings', [ElectricController::class, 'store'])->name('electric.readings.store');
    });

    // Governance
    Route::prefix('governance')->name('governance.')->group(function () {
        // Meetings
        Route::get('meetings/{meeting}/attendees', [MeetingController::class, 'attendees'])
            ->name('meetings.attendees');
        Route::get('meetings/{meeting}/check-in', [MeetingController::class, 'checkIn'])
            ->name('meetings.check-in');
        Route::post('meetings/{meeting}/check-in', [MeetingController::class, 'checkInUser'])
            ->name('meetings.check-in-user');

        Route::get('meetings/{meeting}/absentees', [MeetingController::class, 'absentees'])
            ->name('meetings.absentees');
        Route::get('meetings/{meeting}/absence', [MeetingController::class, 'absence'])
            ->name('meetings.absence');
        Route::post('meetings/{meeting}/absence', [MeetingController::class, 'recordAbsence'])
            ->name('meetings.absence-record');
        Route::resource('meetings', MeetingController::class)
            ->except(['destroy']);

        // Proxies
        Route::get('meetings/{meeting}/proxies', [ProxyController::class, 'index'])
            ->name('proxies.index');
        Route::get('meetings/{meeting}/principles', [ProxyController::class, 'indexForUser'])
            ->name('proxies.index-for-user');
        Route::get('meetings/{meeting}/proxy', [ProxyController::class, 'designateLink'])
            ->name('proxies.link');
        Route::get('m/{meeting}/p/d/{principal}', [ProxyController::class, 'designate'])
            ->name('proxies.designate')
            ->middleware('signed');
        Route::post('meetings/{meeting}/proxy', [ProxyController::class, 'store'])
            ->name('proxies.store');
        Route::delete('meetings/{meeting}/proxy', [ProxyController::class, 'destroy'])
            ->name('proxies.destroy');

        // Voting
        Route::get('voting', [VotingController::class, 'index'])->name('voting.index');
        Route::patch('voting', [VotingController::class, 'update'])->name('voting.update');
    });
});
