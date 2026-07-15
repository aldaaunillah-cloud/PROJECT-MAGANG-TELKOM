@extends('layouts.app')

@section('page-title', 'AGENCY MENTAH')
@section('page-subtitle', 'Daftar seluruh data agency Telkom beserta dealer dan PIC Sales')

@section('content')

<div class="filter-box">
    <div class="row mb-3">
        <div class="col-md-2">
            <label>Pilih Dealer</label>
            <select class="form-select">
                <option>Semua Dealer</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Pilih STO</label>
            <select class="form-select">
                <option>Semua STO</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Pilih Sales</label>
            <select class="form-select">
                <option>Semua Sales</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Pilih Package</label>
            <select class="form-select">
                <option>Semua Package</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end justify-content-end">
            <div class="input-group w-100">
                <input type="text" class="form-control" placeholder="Cari Dealer / Agency..." style="border-radius: 20px 0 0 20px;">
                <button class="btn btn-outline-secondary bg-white" style="border-radius: 0 20px 20px 0; border-color: #dee2e6;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="table-placeholder">
    <div style="padding:40px; background:#f8f9fa; border:2px dashed #cbd5e1; border-radius:8px; text-align:center; color:#64748b;">
        <i class="bi bi-table" style="font-size:32px; display:block; margin-bottom:12px; color:#94a3b8;"></i>
        Tabel Daftar Seluruh Data Agency akan ditampilkan di sini nanti.
    </div>
</div>

@endsection
