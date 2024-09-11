<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Tickets\Http\Controllers\TicketsAPIController;

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

Route::get('/departments', [TicketsAPIController::class, 'departments']);
Route::get('/responders', [TicketsAPIController::class, 'responders']);
Route::get('/sync', [TicketsAPIController::class, 'syncDiscord']);

Route::group([], function () {
    Route::get('/', [TicketsAPIController::class, 'tickets']);

    Route::group(['prefix' => '{ticket}'], function () {
        Route::get('/', [TicketsAPIController::class, 'getTicket']);
        Route::get('/messages', [TicketsAPIController::class, 'getTicketMessages']);
        Route::post('/discord-message', [TicketsAPIController::class, 'createDiscordMessage']);
        Route::get('/timeline', [TicketsAPIController::class, 'getTicketTimeline']);
        Route::get('/close-or-open', [TicketsAPIController::class, 'closeOrOpen']);
        Route::get('/close', [TicketsAPIController::class, 'closeTicket']);
        Route::get('/reopen', [TicketsAPIController::class, 'reopenTicket']);
        Route::get('/lock-or-unlock', [TicketsAPIController::class, 'lockOrUnlock']);
        Route::get('/lock', [TicketsAPIController::class, 'lockTicket']);
        Route::get('/unlock', [TicketsAPIController::class, 'unlockTicket']);
        Route::get('/delete', [TicketsAPIController::class, 'deleteTicket']);
    });
});
