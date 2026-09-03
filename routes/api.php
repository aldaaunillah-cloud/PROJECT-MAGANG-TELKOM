<?php

use App\Http\Controllers\SyncController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Api\ReminderApiController;
use Illuminate\Support\Facades\Route;

// Endpoint untuk memicu sinkronisasi data dari Google Sheets
// Dilindungi autentikasi dan dibatasi maks 5 kali per menit
Route::post('/sync-now', [SyncController::class, 'sync'])
    ->middleware(['auth:sanctum', 'throttle:5,1']);

// Endpoint untuk mengambil data customer (dalam format JSON)
// Dilindungi autentikasi agar data tidak dapat diakses publik
Route::get('/customers', [CustomerController::class, 'hotdDetail'])
    ->middleware(['auth:sanctum']);

// Endpoint untuk menyimpan riwayat reminder dari Telegram Bot / Apps Script
// Dilindungi Bearer Token custom dan dibatasi maks 60 kali per menit
Route::post('/reminders', [ReminderApiController::class, 'store'])->middleware('throttle:60,1');

// Endpoint untuk mengambil status anggota (Aktif / Tidak Aktif) untuk filter Chatbot Telegram
// Dilindungi Bearer Token custom
Route::get('/members-status', [ReminderApiController::class, 'getMembersStatus'])->middleware('throttle:60,1');

