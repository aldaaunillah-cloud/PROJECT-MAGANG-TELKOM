<?php

use App\Http\Controllers\SyncController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Api\ReminderApiController;
use Illuminate\Support\Facades\Route;

// Endpoint untuk memicu sinkronisasi data dari Google Sheets (dibatasi maks 5 kali per menit)
Route::post('/sync-now', [SyncController::class, 'sync'])->middleware('throttle:5,1');

// Endpoint untuk mengambil data customer (dalam format JSON)
Route::get('/customers', [CustomerController::class, 'hotdDetail']); 

// Endpoint untuk menyimpan riwayat reminder dari Telegram Bot / Apps Script (dibatasi maks 60 kali per menit)
Route::post('/reminders', [ReminderApiController::class, 'store'])->middleware('throttle:60,1');

