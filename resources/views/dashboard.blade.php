@extends('layouts.app')

@section('content')

<div class="banner">
    GEBYAR COLLECTION PRITI 150
</div>

<div class="filter-box">
    <div class="row">
        <div class="col-md-3">
            <label>DATEL</label>
            <select class="form-select">
                <option>Semua Datel</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>AGENCY</label>
            <select class="form-select">
                <option>Semua Agency</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>SALES</label>
            <select class="form-select">
                <option>Semua Sales Agency</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2 justify-content-end">
            <button class="btn btn-filter px-4">
                <i class="bi bi-funnel-fill me-2"></i>Terapkan Filter
            </button>
            <button class="btn btn-reset px-4">
                <i class="bi bi-arrow-repeat me-2"></i>Reset
            </button>
        </div>
    </div>
</div>

<div class="stat-cards-container">
    <div class="card-stat blue">
        <div class="stat-info">
            <h6>TOTAL CUSTOMER (SSL)</h6>
            <h3>3.561</h3>
            <small>Semua Billing</small>
        </div>
        <i class="bi bi-people-fill stat-icon ms-auto"></i>
    </div>
    <div class="card-stat green">
        <div class="stat-info">
            <h6>TOTAL TAGIHAN</h6>
            <h3>Rp. 1.859.425.736</h3>
            <small>Semua Billing</small>
        </div>
        <i class="bi bi-cash-stack stat-icon ms-auto"></i>
    </div>
    <div class="card-stat pink">
        <div class="stat-info">
            <h6>TOTAL SALES AGENCY</h6>
            <h3>48</h3>
            <small>Sales</small>
        </div>
        <i class="bi bi-person-fill stat-icon ms-auto"></i>
    </div>
    <div class="card-stat orange">
        <div class="stat-info">
            <h6>TOTAL AGENCY</h6>
            <h3>12</h3>
            <small>Customer</small>
        </div>
        <i class="bi bi-journal-bookmark-fill stat-icon ms-auto"></i>
    </div>
</div>

<div class="table-placeholder">
    <h6 style="font-weight:700; font-size:14px; text-transform:uppercase;">REKAP BILLING 1 - 6 PER DATEL</h6>
    <div style="margin-top:24px; padding:24px; background:#f8f9fa; border:2px dashed #cbd5e1; border-radius:8px; text-align:center; color:#64748b;">
        <i class="bi bi-table" style="font-size:32px; display:block; margin-bottom:12px; color:#94a3b8;"></i>
        Tabel akan ditampilkan di sini nanti sesuai dengan desain.
    </div>
</div>

@endsection