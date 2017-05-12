<?php


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

Route::middleware('auth:api')->group(function () {
    // Search for members
    // api/search/users/matt                    Search term as part of the
    // api/search/users?q=matt                  Search term as a parametre
    // api/search/users?q=matt&withAccount=true Only search for memebrs with Accounts
    Route::name('search.users')->get('search/users/{searchQuery?}', 'Api\SearchController@users');
});
