<?php

use Modules\PagePlus\Http\Controllers\AdminPagePlusController;
use Illuminate\Support\Facades\Route;

Route::prefix('pageplus')->group(function() {
    Route::get('/', [AdminPagePlusController::class, 'index'])->name('admin.pageplus.index');
    Route::get('/create', [AdminPagePlusController::class, 'create'])->name('admin.pageplus.create');
    Route::post('/store', [AdminPagePlusController::class, 'store'])->name('admin.pageplus.store');
    Route::get('/edit/{page}', [AdminPagePlusController::class, 'create'])->name('admin.pageplus.edit');
    Route::get('/destroy/{page}', [AdminPagePlusController::class, 'destroy'])->name('admin.pageplus.delete');
    Route::get('/translate/{page}/{locale?}', [AdminPagePlusController::class, 'translate'])
        ->defaults('locale', app()->getLocale())
        ->name('admin.pageplus.translate');
    Route::post('/translate/{page}', [AdminPagePlusController::class, 'translateStore'])->name('admin.pageplus.translate.store');
    Route::get('/change-order/{page}/{action?}', [AdminPagePlusController::class, 'changeOrder'])->name('admin.pageplus.change_order');
    Route::get('/toggle-editor', [AdminPagePlusController::class, 'toggleEditor'])->name('admin.pageplus.toggle_editor');

});
