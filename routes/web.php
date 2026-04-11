<?php

use App\Http\Controllers\Admin\Web\AdminAuthController;
use App\Http\Controllers\Admin\Web\AdminDashboardController;
use App\Http\Controllers\Admin\CompanyApprovalController;
use App\Http\Controllers\Partner\PartnerAuthController;
use App\Http\Controllers\Partner\CompanyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ================= ADMIN AUTH =================
Route::get('admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// ================= PARTNER AUTH =================
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('login', [PartnerAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [PartnerAuthController::class, 'login'])->name('login.post');
    Route::get('register', [PartnerAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [PartnerAuthController::class, 'register'])->name('register.post');
    
    // Company Registration (requires partner role)
    Route::middleware(['auth', 'role:partner'])->prefix('company')->name('company.')->group(function () {
        Route::get('register', [\App\Http\Controllers\Partner\CompanyRegistrationController::class, 'create'])->name('register');
        Route::post('register', [\App\Http\Controllers\Partner\CompanyRegistrationController::class, 'store'])->name('register.post');
    });
});

// Logout (shared)
Route::post('logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Admin panel (protected)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

    // Company Approvals
    Route::get('companies/pending', [CompanyApprovalController::class, 'pending'])->name('companies.pending');
    Route::get('companies/{id}', [CompanyApprovalController::class, 'show'])->name('companies.show');
    Route::post('companies/{id}/approve', [CompanyApprovalController::class, 'approve'])->name('companies.approve');
    Route::post('companies/{id}/reject', [CompanyApprovalController::class, 'reject'])->name('companies.reject');
    Route::get('companies/{id}/documents', [CompanyApprovalController::class, 'documents'])->name('companies.documents');
    Route::post('companies/documents/{docId}/verify', [CompanyApprovalController::class, 'verifyDocument'])->name('companies.documents.verify');

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

// Partner Panel
require __DIR__ . '/partner.php';
