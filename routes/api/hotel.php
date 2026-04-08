<?php

use App\Http\Controllers\Api\HotelOwner\BookingController;
use App\Http\Controllers\Api\HotelOwner\HotelController;
use App\Http\Controllers\Api\HotelOwner\InventoryController;
use App\Http\Controllers\Api\HotelOwner\RoomTypeController;
use App\Http\Controllers\Api\HotelOwner\SupportChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('hotel')->middleware(['auth:sanctum', 'role:partner'])->group(function () {

    // Hotel CRUD
    Route::post('create',           [HotelController::class, 'store']);
    Route::get('list',              [HotelController::class, 'index']);
    Route::get('{id}',              [HotelController::class, 'show']);
    Route::put('{id}',              [HotelController::class, 'update']);
    Route::delete('{id}',           [HotelController::class, 'destroy']);

    // Room Types
    Route::prefix('{hotelId}/rooms')->group(function () {
        Route::get('',              [RoomTypeController::class, 'index']);
        Route::post('',             [RoomTypeController::class, 'store']);
        Route::get('{id}',          [RoomTypeController::class, 'show']);
        Route::put('{id}',          [RoomTypeController::class, 'update']);
        Route::delete('{id}',       [RoomTypeController::class, 'destroy']);
    });

    // Inventory
    Route::prefix('{hotelId}/inventory')->group(function () {
        Route::get('',                              [InventoryController::class, 'index']);
        Route::put('room/{roomTypeId}',             [InventoryController::class, 'update']);
        Route::post('room/{roomTypeId}/bulk',       [InventoryController::class, 'bulkUpdate']);
        Route::get('room/{roomTypeId}/{startDate}/{endDate}', [InventoryController::class, 'show']);
    });

    // Bookings
    Route::prefix('{hotelId}/bookings')->group(function () {
        Route::get('',                      [BookingController::class, 'index']);
        Route::get('{id}',                  [BookingController::class, 'show']);
        Route::put('{id}/status',           [BookingController::class, 'updateStatus']);
        Route::get('customers/list',        [BookingController::class, 'customers']);
        Route::post('customers/walk-in',    [BookingController::class, 'createWalkInCustomer']);
    });

    // Support Chat
    Route::prefix('{hotelId}/chats')->group(function () {
        Route::get('',                      [SupportChatController::class, 'index']);
        Route::get('{id}',                  [SupportChatController::class, 'show']);
        Route::post('{conversationId}/reply', [SupportChatController::class, 'reply']);
    });

});
