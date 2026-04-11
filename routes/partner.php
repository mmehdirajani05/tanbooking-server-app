<?php

use App\Http\Controllers\Partner\CompanyController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Partner\Hotel\PartnerHotelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Partner Panel Routes
|--------------------------------------------------------------------------
|
| These routes are for the partner panel. Partners can manage their company,
| hotels, tourism packages, events, and view bookings.
|
*/

Route::prefix('partner')->middleware(['auth', 'role:partner'])->name('partner.')->group(function () {

    // Company pending/rejected pages (accessible without approved company)
    Route::get('company/pending', [CompanyController::class, 'pending'])->name('company.pending');
    Route::get('company/rejected', [CompanyController::class, 'rejected'])->name('company.rejected');

    // Protected routes - require approved company
    Route::middleware(['company.approved'])->group(function () {
        
        // Dashboard
        Route::get('dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
        
        // Company Management
        Route::prefix('company')->name('company.')->group(function () {
            Route::get('', [CompanyController::class, 'show'])->name('show');
            Route::get('edit', [CompanyController::class, 'edit'])->name('edit');
            Route::put('', [CompanyController::class, 'update'])->name('update');
            Route::get('documents', [CompanyController::class, 'documents'])->name('documents');
            Route::post('documents/upload', [CompanyController::class, 'uploadDocument'])->name('documents.upload');
        });

        // Hotel Module (requires hotel module access)
        Route::middleware(['module.access:hotel'])->prefix('hotels')->name('hotels.')->group(function () {
            Route::get('', [PartnerHotelController::class, 'index'])->name('index');
            Route::get('create', [PartnerHotelController::class, 'create'])->name('create');
            Route::post('', [PartnerHotelController::class, 'store'])->name('store');
            Route::get('{id}', [PartnerHotelController::class, 'show'])->name('show');
            Route::get('{id}/edit', [PartnerHotelController::class, 'edit'])->name('edit');
            Route::put('{id}', [PartnerHotelController::class, 'update'])->name('update');
            Route::delete('{id}', [PartnerHotelController::class, 'destroy'])->name('destroy');
        });

        // Tourism Module (requires tourism module access)
        Route::middleware(['module.access:tourism'])->prefix('tourism')->name('tourism.')->group(function () {
            Route::get('packages', [\App\Http\Controllers\Partner\Tourism\PartnerTourismController::class, 'index'])->name('packages.index');
            Route::get('packages/create', [\App\Http\Controllers\Partner\Tourism\PartnerTourismController::class, 'create'])->name('packages.create');
            Route::post('packages', [\App\Http\Controllers\Partner\Tourism\PartnerTourismController::class, 'store'])->name('packages.store');
        });

        // Event Module (requires event module access)
        Route::middleware(['module.access:event'])->prefix('events')->name('events.')->group(function () {
            Route::get('', [\App\Http\Controllers\Partner\Event\PartnerEventController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Partner\Event\PartnerEventController::class, 'create'])->name('create');
            Route::post('', [\App\Http\Controllers\Partner\Event\PartnerEventController::class, 'store'])->name('store');
        });

        // Bookings (All modules)
        Route::get('bookings', [\App\Http\Controllers\Partner\PartnerBookingController::class, 'index'])->name('bookings.index');
    });
});