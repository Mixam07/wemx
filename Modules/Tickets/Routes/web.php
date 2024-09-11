<?php

use Illuminate\Support\Facades\Route;
use Modules\Tickets\Http\Controllers\TicketsController;
use Modules\Tickets\Http\Controllers\TicketsAdminController;
use Modules\Tickets\Http\Middleware\TicketAccess;

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

Route::group(['prefix' => 'tickets', 'middleware' => 'auth'], function () {
    Route::get('/', [TicketsController::class, 'tickets'])->name('tickets.index');
    Route::get('/create', [TicketsController::class, 'createTicket'])->name('tickets.create');
    Route::post('/create', [TicketsController::class, 'storeTicket'])->name('tickets.store');

    // to do add middleware to protect tickets
    Route::group(['prefix' => '{ticket}', 'middleware' => TicketAccess::class], function () {
        Route::get('/', [TicketsController::class, 'view'])->name('tickets.view');
        Route::post('/update', [TicketsController::class, 'update'])->name('tickets.update');
        Route::post('/create-message', [TicketsController::class, 'createMessage'])->name('tickets.message.create');
        Route::get('/subscribe', [TicketsController::class, 'subscribe'])->name('tickets.subscribe');
        Route::get('/lock', [TicketsController::class, 'lock'])->name('tickets.lock');
        Route::get('/close', [TicketsController::class, 'close'])->name('tickets.close');
        Route::get('/delete', [TicketsController::class, 'delete'])->name('tickets.delete');
        Route::post('/members/create', [TicketsController::class, 'createMember'])->name('tickets.members.create')->middleware('permission');
        Route::get('/members/{member}/delete', [TicketsController::class, 'deleteMember'])->name('tickets.members.delete')->middleware('permission');
    });
});


// admin routes
Route::group(['prefix' => 'admin/tickets', 'middleware' => 'permission'], function () {
    Route::get('/', [TicketsAdminController::class, 'tickets'])->name('admin.tickets');
    Route::get('/open', [TicketsAdminController::class, 'openTickets'])->name('admin.tickets.open');
    Route::get('/closed', [TicketsAdminController::class, 'closedTickets'])->name('admin.tickets.closed');
    Route::get('/locked', [TicketsAdminController::class, 'lockedTickets'])->name('admin.tickets.locked');
    Route::get('/settings', [TicketsAdminController::class, 'settings'])->name('admin.tickets.settings');
    Route::get('/settings/create-api-key', [TicketsAdminController::class, 'createApiKey'])->name('admin.tickets.create-api-key');
    Route::get('/settings/view-api-key', [TicketsAdminController::class, 'viewApiKey'])->name('admin.tickets.view-api-key');

    Route::group(['prefix' => 'departments'], function () {
        Route::get('/', [TicketsAdminController::class, 'departments'])->name('tickets.departments.index');
        Route::get('/create', [TicketsAdminController::class, 'createDepartment'])->name('tickets.departments.create');
        Route::post('/create', [TicketsAdminController::class, 'storeDepartment'])->name('tickets.departments.store');
        Route::get('/{department}/edit', [TicketsAdminController::class, 'editDepartment'])->name('tickets.departments.edit');
        Route::post('/{department}/update', [TicketsAdminController::class, 'updateDepartment'])->name('tickets.departments.update');
        Route::get('/{department}/delete', [TicketsAdminController::class, 'deleteDepartment'])->name('tickets.departments.delete');
    }); 

    Route::group(['prefix' => 'responders'], function () {
        Route::get('/', [TicketsAdminController::class, 'responders'])->name('tickets.responders.index');
        Route::get('/create', [TicketsAdminController::class, 'createResponder'])->name('tickets.responders.create');
        Route::post('/create', [TicketsAdminController::class, 'storeResponder'])->name('tickets.responders.store');
        Route::get('/{responder}/edit', [TicketsAdminController::class, 'editResponder'])->name('tickets.responders.edit');
        Route::post('/{responder}/update', [TicketsAdminController::class, 'updateResponder'])->name('tickets.responders.update');
        Route::post('/{responder}/add-keyword', [TicketsAdminController::class, 'storeKeyword'])->name('tickets.responders.keyword.store');
        Route::get('/{responder}/delete', [TicketsAdminController::class, 'deleteResponder'])->name('tickets.responders.delete');
        Route::get('/keyword/{keyword}/delete', [TicketsAdminController::class, 'deleteKeyword'])->name('tickets.keywords.delete');
    }); 

});