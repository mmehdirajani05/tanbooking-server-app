<?php

use App\Http\Controllers\Api\Admin\BookingController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\HotelController;
use App\Http\Controllers\Api\Admin\SupportChatController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('dashboard',         [DashboardController::class, 'overview']);

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('',              [UserController::class, 'index']);
        Route::post('',             [UserController::class, 'store']);
        Route::get('{id}',          [UserController::class, 'show']);
        Route::put('{id}',          [UserController::class, 'update']);
        Route::post('{id}/toggle',  [UserController::class, 'toggleStatus']);
        Route::delete('{id}',       [UserController::class, 'destroy']);
    });

    // Hotel Management
    Route::prefix('hotels')->group(function () {
        Route::get('',              [HotelController::class, 'index']);
        Route::get('pending',       [HotelController::class, 'pending']);
        Route::get('{id}',          [HotelController::class, 'show']);
        Route::post('{id}/approve', [HotelController::class, 'approve']);
        Route::post('{id}/reject',  [HotelController::class, 'reject']);
    });

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('',              [BookingController::class, 'index']);
    });

    // Support Chat
    Route::prefix('chats')->group(function () {
        Route::get('',                          [SupportChatController::class, 'index']);
        Route::post('{conversationId}/assign',  [SupportChatController::class, 'assign']);
    });

});
