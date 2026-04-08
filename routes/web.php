<?php

use App\Http\Controllers\Admin\Web\AdminAuthController;
use App\Http\Controllers\Admin\Web\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin login
Route::get('admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('logout');

// Admin panel (protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

    // Hotels
    Route::get('/hotels', [AdminDashboardController::class, 'hotels'])->name('hotels.index');
    Route::get('/hotels/create', [AdminDashboardController::class, 'createHotel'])->name('hotels.create');
    Route::post('/hotels', [AdminDashboardController::class, 'storeHotel'])->name('hotels.store');
    Route::get('/hotels/{id}', [AdminDashboardController::class, 'hotelDetail'])->name('hotels.detail');
    Route::get('/hotels/{id}/edit', [AdminDashboardController::class, 'editHotel'])->name('hotels.edit');
    Route::put('/hotels/{id}', [AdminDashboardController::class, 'updateHotel'])->name('hotels.update');
    Route::delete('/hotels/{id}', [AdminDashboardController::class, 'deleteHotel'])->name('hotels.delete');
    Route::post('/hotels/{id}/approve', [AdminDashboardController::class, 'approveHotel'])->name('hotels.approve');
    Route::post('/hotels/{id}/reject', [AdminDashboardController::class, 'rejectHotel'])->name('hotels.reject');
    Route::post('/hotels/{hotelId}/room-types', [AdminDashboardController::class, 'addRoomType'])->name('hotels.room-types.store');
    Route::post('/hotels/{hotelId}/room-types/{roomTypeId}/inventory', [AdminDashboardController::class, 'setRoomInventory'])->name('hotels.room-types.inventory');
    Route::delete('/hotels/{hotelId}/room-types/{roomTypeId}', [AdminDashboardController::class, 'deleteRoomType'])->name('hotels.room-types.delete');

    // Bookings
    Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/create', [AdminDashboardController::class, 'createBooking'])->name('bookings.create');
    Route::post('/bookings', [AdminDashboardController::class, 'storeBooking'])->name('bookings.store');

    // Chats
    Route::get('/chats', [AdminDashboardController::class, 'chats'])->name('chats.index');
});
