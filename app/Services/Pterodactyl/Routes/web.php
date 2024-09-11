<?php

use App\Services\Pterodactyl\Http\Controllers\BackupsController;
use App\Services\Pterodactyl\Http\Controllers\ConsoleController;
use App\Services\Pterodactyl\Http\Controllers\DatabaseController;
use App\Services\Pterodactyl\Http\Controllers\FilesController;
use App\Services\Pterodactyl\Http\Controllers\NetworkController;
use App\Services\Pterodactyl\Http\Controllers\SchedulesController;
use App\Services\Pterodactyl\Http\Controllers\SettingsController;



Route::prefix('pterodactyl')->middleware('auth')->group(function () {
    Route::get('/{order}/login-panel', [ConsoleController::class, 'loginPanel'])->name('pterodactyl.login');
    Route::get('power/{order}/{action}', [ConsoleController::class, 'powerAction'])->name('pterodactyl.power');
});


Route::prefix('/service-pterodactyl/{order}')->middleware(['auth'])->group(function () {

    Route::prefix('console')->name('pterodactyl.')->group(function () {
        Route::get('/', [ConsoleController::class, 'console'])->name('console');
        Route::get('/websocket', [ConsoleController::class, 'websocket'])->name('console.socket');
    });

    Route::prefix('files')->name('pterodactyl.')->group(function () {
        Route::get('/', [FilesController::class, 'files'])->name('files');
        Route::post('/{server?}', [FilesController::class, 'all'])->name('files');
        Route::get('/download/{server}', [FilesController::class, 'download'])->name('files.download');
        Route::get('/directory/{server}', [FilesController::class, 'createDirectory'])->name('files.create_directory');
        Route::get('/rename/{server}', [FilesController::class, 'rename'])->name('files.rename');
        Route::get('/copy/{server}', [FilesController::class, 'copy'])->name('files.copy');
        Route::delete('/delete/{server}', [FilesController::class, 'delete'])->name('files.delete');
        Route::post('/compress/{server}', [FilesController::class, 'compress'])->name('files.compress');
        Route::get('/decompress/{server}', [FilesController::class, 'decompress'])->name('files.decompress');
        Route::post('/write/{server}', [FilesController::class, 'write'])->name('files.write');
        Route::get('/upload/{server}', [FilesController::class, 'getUploadUrl'])->name('files.upload_url');
        Route::get('/content/{server}', [FilesController::class, 'getContent'])->name('files.get_content');
    });

    Route::prefix('databases')->name('pterodactyl.')->group(function () {
        Route::get('/', [DatabaseController::class, 'databases'])->name('databases');
        Route::post('/create/{server}', [DatabaseController::class, 'create'])->name('databases.create');
        Route::post('/delete/{server}', [DatabaseController::class, 'delete'])->name('databases.delete');
        Route::post('/reset-password/{server}', [DatabaseController::class, 'resetPassword'])->name('databases.reset_password');
    });

    Route::prefix('schedules')->name('pterodactyl.')->group(function () {
        Route::get('/', [SchedulesController::class, 'schedules'])->name('schedules');
        Route::get('/{server?}/{schedule}', [SchedulesController::class, 'get'])->name('schedules.get');
        Route::post('/create/{server}', [SchedulesController::class, 'create'])->name('schedules.create');
        Route::post('/update/{server}', [SchedulesController::class, 'update'])->name('schedules.update');
        Route::post('/delete/{server}', [SchedulesController::class, 'delete'])->name('schedules.delete');
        Route::get('/run/{server}/{schedule}', [SchedulesController::class, 'execute'])->name('schedules.execute');
        Route::post('/create-task/{server}', [SchedulesController::class, 'createTask'])->name('schedules.create_task');
        Route::post('/update-task/{server}', [SchedulesController::class, 'updateTask'])->name('schedules.update_task');
        Route::post('/delete-task/{server}', [SchedulesController::class, 'deleteTask'])->name('schedules.delete_task');
    });

    Route::prefix('backups')->name('pterodactyl.')->group(function () {
        Route::get('/', [BackupsController::class, 'backups'])->name('backups');
        Route::get('/lock/{server}/{backup}', [BackupsController::class, 'lockToggle'])->name('backups.lock');
        Route::get('/download/{server}/{backup}', [BackupsController::class, 'download'])->name('backups.download');
        Route::post('/create/{server}', [BackupsController::class, 'create'])->name('backups.create');
        Route::post('/restore/{server}', [BackupsController::class, 'restore'])->name('backups.restore');
        Route::post('/delete/{server}', [BackupsController::class, 'delete'])->name('backups.delete');
    });

    Route::prefix('network')->name('pterodactyl.')->group(function () {
        Route::get('/', [NetworkController::class, 'network'])->name('network');
        Route::get('/assign/{server}', [NetworkController::class, 'assign'])->name('network.assign');
        Route::post('/note/{server}', [NetworkController::class, 'setNote'])->name('network.note');
        Route::get('/primary/{server}', [NetworkController::class, 'setPrimary'])->name('network.primary');
        Route::post('/delete/{server}', [NetworkController::class, 'delete'])->name('network.delete');
    });

    Route::prefix('settings')->name('pterodactyl.')->group(function () {
        Route::get('/', [SettingsController::class, 'settings'])->name('settings');
        Route::post('/update/{server}', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/rename/{server}', [SettingsController::class, 'rename'])->name('settings.rename');
        Route::post('/docker-image/{server}', [SettingsController::class, 'setDockerImage'])->name('settings.docker_image');
        Route::get('/reinstall/{server}', [SettingsController::class, 'reinstall'])->name('settings.reinstall');
        Route::post('/change-password/{server}', [SettingsController::class, 'changePassword'])->name('settings.change_password');
        Route::post('/update-variable/{server}', [SettingsController::class, 'updateVariable'])->name('settings.update_variable');
    });
});
