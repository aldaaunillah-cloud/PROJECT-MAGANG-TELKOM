<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::middleware(['web'])->group(function () {
    Auth::routes([
        'register' => false,
        'reset'    => false, // Gunakan custom OTP Telegram di bawah
        'verify'   => false,
    ]);

    // ============================================
    // RESET PASSWORD VIA BOT TELEGRAM (OTP)
    // ============================================
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetOtp'])
        ->name('password.email');
    Route::get('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])
        ->name('password.update');

    Route::middleware(['auth'])->group(function () {

        Route::get('/', [CustomerController::class, 'dashboard'])->name('home');
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');

        Route::get(
            '/dashboard/download-ssl/{snd}',
            [CustomerController::class, 'downloadSsl']
        )->name('dashboard.download-ssl');

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/export/excel', [CustomerController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [CustomerController::class, 'exportPdf'])->name('export.pdf');

            Route::get(
                '/{snd}/download-ssl',
                [CustomerController::class, 'downloadSsl']
            )->name('download-ssl');
        });

        // AJAX Filter for Dashboard
        Route::get('/filter/agencies', [CustomerController::class, 'getAgencies'])
            ->name('filter.agencies');

        Route::get('/filter/sales', [CustomerController::class, 'getSales'])
            ->name('filter.sales');

        // Rekap Agency
        Route::get('/rekap/agency', [CustomerController::class, 'rekapAgency'])
            ->name('rekap.agency');

        // Rekap Agency Billing 3-6 HOTD
        Route::get('/rekap/agency-billing-3-6', [CustomerController::class, 'rekapAgencyBilling36'])
            ->name('rekap.agency.billing36');

        // ============================================
        // REMINDERS
        // ============================================
        Route::get('/reminders', [CustomerController::class, 'riwayatReminder'])
            ->name('reminders.index');

        // ============================================
        // BILLING
        // ============================================
        Route::get('/billing/{billing_ke}', [BillingController::class, 'detail'])
            ->name('billing.detail');

        Route::get('/billing/{billing_ke}/export-excel', [BillingController::class, 'exportExcel'])
            ->name('billing.detail.export-excel');

        Route::get('/billing/{billing_ke}/print-pdf', [BillingController::class, 'printPdf'])
            ->name('billing.detail.print-pdf');

        // ============================================
        // HOTD DETAIL
        // ============================================
        Route::get('/hotd-detail/{billingKe}/{datel}', [CustomerController::class, 'hotdDetail'])
            ->name('hotd.detail');

        Route::get('/hotd-detail/{billingKe}/{datel}/export', [CustomerController::class, 'exportHotdExcel'])
            ->name('hotd.export');

        // ============================================
        // KHUSUS PIKOL (ADMIN / SINKRONISASI)
        // ============================================
        Route::middleware(['role:pikol'])->group(function () {
            // SINKRONISASI
            Route::prefix('sync')->group(function () {
                Route::get('/', [SyncController::class, 'index'])
                    ->name('sync.index');

                Route::get('/google-sheets', [SyncController::class, 'sync'])
                    ->name('sync.google-sheets');

                Route::get('/init', [SyncController::class, 'init'])
                    ->name('sync.init');

                Route::post('/batch', [SyncController::class, 'syncBatch'])
                    ->name('sync.batch');

                Route::get('/status', [SyncController::class, 'status'])
                    ->name('sync.status');
            });

            // MANAJEMEN ANGGOTA & AGENSI
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
                Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
                Route::put('/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
                Route::patch('/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('toggle-status');
                Route::delete('/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
            });
        });

        // ============================================
        // PROFILE / ADMINISTRATOR
        // ============================================
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])
            ->name('profile');

        Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])
            ->name('profile.update');

        Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])
            ->name('profile.password');
    });
});