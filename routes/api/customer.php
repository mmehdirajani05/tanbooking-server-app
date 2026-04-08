<?php

use App\Http\Controllers\Api\Customer\BookingController;
use App\Http\Controllers\Api\Customer\SupportChatController;
use Illuminate\Support\Facades\Route;

// Public hotel search
Route::prefix('customer')->group(function () {
    Route::post('hotels/search',  [BookingController::class, 'search']);
});

// Protected routes
Route::prefix('customer')->middleware(['auth:sanctum', 'role:customer'])->group(function () {

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('',            [BookingController::class, 'index']);
        Route::post('',           [BookingController::class, 'store']);
        Route::get('{id}',        [BookingController::class, 'show']);
        Route::post('{id}/cancel', [BookingController::class, 'cancel']);
    });

    // Support Chat
    Route::prefix('chats')->group(function () {
        Route::post('start',                      [SupportChatController::class, 'start']);
        Route::get('',                            [SupportChatController::class, 'index']);
        Route::get('{id}',                        [SupportChatController::class, 'show']);
        Route::post('{conversationId}/message',   [SupportChatController::class, 'sendMessage']);
        Route::post('{id}/close',                 [SupportChatController::class, 'close']);
    });

});
