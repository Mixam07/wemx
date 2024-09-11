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

Route::get('/aff/{affiliate}', 'AffiliatesController@affiliate')->name('affiliate');;


Route::prefix('affiliates')->middleware('auth')->group(function() {
    Route::get('/', 'AffiliatesController@manage')->name('affiliates.manage');
    Route::post('/payout/create', 'AffiliatesController@payout')->name('affiliates.payout');
    Route::get('/show-last-days/{days}', 'AffiliatesController@showLastDays')->name('affiliates.days');
});

Route::prefix('/admin/affiliates')->middleware('permission')->group(function() {
    Route::get('/settings', 'AffiliatesAdminController@settings')->name('affiliates.settings');
    Route::get('/', 'AffiliatesAdminController@affiliates')->name('affiliates.index');
    Route::get('/{affiliate}/edit', 'AffiliatesAdminController@edit')->name('affiliates.edit');
    Route::post('/{affiliate}/update', 'AffiliatesAdminController@update')->name('affiliates.update');

    Route::get('/payouts', 'AffiliatesAdminController@payouts')->name('affiliates.payouts');
    Route::get('/payouts/{payouts}/edit', 'AffiliatesAdminController@editPayout')->name('affiliates.payouts.edit');
    Route::post('/payouts/{payouts}/update', 'AffiliatesAdminController@updatePayout')->name('affiliates.payouts.update');
});
