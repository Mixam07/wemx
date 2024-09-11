<?php

use App\Http\Controllers\Auth;
use App\Http\Middleware\CheckRevokedDevices;
use App\Http\Middleware\TwoFactorAuthentication;
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

Route::middleware('auth')->group(function () {
    Route::get('/auth/logout', [Auth\AuthController::class, 'logout'])->name('logout')->withoutMiddleware(App\Http\Middleware\Maintenance::class);

    Route::get('/auth/{device}/reauthenticate', [Auth\ReAuthenticationController::class, 'client'])
        ->name('client.reauthenticate')->withoutMiddleware(CheckRevokedDevices::class);
    Route::post('/auth/{device}/reauthenticate', [Auth\ReAuthenticationController::class, 'clientPost'])
        ->name('client.reauthenticate.post')->withoutMiddleware(CheckRevokedDevices::class);
    
    Route::get('/admin/reauthenticate', [Auth\ReAuthenticationController::class, 'index'])->name('reauthenticate');
    Route::post('/admin/reauthenticate', [Auth\ReAuthenticationController::class, 'reauthenticate'])->name('reauthenticate.submit');
});

Route::middleware('auth')->prefix('/auth/verification')->withoutMiddleware(App\Http\Middleware\Maintenance::class)->group(function () {
    Route::get('/', [Auth\AuthController::class, 'verification'])->name('verification');
    Route::post('/validate', [Auth\AuthController::class, 'validateVerification'])->name('verification.validate');
});

Route::middleware('auth')->prefix('/two-factor-authentication')->withoutMiddleware(\App\Http\Middleware\ForceTwoFa::class)->group(function () {
    Route::post('/disable', [Auth\TwoFactorController::class, 'disableTwoFactor'])->name('2fa.disable');
    Route::get('/setup', [Auth\TwoFactorController::class, 'enableTwoFactor'])->name('2fa.setup');
    Route::post('/setup', [Auth\TwoFactorController::class, 'setupTwoFactor'])->name('2fa.setup.validate');
    Route::get('/recovery', [Auth\TwoFactorController::class, 'recovery'])->name('2fa.recovery');
    Route::get('/recovery/download', [Auth\TwoFactorController::class, 'downloadRecoveryCodes'])->name('2fa.recovery.download');
    Route::post('/activate', [Auth\TwoFactorController::class, 'activateTwoFactor'])->name('2fa.activate');
    Route::get('/validate', [Auth\TwoFactorController::class, 'validateTwoFactor'])->name('2fa.validate')->withoutMiddleware(TwoFactorAuthentication::class);
    Route::post('/validate', [Auth\TwoFactorController::class, 'validateTwoFactorCheck'])->name('2fa.validate.check')->middleware('throttle:authentication')->withoutMiddleware(TwoFactorAuthentication::class);
    Route::get('/lost-access', [Auth\TwoFactorController::class, 'recover'])->name('2fa.recover')->withoutMiddleware(TwoFactorAuthentication::class);
    Route::post('/lost-access', [Auth\TwoFactorController::class, 'recoverDeviceAccess'])->name('2fa.recover.access')->middleware('throttle:authentication')->withoutMiddleware(TwoFactorAuthentication::class);
});

Route::middleware('guest')->prefix('auth')->group(function () {

    Route::get('login', [Auth\AuthController::class, 'login'])
    ->name('login');

    Route::post('login', [Auth\AuthController::class, 'authenticate'])
    ->name('login.authenticate')->middleware('throttle:authentication');

    Route::get('register', [Auth\AuthController::class, 'register'])
    ->name('register');

    Route::post('register', [Auth\AuthController::class, 'store'])
    ->name('register.store');

    Route::get('forgot-password', [Auth\AuthController::class, 'forgotPassword'])
    ->name('forgot-password');

    Route::post('forgot-password', [Auth\AuthController::class, 'sendPasswordResetEmail'])
    ->name('forgot-password.send-email')->middleware('throttle:authentication');;

    Route::get('reset-password/{token}', [Auth\AuthController::class, 'resetPassword'])
    ->name('reset-password');

    Route::post('reset-password/{token}', [Auth\AuthController::class, 'resetPasswordUpdate'])
    ->name('reset-password.update');
});
