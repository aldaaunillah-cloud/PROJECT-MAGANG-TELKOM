@extends('layouts.app')

@section('page-title', 'RIWAYAT CHATBOT REMINDER')
@section('page-subtitle', 'Daftar pengiriman reminder chatbot ke Sales Agency')

@section('content')

<div class="filter-box" style="border-radius: 20px;">
    <div class="row align-items-end">
        <div class="col-md-3">
            <label>Tanggal</label>
            <div class="input-group">
                <input type="text" class="form-control" value="01/07/2026 - 05/07/2026" readonly style="background:#fff; font-size:13px; border-color:#cbd5e1; color:#3b82f6;">
                <span class="input-group-text bg-white" style="border-color:#cbd5e1; color:#3b82f6;">
                    <i class="bi bi-calendar3"></i>
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <label>Sales Agency</label>
            <select class="form-select" style="border-color:#cbd5e1;">
                <option>Semua Sales Agency</option>
            </select>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end">
            <div class="input-group w-75">
                <input type="text" class="form-control" placeholder="Cari Customer / pesan..." style="border-radius: 20px 0 0 20px;">
                <button class="btn btn-outline-secondary bg-white" style="border-radius: 0 20px 20px 0; border-color: #dee2e6;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="table-placeholder mt-4">
    <div style="padding:40px; background:#f8f9fa; border:2px dashed #cbd5e1; border-radius:8px; text-align:center; color:#64748b;">
        <i class="bi bi-chat-dots" style="font-size:32px; display:block; margin-bottom:12px; color:#94a3b8;"></i>
        Tabel Riwayat Chatbot Reminder akan ditampilkan di sini nanti.
    </div>
</div>

@endsection
