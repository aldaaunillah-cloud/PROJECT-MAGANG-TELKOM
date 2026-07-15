@extends('layouts.app')

@section('page-title', 'DATA ALL')
@section('page-subtitle', 'Daftar keseluruhan data pada sistem')

@section('content')

<div class="filter-box">
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Kategori</label>
            <select class="form-select">
                <option>Semua Kategori</option>
            </select>
        </div>
        <div class="col-md-9 d-flex align-items-end justify-content-end gap-2">
            <button class="btn btn-filter px-4">
                <i class="bi bi-funnel-fill me-2"></i>Terapkan Filter
            </button>
        </div>
    </div>
</div>

<div class="table-placeholder">
    <div style="padding:40px; background:#f8f9fa; border:2px dashed #cbd5e1; border-radius:8px; text-align:center; color:#64748b;">
        <i class="bi bi-table" style="font-size:32px; display:block; margin-bottom:12px; color:#94a3b8;"></i>
        Tabel Data All akan ditampilkan di sini nanti.
    </div>
</div>

@endsection
