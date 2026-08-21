<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::middleware(['web'])->group(function () {
    Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::middleware(['auth'])->group(function () {
    
    Route::get('/', [CustomerController::class, 'dashboard'])->name('home');
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/download-ssl', [CustomerController::class, 'downloadSsl'])->name('dashboard.download-ssl');

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/export/excel', [CustomerController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [CustomerController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{id}/download-ssl', [CustomerController::class, 'downloadSsl'])->name('download-ssl');
    });

    // AJAX Filter for Dashboard
    Route::get('/filter/agencies', [CustomerController::class, 'getAgencies'])->name('filter.agencies');
    Route::get('/filter/sales', [CustomerController::class, 'getSales'])->name('filter.sales');

    Route::get('/rekap/agency', [CustomerController::class, 'rekapAgency'])->name('rekap.agency');

    // ============================================
    // REMINDERS - UDAH BENER DI SINI!
    // ============================================
    Route::get('/reminders', [CustomerController::class, 'riwayatReminder'])->name('reminders.index');

    // ============================================
    Route::get('/billing/{billing_ke}', [BillingController::class, 'detail'])->name('billing.detail');
    Route::get('/billing/{billing_ke}/export-excel', [BillingController::class, 'exportExcel'])->name('billing.detail.export-excel');
    Route::get('/billing/{billing_ke}/print-pdf', [BillingController::class, 'printPdf'])->name('billing.detail.print-pdf');

    // ============================================
    // HOTD DETAIL
    // ============================================
    Route::get('/hotd-detail/{billingKe}/{datel}', [CustomerController::class, 'hotdDetail'])
        ->name('hotd.detail');
    Route::get('/hotd-detail/{billingKe}/{datel}/export', [CustomerController::class, 'exportHotdExcel'])
        ->name('hotd.export');

    // ============================================
    // SYNC
    // ============================================
    Route::prefix('sync')->group(function () {
        Route::get('/', [SyncController::class, 'index'])->name('sync.index');
        Route::get('/google-sheets', [SyncController::class, 'sync'])->name('sync.google-sheets');
        Route::post('/excel', [SyncController::class, 'syncExcel'])->name('sync.excel');
        Route::get('/status', [SyncController::class, 'status'])->name('sync.status');
    });

    // ============================================
    // PROFILE / ADMINISTRATOR
    // ============================================
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});
});