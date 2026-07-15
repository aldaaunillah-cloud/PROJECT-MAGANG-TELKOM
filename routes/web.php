<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/data-all', function () {
    return view('data-all');
})->name('data.all');

Route::get('/agency-mentah', function () {
    return view('agency-mentah');
})->name('agency.mentah');

Route::get('/rekap-billing', function () {
    return view('rekap-billing');
})->name('rekap.billing');

Route::get('/saldo', function () {
    return view('saldo');
})->name('saldo');

Route::get('/riwayat-reminder', function () {
    return view('riwayat-reminder');
})->name('riwayat.reminder');