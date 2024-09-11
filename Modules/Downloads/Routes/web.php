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

Route::prefix('admin/downloads')->as('downloads.')->middleware('permission')->group(function () {

    Route::get('/', 'DownloadsController@index')->name('index');
    Route::get('create', 'DownloadsController@create')->name('create');
    Route::post('store', 'DownloadsController@store')->name('store');
    Route::get('download/{download}', 'DownloadsController@download')->name('download');
    Route::get('edit/{download}', 'DownloadsController@edit')->name('edit');
    Route::put('update/{download}', 'DownloadsController@update')->name('update');
    Route::delete('destroy/{download}', 'DownloadsController@destroy')->name('destroy');

});

Route::as('downloads.client.')->group(function(){
    Route::get('/downloads', 'ClientDownloadsController@index')->name('downloads');
    Route::get('/downloads/{download}/download', 'ClientDownloadsController@download')->name('download');
});

