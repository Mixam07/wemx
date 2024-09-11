<?php

use Illuminate\Support\Facades\Route;

Route::prefix('locales')->group(function () {
    Route::get('/', 'LocalesController@index')->name('locales.view');
    Route::get('/translate/{code}', 'LocalesController@translate')->name('locales.translate');
    Route::post('file/translate/{code}', 'LocalesController@translateFile')->name('locales.translate.file');
    Route::post('save/translate/{code}', 'LocalesController@translateSave')->name('locales.translate.save');
    Route::post('/api/translate', 'LocalesController@translateApi')->name('locales.translate.api');
    Route::any('/import', 'LocalesController@import')->name('locales.import');
    Route::any('/generate', 'LocalesController@generate')->name('locales.generate');
    Route::any('/remove/{code}', 'LocalesController@remove')->name('locales.remove');
})->middleware('permission');
