<?php

use App\Http\Controllers\SyncController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Api\ReminderApiController;
use Illuminate\Support\Facades\Route;

// Endpoint untuk memicu sinkronisasi data dari Google Sheets
Route::post('/sync-now', [SyncController::class, 'sync']);

// Endpoint untuk mengambil data customer (dalam format JSON)
Route::get('/customers', [CustomerController::class, 'hotdDetail']); 

// Endpoint untuk menyimpan riwayat reminder dari Telegram Bot / Apps Script
Route::post('/reminders', [ReminderApiController::class, 'store']);

