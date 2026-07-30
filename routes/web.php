<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Auth::routes();

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

    Route::get('/rekap/agency', [CustomerController::class, 'rekapAgency'])->name('rekap.agency');

    // ============================================
    // REMINDERS - UDAH BENER DI SINI!
    // ============================================
    Route::get('/reminders', [CustomerController::class, 'riwayatReminder'])->name('reminders.index');

    // ============================================
    // BILLING DETAIL
    // ============================================
    Route::get('/billing/{billing_ke}', [BillingController::class, 'detail'])->name('billing.detail');

    // ============================================
    // HOTD DETAIL
    // ============================================
    Route::get('/hotd-detail/{billingKe}/{datel}', [CustomerController::class, 'hotdDetail'])
        ->name('hotd.detail');

    // ============================================
    // SYNC
    // ============================================
    Route::prefix('sync')->group(function () {
        Route::get('/', [SyncController::class, 'index'])->name('sync.index');
        Route::get('/google-sheets', [SyncController::class, 'sync'])->name('sync.google-sheets');
        Route::get('/status', [SyncController::class, 'status'])->name('sync.status');
    });
});