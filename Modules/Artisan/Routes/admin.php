<?php

use Modules\Artisan\Http\Controllers\ArtisanController;

Route::prefix('artisan')->name('artisan.')->group(function () {
    Route::get('/', [ArtisanController::class, 'index'])->name('index');
    Route::post('/command', [ArtisanController::class, 'command'])->name('command');
    Route::post('/command-api', [ArtisanController::class, 'commandApi'])->name('command-api');
    Route::get('/read-logs', [ArtisanController::class, 'readLogs'])->name('read-logs');
    Route::get('/clear-logs', [ArtisanController::class, 'clearLogs'])->name('clear-logs');
    Route::get('/admin-debug-toggle', [ArtisanController::class, 'adminDebugToggle'])->name('admin-debug-toggle');
    Route::get('/env-editor', [ArtisanController::class, 'envEditor'])->name('env-editor');
    Route::post('/env-editor-save', [ArtisanController::class, 'envEditorSave'])->name('env-editor-save');
    Route::get('/env-backups', [ArtisanController::class, 'envBackups'])->name('env-backups');
    Route::get('/env-backup-restore/{file}', [ArtisanController::class, 'envBackupsRestore'])->name('env-backup-restore');
    Route::get('/env-backup-delete/{file}', [ArtisanController::class, 'envBackupsDelete'])->name('env-backup-delete');
    Route::get('/env-backup-download/{file}', [ArtisanController::class, 'envBackupsDownload'])->name('env-backup-download');
});
