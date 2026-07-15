@extends('layouts.app')

@section('page-title', 'REKAP AGENCY')
@section('page-subtitle', 'Penyelesaian Billing 1 dan 2 - Agensi Witel Priangan Timur')

@section('content')

<div class="filter-box">
    <div class="row align-items-end">
        <div class="col-md-3">
            <label>AGENCY</label>
            <select class="form-select">
                <option>Semua Agency</option>
            </select>
        </div>
        <div class="col-md-9 d-flex align-items-end justify-content-end gap-2">
            <button class="btn btn-filter px-4">
                <i class="bi bi-funnel-fill me-2"></i>Terapkan Filter
            </button>
            <button class="btn btn-reset px-4">
                <i class="bi bi-arrow-repeat me-2"></i>Reset
            </button>
        </div>
    </div>
</div>

<div class="table-placeholder mt-4">
    <h6 style="font-weight:700; font-size:14px; text-transform:uppercase;">REKAP BILLING 1 DAN 2</h6>
    <div style="margin-top:24px; padding:40px; background:#f8f9fa; border:2px dashed #cbd5e1; border-radius:8px; text-align:center; color:#64748b;">
        <i class="bi bi-table" style="font-size:32px; display:block; margin-bottom:12px; color:#94a3b8;"></i>
        Tabel Rekap Billing 1 & 2 per Agency akan ditampilkan di sini nanti.
    </div>
</div>

@endsection
