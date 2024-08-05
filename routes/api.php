<?php

use App\Http\Controllers\Api\Auth\CanCheckController;
use App\Http\Controllers\Api\Auth\RfidAccessTokenController;
use App\Http\Controllers\Api\Banking\StripeController;
use App\Http\Controllers\Api\Banking\TransactionUploadController;
use App\Http\Controllers\Api\Gatekeeper\BuildingController;
use App\Http\Controllers\Api\Gatekeeper\RegisterRfidTagController;
use App\Http\Controllers\Api\Gatekeeper\TemporaryAccessBookingController;
use App\Http\Controllers\Api\Governance\CheckInController;
use App\Http\Controllers\Api\Members\BoxController;
use App\Http\Controllers\Api\Members\ProjectController;
use App\Http\Controllers\Api\MwAuthHmsController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\Snackspace\VendingMachineController;
use App\Http\Controllers\Api\SpaceApiController;
use App\Http\Controllers\Api\Tools\BookingController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
 * All urls should be hyphenated
 */

// All api route names are prefixed with api.
Route::name('api.')->group(function () {
    Route::get('spaceapi', SpaceApiController::class)->name('spaceapi');
    Route::post('mw-auth-hms', MwAuthHmsController::class)->name('mw-auth-hms');

    // Stripe (not auth restricted)
    Route::post('stripe/intent/makeGuest', [StripeController::class, 'makeIntent'])
        ->name('stripe.make-intent.anon');
    Route::post('stripe/intent/updateGuest', [StripeController::class, 'updateIntent'])
        ->name('stripe.update-intent.anon');
    Route::post('stripe/intent/successGuest', [StripeController::class, 'intentSuccess'])
        ->name('stripe.intent-success.anon');

    Route::middleware('auth:api')->group(function () {
        // Stripe
        Route::post('stripe/intent/make', [StripeController::class, 'makeIntent'])
            ->name('stripe.make-intent');
        Route::post('stripe/intent/update', [StripeController::class, 'updateIntent'])
            ->name('stripe.update-intent');
        Route::post('stripe/intent/success', [StripeController::class, 'intentSuccess'])
            ->name('stripe.intent-success');

        // Search for members
        // api/search/users/matt                    Search term as part of the
        // api/search/users?q=matt                  Search term as a parameter
        // api/search/users?q=matt&withAccount=true Only search for members with Accounts
        Route::get('search/users/{searchQuery?}', [SearchController::class, 'users'])
            ->name('search.users');

        Route::get('search/invites/{searchQuery?}', [SearchController::class, 'invites'])
            ->name('search.invites');

        // Users
        Route::post('can', CanCheckController::class)
            ->name('user.can');
        Route::get('userinfo', [UserController::class, 'showOpenId'])
            ->name('users.show.open-id');
        Route::apiResource('users', UserController::class)
            ->except(['store', 'destroy']);

        // Snackspace
        Route::patch(
            'snackspace/vending-machines/{vendingMachine}/locations',
            [VendingMachineController::class, 'locationAssign']
        )->name('snackspace.vending-machines.locations.assign');

        // Tools
        Route::apiResource('tools.bookings', BookingController::class);

        // Gatekeeper
        Route::prefix('gatekeeper')->name('gatekeeper.')->group(function () {
            Route::get('buildings', [BuildingController::class, 'index'])
                ->name('buildings.index');
            Route::get('buildings/{building}', [BuildingController::class, 'show'])
                ->name('buildings.show');
            Route::patch('buildings/{building}/occupancy', [BuildingController::class, 'updateOccupancy'])
                ->name('buildings.update-occupancy');
            Route::patch('buildings/{building}/access-state', [BuildingController::class, 'updateAccessState'])
                ->name('buildings.update-access-state');

            // Temporary Access
            Route::apiResource('temporary-access-bookings', TemporaryAccessBookingController::class)
                ->parameters(['temporary-access-bookings' => 'temporaryAccessBooking']);
        });

        // Governance
        Route::prefix('governance')->name('governance.')->group(function () {
            // Meeting
            Route::get('meetings/{meeting}', [CheckInController::class, 'show'])
                ->middleware('can:governance.meeting.view') // applied here so method can also be use by client route
                ->name('meetings.show');
            Route::post('meetings/{meeting}/check-in', [CheckInController::class, 'checkInUser'])
                ->name('meetings.check-in-user');
        });

        // Members Projects and DNH labels
        Route::patch('projects/{project}/markActive', [ProjectController::class, 'markActive'])
            ->name('projects.markActive');
        Route::patch('projects/{project}/markAbandoned', [ProjectController::class, 'markAbandoned'])
            ->name('projects.markAbandoned');
        Route::patch('projects/{project}/markComplete', [ProjectController::class, 'markComplete'])
            ->name('projects.markComplete');
        Route::post('projects/{project}/print', [ProjectController::class, 'printLabel'])
            ->name('projects.print');
        Route::apiResource('projects', ProjectController::class)
            ->except(['destroy']);

        // Members Boxes and labels
        Route::get('boxes/audit', [BoxController::class, 'audit'])
            ->name('boxes.audit');
        Route::patch('boxes/{box}/markInUse', [BoxController::class, 'markInUse'])
            ->name('boxes.markInUse');
        Route::patch('boxes/{box}/markAbandoned', [BoxController::class, 'markAbandoned'])
            ->name('boxes.markAbandoned');
        Route::patch('boxes/{box}/markRemoved', [BoxController::class, 'markRemoved'])
            ->name('boxes.markRemoved');
        Route::post('boxes/{box}/print', [BoxController::class, 'printLabel'])
            ->name('boxes.print');
        Route::apiResource('boxes', BoxController::class)
            ->except(['store', 'update', 'destroy']);
    });
});

// All 'client_credentials' api route names are prefixed with client.
Route::name('client.')->prefix('cc')->middleware('client')->group(function () {
    // upload new bankTransactions/
    Route::post('bank-transactions/upload', [TransactionUploadController::class, 'upload'])
        ->name('bankTransactions.upload');

    // Governance
    Route::prefix('governance')->name('governance.')->group(function () {
        // Meeting
        Route::get('meetings/next', [CheckInController::class, 'next'])
            ->name('meetings.next');
        Route::get('meetings/{meeting}', [CheckInController::class, 'show'])
            ->name('meetings.show');
        Route::post('meetings/{meeting}/check-in-rfid', [CheckInController::class, 'checkInUserByRFID'])
            ->name('meetings.check-in-rfid');
    });

    Route::post('rfid-token', RfidAccessTokenController::class)
        ->name('rfid-token');

    Route::post('rfid-tags/register', RegisterRfidTagController::class)
        ->name('rfid-tags.register');
});

Route::name('webhook.')->group(function () {
    Route::stripeWebhooks('stripe/webhook')->name('stripe');
});
