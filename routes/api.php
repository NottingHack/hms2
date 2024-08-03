<?php

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
Route::name('api.')->namespace('Api')->group(function () {
    Route::get('spaceapi', 'SpaceApiController')->name('spaceapi');
    Route::post('mw-auth-hms', 'MwAuthHmsController')->name('mw-auth-hms');

    // Stripe (not auth restricted)
    Route::post('stripe/intent/makeGuest', 'Banking\StripeController@makeIntent')
        ->name('stripe.make-intent.anon');
    Route::post('stripe/intent/updateGuest', 'Banking\StripeController@updateIntent')
        ->name('stripe.update-intent.anon');
    Route::post('stripe/intent/successGuest', 'Banking\StripeController@intentSuccess')
        ->name('stripe.intent-success.anon');

    Route::middleware('auth:api')->group(function () {
        // Stripe
        Route::post('stripe/intent/make', 'Banking\StripeController@makeIntent')
            ->name('stripe.make-intent');
        Route::post('stripe/intent/update', 'Banking\StripeController@updateIntent')
            ->name('stripe.update-intent');
        Route::post('stripe/intent/success', 'Banking\StripeController@intentSuccess')
            ->name('stripe.intent-success');

        // Search for members
        // api/search/users/matt                    Search term as part of the
        // api/search/users?q=matt                  Search term as a parameter
        // api/search/users?q=matt&withAccount=true Only search for members with Accounts
        Route::get('search/users/{searchQuery?}', 'SearchController@users')
            ->name('search.users');

        Route::get('search/invites/{searchQuery?}', 'SearchController@invites')
            ->name('search.invites');

        // Users
        Route::post('can', 'Auth\CanCheckController')
            ->name('user.can');
        Route::get('userinfo', 'UserController@showOpenId')
            ->name('users.show.open-id');
        Route::apiResource('users', 'UserController')
            ->except(['store', 'destroy']);

        // Snackspace
        Route::patch(
            'snackspace/vending-machines/{vendingMachine}/locations',
            'Snackspace\VendingMachineController@locationAssign'
        )->name('snackspace.vending-machines.locations.assign');

        // Tools
        Route::apiResource('tools.bookings', 'Tools\BookingController');

        // Gatekeeper
        Route::prefix('gatekeeper')->namespace('Gatekeeper')->name('gatekeeper.')->group(function () {
            Route::get('buildings', 'BuildingController@index')
                ->name('buildings.index');
            Route::get('buildings/{building}', 'BuildingController@show')
                ->name('buildings.show');
            Route::patch('buildings/{building}/occupancy', 'BuildingController@updateOccupancy')
                ->name('buildings.update-occupancy');
            Route::patch('buildings/{building}/access-state', 'BuildingController@updateAccessState')
                ->name('buildings.update-access-state');

            // Temporary Access
            Route::apiResource('temporary-access-bookings', 'TemporaryAccessBookingController')
                ->parameters(['temporary-access-bookings' => 'temporaryAccessBooking']);
        });

        // Governance
        Route::prefix('governance')->namespace('Governance')->name('governance.')->group(function () {
            // Meeting
            Route::get('meetings/{meeting}', 'CheckInController@show')
                ->middleware('can:governance.meeting.view') // applied here so method can also be use by client route
                ->name('meetings.show');
            Route::post('meetings/{meeting}/check-in', 'CheckInController@checkInUser')
                ->name('meetings.check-in-user');
        });

        // Members Projects and DNH labels
        Route::patch('projects/{project}/markActive', 'Members\ProjectController@markActive')
            ->name('projects.markActive');
        Route::patch('projects/{project}/markAbandoned', 'Members\ProjectController@markAbandoned')
            ->name('projects.markAbandoned');
        Route::patch('projects/{project}/markComplete', 'Members\ProjectController@markComplete')
            ->name('projects.markComplete');
        Route::post('projects/{project}/print', 'Members\ProjectController@printLabel')
            ->name('projects.print');
        Route::apiResource('projects', 'Members\ProjectController')
            ->except(['destroy']);

        // Members Boxes and labels
        Route::get('boxes/audit', 'Members\BoxController@audit')
            ->name('boxes.audit');
        Route::patch('boxes/{box}/markInUse', 'Members\BoxController@markInUse')
            ->name('boxes.markInUse');
        Route::patch('boxes/{box}/markAbandoned', 'Members\BoxController@markAbandoned')
            ->name('boxes.markAbandoned');
        Route::patch('boxes/{box}/markRemoved', 'Members\BoxController@markRemoved')
            ->name('boxes.markRemoved');
        Route::post('boxes/{box}/print', 'Members\BoxController@printLabel')
            ->name('boxes.print');
        Route::apiResource('boxes', 'Members\BoxController')
            ->except(['store', 'update', 'destroy']);
    });
});

// All 'client_credentials' api route names are prefixed with client.
Route::name('client.')->prefix('cc')->namespace('Api')->middleware('client')->group(function () {
    // upload new bankTransactions/
    Route::post('bank-transactions/upload', 'Banking\TransactionUploadController@upload')
        ->name('bankTransactions.upload');

    // Governance
    Route::namespace('Governance')->prefix('governance')->name('governance.')->group(function () {
        // Meeting
        Route::get('meetings/next', 'CheckInController@next')
            ->name('meetings.next');
        Route::get('meetings/{meeting}', 'CheckInController@show')
            ->name('meetings.show');
        Route::post('meetings/{meeting}/check-in-rfid', 'CheckInController@checkInUserByRFID')
            ->name('meetings.check-in-rfid');
    });

    Route::post('rfid-token', 'Auth\RfidAccessTokenController')
        ->name('rfid-token');

    Route::post('rfid-tags/register', 'Gatekeeper\RegisterRfidTagController')
        ->name('rfid-tags.register');
});

Route::name('webhook.')->group(function () {
    Route::stripeWebhooks('stripe/webhook')->name('stripe');
});
