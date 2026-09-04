@extends('layouts.app')

@section('title', 'BILL 1-2 AGENCY')

@section('content')

<style>
    /* ============================================================
       GLOBAL
    ============================================================ */

    .container-fluid {
        overflow-x: hidden !important;
        padding-left: 8px !important;
        padding-right: 8px !important;
    }

    .row {
        margin-left: -4px !important;
        margin-right: -4px !important;
    }

    .row > * {
        padding-left: 4px !important;
        padding-right: 4px !important;
    }

    .card-body {
        padding: 15px !important;
    }

    .card-header {
        padding: 12px 15px !important;
    }

    .card-header h6 {
        font-size: 0.9rem !important;
    }

    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .table-responsive table {
        min-width: 1000px !important;
        width: 100% !important;
    }

    .table-responsive table th,
    .table-responsive table td {
        white-space: nowrap !important;
        padding: 5px 8px !important;
        font-size: 0.72rem !important;
        vertical-align: middle !important;
    }

    .badge {
        font-size: 0.65rem !important;
        padding: 4px 8px !important;
    }

    .form-label {
        font-size: 0.75rem !important;
        margin-bottom: 3px !important;
    }

    .form-control,
    .form-select {
        font-size: 0.8rem !important;
        padding: 5px 10px !important;
    }

    .btn-sm {
        font-size: 0.75rem !important;
        padding: 5px 12px !important;
    }

    .summary-card .card-body {
        padding: 8px 12px !important;
    }

    .summary-card h5 {
        font-size: 1rem !important;
        margin-bottom: 0 !important;
    }

    .summary-card small {
        font-size: 0.65rem !important;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6 !important;
    }

    .bg-agency {
        background-color: #f8f9fa;
    }

    .fw-600 {
        font-weight: 600;
    }


    /* ============================================================
       BILLING FILTER
    ============================================================ */

    .billing-filter {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-bottom: 25px;
    }

    .billing-filter-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        background-color: #fff;
        color: #495057;
        font-size: 0.78rem;
        font-weight: 500;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }

    .billing-radio {
        width: 14px;
        height: 14px;
        border: 1.5px solid #6c757d;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .billing-filter-item.active .billing-radio::after {
        content: "";
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: currentColor;
    }

    .billing-filter-item.active {
        background-color: #e9ecef;
        border-color: #ced4da;
        color: #2f3a4a;
    }

    .billing-filter-item.billing-1.active,
    .billing-filter-item.billing-2.active {
        background-color: #e9ecef;
        border-color: #ced4da;
        color: #2f3a4a;
    }

    .billing-filter-item.active .billing-radio {
        border-color: currentColor;
    }

    .billing-filter-item:hover {
        border-color: #adb5bd;
        color: #212529;
    }

    .billing-filter-item.active:hover {
        color: inherit;
    }


    /* ============================================================
       SEARCH CUSTOMER
    ============================================================ */

    .customer-search-input {
        font-size: 0.8rem !important;
    }

    .customer-search-button {
        background-color: #0b5ed7 !important;
        font-size: 0.8rem !important;
    }

    .customer-search-button:hover {
        background-color: #0a58ca !important;
    }

    .search-result-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        overflow: hidden;
    }

    .search-result-title {
        font-size: 0.75rem !important;
        letter-spacing: 0.2px;
    }

    .search-result-table {
        min-width: 1000px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }

    .search-result-table th,
    .search-result-table td {
        padding: 5px 8px !important;
        font-size: 0.7rem !important;
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .search-result-table thead th {
        font-weight: 600 !important;
        color: #495057;
        background-color: #f8f9fa !important;
    }

    .search-customer-name {
        color: #000361;
        font-weight: 600;
    }


    /* ============================================================
       BILLING TABLE
    ============================================================ */

    .billing-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        overflow: hidden;
        background: #ffffff;
    }

    .billing-title {
        font-size: 0.9rem !important;
        line-height: 1.3;
        letter-spacing: 0.3px;
    }

    .billing-table {
        width: 100% !important;
        min-width: 1000px !important;
        margin-bottom: 0 !important;
    }

    .billing-table th,
    .billing-table td {
        white-space: nowrap !important;
        padding: 5px 8px !important;
        font-size: 0.72rem !important;
        vertical-align: middle !important;
        border: 1px solid #dee2e6 !important;
    }

    .billing-table thead th {
        font-weight: 600 !important;
    }

    .billing-table tbody td {
        background-color: #ffffff;
    }

    .billing-table tbody tr:nth-child(even) td {
        background-color: #f8f9fa;
    }

    .billing-table tbody tr:hover td {
        background-color: #f1f5f9 !important;
    }


    /* ============================================================
       HEADER BILLING
    ============================================================ */

    .billing-header {
        background-color: #176B87 !important;
        color: white !important;
        font-weight: 600 !important;
        text-align: center;
        vertical-align: middle !important;
    }

    .billing-header.billing-1-header {
        background-color: #E2001A !important;
        color: white !important;
    }

    .billing-header.billing-2-header {
        background-color: #2F3A4A !important;
        color: white !important;
    }

    .billing-header.billing-total-header {
        background-color: #28a745 !important;
        color: white !important;
    }


    /* ============================================================
       LINK SSL
    ============================================================ */

    .ssl-link {
        color: #212529 !important;
        text-decoration: none !important;
        font-weight: bold;
        transition: all 0.15s ease-in-out;
    }

    .ssl-link:hover {
        color: #0b5ed7 !important;
        text-decoration: underline !important;
    }

    .billing-link {
        color: #0b5ed7 !important;
        text-decoration: underline !important;
        font-weight: bold !important;
    }

    .billing-link:hover {
        color: #084298 !important;
    }


    /* ============================================================
       GRAND TOTAL
    ============================================================ */

    .billing-footer {
        background-color: #176B87 !important;
        color: white !important;
        font-weight: bold !important;
    }

    .billing-footer td {
        background-color: #176B87 !important;
        color: white !important;
    }

    .billing-footer-link {
        color: white !important;
        text-decoration: underline !important;
        font-weight: bold !important;
    }


    /* ============================================================
       PAGINATION
    ============================================================ */

    .billing-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        padding: 0 5px;
        width: 100%;
        gap: 20px;
    }

    .billing-pagination-info {
        font-size: 0.75rem;
        color: #6c757d;
        white-space: nowrap;
    }

    .billing-pagination-nav {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-shrink: 0;
    }

    .billing-page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 4px 9px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background-color: #fff;
        color: #495057;
        font-size: 0.72rem;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }

    .billing-page-link:hover {
        background-color: #f1f5f9;
        border-color: #adb5bd;
        color: #212529;
    }

    .billing-page-link.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .billing-page-link.disabled {
        background-color: #f8f9fa;
        color: #adb5bd;
        border-color: #dee2e6;
        cursor: not-allowed;
    }


    /* ============================================================
       RESPONSIVE
    ============================================================ */

    @media (max-width: 768px) {

        .billing-filter {
            gap: 5px;
            flex-wrap: wrap;
        }

        .billing-filter-item {
            padding: 6px 11px;
            font-size: 0.72rem;
        }

        .billing-radio {
            width: 13px;
            height: 13px;
        }

        .billing-pagination {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .billing-pagination-info {
            text-align: center;
        }

        .billing-pagination-nav {
            justify-content: center;
            flex-wrap: wrap;
        }

        .table-responsive table {
            min-width: 900px !important;
        }
    }
</style>


<div class="container-fluid">

    {{-- ============================================================
        FILTER PILIH TABEL
    ============================================================= --}}

    <div class="billing-filter">

        {{-- BILLING 1 & 2 --}}
        <a
            href="{{ route('rekap.agency', [
                'view' => 'rekap',
                'agency_psb' => request('agency_psb'),
                'sales_agency' => request('sales_agency'),
                'search' => request('search')
            ]) }}"
            class="billing-filter-item
                {{ request('view', 'rekap') == 'rekap' ? 'active' : '' }}"
        >
            <span class="billing-radio"></span>
            <span>Billing 1 & 2</span>
        </a>


        {{-- BILLING KE-1 --}}
        <a
            href="{{ route('rekap.agency', [
                'view' => 'billing1'
            ]) }}"
            class="billing-filter-item billing-1
                {{ request('view') == 'billing1' ? 'active' : '' }}"
        >
            <span class="billing-radio"></span>
            <span>Billing Ke-1</span>
        </a>


        {{-- BILLING KE-2 --}}
        <a
            href="{{ route('rekap.agency', [
                'view' => 'billing2'
            ]) }}"
            class="billing-filter-item billing-2
                {{ request('view') == 'billing2' ? 'active' : '' }}"
        >
            <span class="billing-radio"></span>
            <span>Billing Ke-2</span>
        </a>

    </div>


    {{-- ============================================================
        BILLING 1 & 2
    ============================================================= --}}

    @if(request('view', 'rekap') == 'rekap')

        <div
            class="card border-0 shadow-sm"
            style="
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px;
                overflow: hidden;
            "
        >

            {{-- JUDUL --}}
            <div
                class="card-header bg-white border-0
                       d-flex justify-content-center
                       align-items-center flex-wrap gap-2"
            >

                <h6 class="mb-0 fw-bold text-primary-custom text-center">

                    PENYELESAIAN BILLING 1 DAN 2
                    <br>
                    AGENCY WITEL PRIANGAN TIMUR

                </h6>

            </div>


            <div class="card-body">


                {{-- ====================================================
                    FORM FILTER DAN SEARCH
                ===================================================== --}}

                <form
                    method="GET"
                    action="{{ route('rekap.agency') }}"
                    class="row g-2 mb-3 p-3 bg-light rounded-3"
                    style="border: 1px solid #f1f5f9;"
                >

                    {{-- TETAP BILLING 1 & 2 --}}
                    <input
                        type="hidden"
                        name="view"
                        value="rekap"
                    >


                    {{-- ====================================================
                        SEARCH CUSTOMER
                    ===================================================== --}}

                    <div class="col-12">

                        <div class="row g-2 align-items-end">

                            <div class="col-md-5 col-lg-4">

                                <label
                                    for="filter-search"
                                    class="form-label mb-1 text-primary fw-bold text-nowrap"
                                    style="
                                        font-size: 0.75rem;
                                        letter-spacing: 0.5px;
                                    "
                                >
                                    PENCARIAN CUSTOMER (NAMA/SND/DLL)
                                </label>

                                <input
                                    type="text"
                                    name="search"
                                    id="filter-search"
                                    class="form-control form-control-sm
                                           border-light-subtle text-muted
                                           customer-search-input"
                                    placeholder="Cari nama, SND, NCLI, dll..."
                                    value="{{ request('search') }}"
                                    autocomplete="off"
                                >

                            </div>


                            {{-- BUTTON SEARCH --}}
                            <div class="col-md-2 col-lg-2">

                                <button
                                    type="submit"
                                    class="btn btn-sm text-white w-100 shadow-sm
                                           d-flex align-items-center
                                           justify-content-center
                                           customer-search-button"
                                >

                                    <i class="bi bi-search me-1"></i>
                                    Cari

                                </button>

                            </div>

                        </div>

                    </div>


                    {{-- ====================================================
                        FILTER AGENCY
                    ===================================================== --}}

                    <div class="col-md-4 col-sm-6">

                        <label class="form-label fw-bold text-secondary">
                            Agency
                        </label>

                        <select
                            name="agency_psb"
                            class="form-select form-select-sm"
                        >

                            <option value="">
                                Semua Agency
                            </option>

                            @foreach($filters['agency_psb'] as $agency)

                                <option
                                    value="{{ $agency }}"
                                    {{ request('agency_psb') == $agency ? 'selected' : '' }}
                                >
                                    {{ $agency }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- ====================================================
                        FILTER SALES AGENCY
                    ===================================================== --}}

                    <div class="col-md-4 col-sm-6">

                        <label class="form-label fw-bold text-secondary">
                            Sales Agency
                        </label>

                        <select
                            name="sales_agency"
                            class="form-select form-select-sm"
                        >

                            <option value="">
                                Semua Sales
                            </option>

                            @foreach($filters['sales_agency'] as $sales)

                                <option
                                    value="{{ $sales }}"
                                    {{ request('sales_agency') == $sales ? 'selected' : '' }}
                                >
                                    {{ $sales }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- ====================================================
                        BUTTON FILTER
                    ===================================================== --}}

                    <div class="col-md-2 col-sm-6 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary btn-sm w-100"
                            style="border-radius: 8px;"
                        >

                            <i class="bi bi-funnel me-1"></i>
                            Filter

                        </button>

                    </div>


                    {{-- ====================================================
                        RESET
                    ===================================================== --}}

                    <div class="col-md-2 col-sm-6 d-flex align-items-end">

                        <a
                            href="{{ route('rekap.agency', [
                                'view' => 'rekap'
                            ]) }}"
                            class="btn btn-secondary btn-sm w-100"
                            style="border-radius: 8px;"
                        >

                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset

                        </a>

                    </div>

                </form>


                {{-- ============================================================
                    SUMMARY
                ============================================================= --}}

                <div class="row g-2 mb-4">

                    {{-- TOTAL CUSTOMER --}}
                    <div class="col-md-3 col-6">

                        <div
                            class="card bg-light border-0
                                   summary-card rounded-3"
                            style="
                                border: 1px solid #e2e8f0 !important;
                            "
                        >

                            <div class="card-body text-center">

                                <small class="text-muted">
                                    Total Customer (Belum Bayar)
                                </small>

                                <h5 class="mb-0 fw-bold mt-1 text-dark">

                                    {{
                                        number_format(
                                            $summary['total_customer'] ?? 0
                                        )
                                    }}

                                </h5>

                            </div>

                        </div>

                    </div>


                    {{-- SUDAH BAYAR --}}
                    <div class="col-md-3 col-6">

                        <div
                            class="card bg-success bg-opacity-10
                                   border-0 summary-card rounded-3"
                            style="
                                border: 1px solid #d1e7dd !important;
                            "
                        >

                            <div class="card-body text-center">

                                <small class="text-success">
                                    Sudah Bayar
                                </small>

                                <h5 class="mb-0 text-success fw-bold mt-1">

                                    {{
                                        number_format(
                                            $summary['total_sudah_bayar'] ?? 0
                                        )
                                    }}

                                </h5>

                            </div>

                        </div>

                    </div>


                    {{-- BELUM BAYAR --}}
                    <div class="col-md-3 col-6">

                        <div
                            class="card bg-danger bg-opacity-10
                                   border-0 summary-card rounded-3"
                            style="
                                border: 1px solid #f8d7da !important;
                            "
                        >

                            <div class="card-body text-center">

                                <small class="text-danger">
                                    Belum Bayar
                                </small>

                                <h5 class="mb-0 text-danger fw-bold mt-1">

                                    {{
                                        number_format(
                                            $summary['total_belum_bayar'] ?? 0
                                        )
                                    }}

                                </h5>

                            </div>

                        </div>

                    </div>


                    {{-- TOTAL SALDO --}}
                    <div class="col-md-3 col-6">

                        <div
                            class="card bg-info bg-opacity-10
                                   border-0 summary-card rounded-3"
                            style="
                                border: 1px solid #cff4fc !important;
                            "
                        >

                            <div class="card-body text-center">

                                <small class="text-info">
                                    Total Saldo
                                </small>

                                <h5 class="mb-0 text-info fw-bold mt-1">

                                    Rp
                                    {{
                                        number_format(
                                            $summary['total_saldo'] ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}

                                </h5>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ============================================================
                    TABEL REKAP BILLING 1 & 2
                    AGENCY PSB + SALES AGENCY
                ============================================================= --}}

                <div class="table-responsive">

                    <table
                        id="agencyBillingTable"
                        class="table table-bordered table-hover
                               table-striped mb-0"
                    >

                        <thead class="table-dark">

                            <tr>

                                <th
                                    rowspan="2"
                                    class="text-center"
                                    style="
                                        vertical-align:middle;
                                        min-width:45px;
                                    "
                                >
                                    No
                                </th>

                                <th
                                    rowspan="2"
                                    style="
                                        vertical-align:middle;
                                        min-width:200px;
                                    "
                                >
                                    Agency PSB
                                </th>

                                <th
                                    rowspan="2"
                                    style="
                                        vertical-align:middle;
                                        min-width:150px;
                                    "
                                >
                                    Sales Agency
                                </th>

                                <th
                                    colspan="2"
                                    class="text-center"
                                    style="
                                        background-color:#E2001A;
                                        color:white;
                                    "
                                >
                                    Billing 1
                                </th>

                                <th
                                    colspan="2"
                                    class="text-center"
                                    style="
                                        background-color:#2F3A4A;
                                        color:white;
                                    "
                                >
                                    Billing 2
                                </th>

                                <th
                                    colspan="2"
                                    class="text-center"
                                    style="
                                        background-color:#28a745;
                                        color:white;
                                    "
                                >
                                    Total
                                </th>

                            </tr>


                            <tr>

                                <th
                                    class="text-center"
                                    style="
                                        background-color:#E2001A;
                                        color:white;
                                        width:80px;
                                    "
                                >
                                    SSL
                                </th>

                                <th
                                    class="text-end"
                                    style="
                                        background-color:#E2001A;
                                        color:white;
                                        width:120px;
                                    "
                                >
                                    Saldo
                                </th>

                                <th
                                    class="text-center"
                                    style="
                                        background-color:#2F3A4A;
                                        color:white;
                                        width:80px;
                                    "
                                >
                                    SSL
                                </th>

                                <th
                                    class="text-end"
                                    style="
                                        background-color:#2F3A4A;
                                        color:white;
                                        width:120px;
                                    "
                                >
                                    Saldo
                                </th>

                                <th
                                    class="text-center"
                                    style="
                                        background-color:#28a745;
                                        color:white;
                                        width:80px;
                                    "
                                >
                                    SSL
                                </th>

                                <th
                                    class="text-end"
                                    style="
                                        background-color:#28a745;
                                        color:white;
                                        width:120px;
                                    "
                                >
                                    Saldo
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @php

                                $grandTotal1Ssl = 0;
                                $grandTotal1Saldo = 0;

                                $grandTotal2Ssl = 0;
                                $grandTotal2Saldo = 0;

                                $grandTotalSsl = 0;
                                $grandTotalSaldo = 0;

                                $lastAgency = null;

                            @endphp


                            @forelse($rekap as $index => $item)

                                @php

                                    $isSameAgency =
                                        ($lastAgency === $item->agency_psb);

                                    $grandTotal1Ssl +=
                                        $item->billing_1_ssl ?? 0;

                                    $grandTotal1Saldo +=
                                        $item->billing_1_saldo ?? 0;

                                    $grandTotal2Ssl +=
                                        $item->billing_2_ssl ?? 0;

                                    $grandTotal2Saldo +=
                                        $item->billing_2_saldo ?? 0;

                                    $grandTotalSsl +=
                                        $item->total_ssl ?? 0;

                                    $grandTotalSaldo +=
                                        $item->total_saldo ?? 0;

                                @endphp


                                <tr>

                                    <td class="text-center">
                                        {{ $rekap->firstItem() + $index }}
                                    </td>


                                    <td>

                                        @if(!$isSameAgency)

                                            <strong>
                                                {{ $item->agency_psb }}
                                            </strong>

                                        @endif

                                    </td>


                                    <td>
                                        {{ $item->sales_agency ?? '-' }}
                                    </td>


                                    {{-- BILLING 1 --}}

                                    <td class="text-center">

                                        @if(($item->billing_1_ssl ?? 0) > 0)

                                            <a
                                                href="{{ route('billing.detail', [
                                                    'billing_ke' => 1,
                                                    'agency' => $item->agency_psb,
                                                    'sales' => $item->sales_agency,
                                                    'status' => 'Blm Bayar'
                                                ]) }}"
                                                class="ssl-link"
                                            >

                                                {{
                                                    number_format(
                                                        $item->billing_1_ssl
                                                    )
                                                }}

                                            </a>

                                        @else

                                            0

                                        @endif

                                    </td>


                                    <td class="text-end">

                                        Rp
                                        {{
                                            number_format(
                                                $item->billing_1_saldo ?? 0,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}

                                    </td>


                                    {{-- BILLING 2 --}}

                                    <td class="text-center">

                                        @if(($item->billing_2_ssl ?? 0) > 0)

                                            <a
                                                href="{{ route('billing.detail', [
                                                    'billing_ke' => 2,
                                                    'agency' => $item->agency_psb,
                                                    'sales' => $item->sales_agency,
                                                    'status' => 'Blm Bayar'
                                                ]) }}"
                                                class="ssl-link"
                                            >

                                                {{
                                                    number_format(
                                                        $item->billing_2_ssl
                                                    )
                                                }}

                                            </a>

                                        @else

                                            0

                                        @endif

                                    </td>


                                    <td class="text-end">

                                        Rp
                                        {{
                                            number_format(
                                                $item->billing_2_saldo ?? 0,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}

                                    </td>


                                    {{-- TOTAL --}}

                                    <td class="text-center fw-bold">

                                        {{
                                            number_format(
                                                $item->total_ssl ?? 0
                                            )
                                        }}

                                    </td>


                                    <td class="text-end fw-bold">

                                        Rp
                                        {{
                                            number_format(
                                                $item->total_saldo ?? 0,
                                                0,
                                                ',',
                                                '.'
                                            )
                                        }}

                                    </td>

                                </tr>


                                @php
                                    $lastAgency = $item->agency_psb;
                                @endphp

                            @empty

                                <tr>

                                    <td
                                        colspan="9"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="bi bi-inbox fs-3
                                                   text-muted d-block mb-1"
                                        ></i>

                                        <span
                                            class="text-muted"
                                            style="font-size:0.85rem;"
                                        >
                                            Tidak ada data agency
                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        {{-- GRAND TOTAL --}}

                        <tfoot class="table-secondary fw-bold">

                            <tr>

                                <td
                                    colspan="3"
                                    class="text-end"
                                >
                                    GRAND TOTAL
                                </td>


                                <td class="text-center">

                                    {{ number_format($grandTotal1Ssl) }}

                                </td>


                                <td class="text-end">

                                    Rp
                                    {{
                                        number_format(
                                            $grandTotal1Saldo,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}

                                </td>


                                <td class="text-center">

                                    {{ number_format($grandTotal2Ssl) }}

                                </td>


                                <td class="text-end">

                                    Rp
                                    {{
                                        number_format(
                                            $grandTotal2Saldo,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}

                                </td>


                                <td class="text-center">

                                    {{ number_format($grandTotalSsl) }}

                                </td>


                                <td class="text-end">

                                    Rp
                                    {{
                                        number_format(
                                            $grandTotalSaldo,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}

                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>


                {{-- ============================================================
                    PAGINATION REKAP AGENCY
                ============================================================= --}}

                <div class="billing-pagination">

                    <div class="billing-pagination-info">

                        Menampilkan

                        <strong>
                            {{ $rekap->firstItem() ?? 0 }}
                        </strong>

                        -

                        <strong>
                            {{ $rekap->lastItem() ?? 0 }}
                        </strong>

                        dari

                        <strong>
                            {{ number_format($rekap->total()) }}
                        </strong>

                        data

                    </div>


                    <div class="billing-pagination-nav">

                        {{-- PREVIOUS --}}

                        @if($rekap->onFirstPage())

                            <span class="billing-page-link disabled">
                                <i class="bi bi-chevron-left"></i>
                            </span>

                        @else

                            <a
                                href="{{
                                    $rekap
                                        ->appends(request()->query())
                                        ->previousPageUrl()
                                }}"
                                class="billing-page-link"
                            >
                                <i class="bi bi-chevron-left"></i>
                            </a>

                        @endif


                        {{-- NOMOR HALAMAN --}}

                        @foreach(
                            $rekap->getUrlRange(
                                max(1, $rekap->currentPage() - 2),
                                min(
                                    $rekap->lastPage(),
                                    $rekap->currentPage() + 2
                                )
                            )
                            as $page => $url
                        )

                            @if($page == $rekap->currentPage())

                                <span class="billing-page-link active">
                                    {{ $page }}
                                </span>

                            @else

                                <a
                                    href="{{
                                        $url . '&' .
                                        http_build_query(
                                            request()->except('page')
                                        )
                                    }}"
                                    class="billing-page-link"
                                >
                                    {{ $page }}
                                </a>

                            @endif

                        @endforeach


                        {{-- NEXT --}}

                        @if($rekap->hasMorePages())

                            <a
                                href="{{
                                    $rekap
                                        ->appends(request()->query())
                                        ->nextPageUrl()
                                }}"
                                class="billing-page-link"
                            >
                                <i class="bi bi-chevron-right"></i>
                            </a>

                        @else

                            <span class="billing-page-link disabled">
                                <i class="bi bi-chevron-right"></i>
                            </span>

                        @endif

                    </div>

                </div>


                {{-- ============================================================
                    HASIL PENCARIAN CUSTOMER BELUM LUNAS
                    POSISI: DI BAWAH TABEL AGENCY PSB
                ============================================================= --}}

                @if(request()->filled('search'))

                    <div class="card search-result-card shadow-sm mt-4 mb-4">

                        {{-- HEADER --}}

                        <div class="card-header bg-white border-0">

                            <h6
                                class="mb-0 fw-bold text-primary
                                       search-result-title"
                            >

                                HASIL PENCARIAN CUSTOMER BELUM LUNAS -
                                "{{ request('search') }}"

                            </h6>

                        </div>


                        {{-- BODY --}}

                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table
                                    class="table table-bordered
                                           table-hover table-striped
                                           search-result-table"
                                >

                                    <thead>

                                        <tr>

                                            <th class="text-center">
                                                NO
                                            </th>

                                            <th>
                                                SND
                                            </th>

                                            <th>
                                                CUSTOMER
                                            </th>

                                            <th>
                                                DATEL
                                            </th>

                                            <th>
                                                AGENCY PSB
                                            </th>

                                            <th>
                                                BILLING
                                            </th>

                                            <th class="text-end">
                                                TAGIHAN
                                            </th>

                                            <th class="text-end">
                                                SALDO
                                            </th>

                                            <th class="text-center">
                                                STATUS
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @forelse(
                                            $searchCustomers ?? []
                                            as $index => $customer
                                        )

                                            <tr>

                                                {{-- NO --}}

                                                <td class="text-center">

                                                    {{
                                                        ($searchCustomers->firstItem() ?? 1)
                                                        + $index
                                                    }}

                                                </td>


                                                {{-- SND --}}

                                                <td>

                                                    {{ $customer->snd ?? '-' }}

                                                </td>


                                                {{-- CUSTOMER --}}

                                                <td class="search-customer-name">

                                                    {{ $customer->nama ?? '-' }}

                                                </td>


                                                {{-- DATEL --}}

                                                <td>

                                                    {{ $customer->datel ?? '-' }}

                                                </td>


                                                {{-- AGENCY --}}

                                                <td>

                                                    {{ $customer->agency_psb ?? '-' }}

                                                </td>


                                                {{-- BILLING --}}

                                                <td>

                                                    Billing
                                                    {{ $customer->billing_ke ?? '-' }}

                                                </td>


                                                {{-- TAGIHAN --}}

                                                <td class="text-end">

                                                    <span
                                                        class="text-muted"
                                                        style="
                                                            font-size:0.75em;
                                                        "
                                                    >
                                                        Rp
                                                    </span>

                                                    {{
                                                        number_format(
                                                            $customer->tag_total ?? 0,
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                    }}

                                                </td>


                                                {{-- SALDO --}}

                                                <td class="text-end">

                                                    <span
                                                        class="text-muted"
                                                        style="
                                                            font-size:0.75em;
                                                        "
                                                    >
                                                        Rp
                                                    </span>

                                                    {{
                                                        number_format(
                                                            $customer->saldo ?? 0,
                                                            0,
                                                            ',',
                                                            '.'
                                                        )
                                                    }}

                                                </td>


                                                {{-- STATUS --}}

                                                <td class="text-center">

                                                    <span
                                                        class="badge bg-danger"
                                                    >
                                                        {{
                                                            $customer->status_bayar
                                                            ?? 'Belum Bayar'
                                                        }}
                                                    </span>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td
                                                    colspan="9"
                                                    class="text-center py-5"
                                                >

                                                    <i
                                                        class="bi bi-search fs-3
                                                               text-muted
                                                               d-block mb-2"
                                                    ></i>

                                                    <span
                                                        class="text-muted"
                                                        style="
                                                            font-size:0.85rem;
                                                        "
                                                    >

                                                        Tidak ada customer
                                                        yang sesuai dengan
                                                        pencarian
                                                        "{{ request('search') }}"

                                                    </span>

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>


                            {{-- =================================================
                                PAGINATION CUSTOMER
                            ================================================== --}}

                            @if(
                                isset($searchCustomers) &&
                                $searchCustomers instanceof
                                \Illuminate\Pagination\LengthAwarePaginator
                            )

                                <div
                                    class="d-flex
                                           justify-content-between
                                           align-items-center
                                           px-3 py-2"
                                >

                                    <small
                                        class="text-muted"
                                        style="font-size:0.7rem;"
                                    >

                                        Menampilkan

                                        <strong>
                                            {{
                                                $searchCustomers->firstItem()
                                                ?? 0
                                            }}
                                        </strong>

                                        -

                                        <strong>
                                            {{
                                                $searchCustomers->lastItem()
                                                ?? 0
                                            }}
                                        </strong>

                                        dari

                                        <strong>
                                            {{
                                                number_format(
                                                    $searchCustomers->total()
                                                )
                                            }}
                                        </strong>

                                        data

                                    </small>


                                    <div>

                                        {{
                                            $searchCustomers
                                                ->appends(
                                                    request()->query()
                                                )
                                                ->links()
                                        }}

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                @endif

            </div>

        </div>

    @endif


    {{-- ============================================================
        BILLING KE-1
    ============================================================= --}}

    @if(request('view') == 'billing1')

        <div
            class="card border-0 shadow-sm mt-4"
            style="
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px;
                overflow: hidden;
            "
        >

            <div
                class="card-header bg-white border-0 text-center"
                style="padding: 15px !important;"
            >

                <h6
                    class="mb-1 fw-bold text-primary-custom"
                    style="font-size:0.9rem;"
                >
                    BILLING KE - 1
                </h6>

                <h6
                    class="mb-0 fw-bold text-primary-custom"
                    style="font-size:0.9rem;"
                >
                    AGENCY WITEL PRIANGAN TIMUR
                </h6>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover
                               table-striped mb-0"
                        style="
                            min-width:1000px !important;
                            width:100%;
                        "
                    >

                        <thead>

                            <tr>

                                <th
                                    class="text-center"
                                    style="
                                        background-color:#176B87;
                                        color:white;
                                        min-width:45px;
                                        vertical-align:middle;
                                    "
                                >
                                    No
                                </th>

                                <th
                                    class="text-start"
                                    style="
                                        background-color:#176B87;
                                        color:white;
                                        min-width:200px;
                                        vertical-align:middle;
                                    "
                                >
                                    Agency
                                </th>

                                @foreach($witelDatels as $datel)

                                    <th
                                        class="text-center"
                                        style="
                                            background-color:#176B87;
                                            color:white;
                                            min-width:120px;
                                            vertical-align:middle;
                                        "
                                    >
                                        {{ $datel }}
                                    </th>

                                @endforeach

                                <th
                                    class="text-center"
                                    style="
                                        background-color:#176B87;
                                        color:white;
                                        min-width:120px;
                                        vertical-align:middle;
                                    "
                                >
                                    Grand Total
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @php

                                $colTotals = [];

                                foreach($witelDatels as $datel) {
                                    $colTotals[$datel] = 0;
                                }

                                $grandTotalAll = 0;
                                $no = 1;

                            @endphp


                            @forelse($witelAgencies as $agency)

                                @php
                                    $rowTotal = 0;
                                @endphp


                                <tr>

                                    <td class="text-center">
                                        {{ $no++ }}
                                    </td>


                                    <td class="fw-bold">
                                        {{ $agency }}
                                    </td>


                                    @foreach($witelDatels as $datel)

                                        @php

                                            $val =
                                                $witelData[$agency][$datel]
                                                ?? 0;

                                            $rowTotal += $val;

                                            $colTotals[$datel] += $val;

                                            $grandTotalAll += $val;

                                        @endphp


                                        <td class="text-center">

                                            @if($val > 0)

                                                <a
                                                    href="{{ route(
                                                        'billing.detail',
                                                        [
                                                            'billing_ke' => 1,
                                                            'agency' => $agency,
                                                            'datel' => $datel,
                                                            'status' => 'Blm Bayar'
                                                        ]
                                                    ) }}"
                                                    class="ssl-link"
                                                >

                                                    {{ number_format($val) }}

                                                </a>

                                            @else

                                                0

                                            @endif

                                        </td>

                                    @endforeach


                                    <td class="text-center fw-bold">

                                        @if($rowTotal > 0)

                                            <a
                                                href="{{ route(
                                                    'billing.detail',
                                                    [
                                                        'billing_ke' => 1,
                                                        'agency' => $agency,
                                                        'status' => 'Blm Bayar'
                                                    ]
                                                ) }}"
                                                class="ssl-link"
                                            >

                                                {{ number_format($rowTotal) }}

                                            </a>

                                        @else

                                            0

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="{{ count($witelDatels) + 3 }}"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="bi bi-inbox fs-3
                                                   text-muted d-block mb-1"
                                        ></i>

                                        <span
                                            class="text-muted"
                                            style="font-size:0.85rem;"
                                        >
                                            Tidak ada data belum bayar
                                            untuk Billing Ke-1 Agency
                                            Witel Priangan Timur
                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        @if(count($witelAgencies) > 0)

                            <tfoot>

                                <tr class="fw-bold">

                                    <td
                                        colspan="2"
                                        class="text-end"
                                        style="
                                            background-color:#176B87;
                                            color:white;
                                        "
                                    >
                                        GRAND TOTAL
                                    </td>


                                    @foreach($witelDatels as $datel)

                                        <td
                                            class="text-center"
                                            style="
                                                background-color:#176B87;
                                                color:white;
                                            "
                                        >

                                            @if(($colTotals[$datel] ?? 0) > 0)

                                                <a
                                                    href="{{ route(
                                                        'billing.detail',
                                                        [
                                                            'billing_ke' => 1,
                                                            'datel' => $datel,
                                                            'status' => 'Blm Bayar'
                                                        ]
                                                    ) }}"
                                                    class="text-white fw-bold"
                                                    style="
                                                        text-decoration:underline;
                                                    "
                                                >

                                                    {{
                                                        number_format(
                                                            $colTotals[$datel]
                                                        )
                                                    }}

                                                </a>

                                            @else

                                                0

                                            @endif

                                        </td>

                                    @endforeach


                                    <td
                                        class="text-center"
                                        style="
                                            background-color:#176B87;
                                            color:white;
                                        "
                                    >

                                        @if($grandTotalAll > 0)

                                            <a
                                                href="{{ route(
                                                    'billing.detail',
                                                    [
                                                        'billing_ke' => 1,
                                                        'status' => 'Blm Bayar'
                                                    ]
                                                ) }}"
                                                class="text-white fw-bold"
                                                style="
                                                    text-decoration:underline;
                                                "
                                            >

                                                {{
                                                    number_format(
                                                        $grandTotalAll
                                                    )
                                                }}

                                            </a>

                                        @else

                                            0

                                        @endif

                                    </td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>

            </div>

        </div>

    @endif


    {{-- ============================================================
        BILLING KE-2
    ============================================================= --}}

    @if(request('view') == 'billing2')

        <div
            class="card border-0 shadow-sm mt-4"
            style="
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px;
                overflow: hidden;
            "
        >

            <div
                class="card-header bg-white border-0 text-center"
                style="padding:15px !important;"
            >

                <h6
                    class="mb-1 fw-bold text-primary-custom"
                    style="font-size:0.9rem;"
                >
                    BILLING KE - 2
                </h6>

                <h6
                    class="mb-0 fw-bold text-primary-custom"
                    style="font-size:0.9rem;"
                >
                    AGENCY WITEL PRIANGAN TIMUR
                </h6>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover
                               table-striped mb-0"
                        style="
                            min-width:1000px !important;
                            width:100%;
                        "
                    >

                        <thead>

                            <tr>

                                <th
                                    class="text-center"
                                    style="
                                        background-color:#176B87;
                                        color:white;
                                        min-width:45px;
                                        vertical-align:middle;
                                    "
                                >
                                    No
                                </th>

                                <th
                                    class="text-start"
                                    style="
                                        background-color:#176B87;
                                        color:white;
                                        min-width:200px;
                                        vertical-align:middle;
                                    "
                                >
                                    Agency
                                </th>

                                @foreach($witelDatels2 as $datel)

                                    <th
                                        class="text-center"
                                        style="
                                            background-color:#176B87;
                                            color:white;
                                            min-width:120px;
                                            vertical-align:middle;
                                        "
                                    >
                                        {{ $datel }}
                                    </th>

                                @endforeach

                                <th
                                    class="text-center"
                                    style="
                                        background-color:#176B87;
                                        color:white;
                                        min-width:120px;
                                        vertical-align:middle;
                                    "
                                >
                                    Grand Total
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @php

                                $colTotals2 = [];

                                foreach($witelDatels2 as $datel) {
                                    $colTotals2[$datel] = 0;
                                }

                                $grandTotalAll2 = 0;
                                $no2 = 1;

                            @endphp


                            @forelse($witelAgencies2 as $agency)

                                @php
                                    $rowTotal2 = 0;
                                @endphp


                                <tr>

                                    <td class="text-center">
                                        {{ $no2++ }}
                                    </td>


                                    <td class="fw-bold">
                                        {{ $agency }}
                                    </td>


                                    @foreach($witelDatels2 as $datel)

                                        @php

                                            $val =
                                                $witelData2[$agency][$datel]
                                                ?? 0;

                                            $rowTotal2 += $val;

                                            $colTotals2[$datel] += $val;

                                            $grandTotalAll2 += $val;

                                        @endphp


                                        <td class="text-center">

                                            @if($val > 0)

                                                <a
                                                    href="{{ route(
                                                        'billing.detail',
                                                        [
                                                            'billing_ke' => 2,
                                                            'agency' => $agency,
                                                            'datel' => $datel,
                                                            'status' => 'Blm Bayar'
                                                        ]
                                                    ) }}"
                                                    class="ssl-link"
                                                >

                                                    {{ number_format($val) }}

                                                </a>

                                            @else

                                                0

                                            @endif

                                        </td>

                                    @endforeach


                                    <td class="text-center fw-bold">

                                        @if($rowTotal2 > 0)

                                            <a
                                                href="{{ route(
                                                    'billing.detail',
                                                    [
                                                        'billing_ke' => 2,
                                                        'agency' => $agency,
                                                        'status' => 'Blm Bayar'
                                                    ]
                                                ) }}"
                                                class="ssl-link"
                                            >

                                                {{ number_format($rowTotal2) }}

                                            </a>

                                        @else

                                            0

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="{{ count($witelDatels2) + 3 }}"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="bi bi-inbox fs-3
                                                   text-muted d-block mb-1"
                                        ></i>

                                        <span
                                            class="text-muted"
                                            style="font-size:0.85rem;"
                                        >
                                            Tidak ada data belum bayar
                                            untuk Billing Ke-2 Agency
                                            Witel Priangan Timur
                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        @if(count($witelAgencies2) > 0)

                            <tfoot>

                                <tr class="fw-bold">

                                    <td
                                        colspan="2"
                                        class="text-end"
                                        style="
                                            background-color:#176B87;
                                            color:white;
                                        "
                                    >
                                        GRAND TOTAL
                                    </td>


                                    @foreach($witelDatels2 as $datel)

                                        <td
                                            class="text-center"
                                            style="
                                                background-color:#176B87;
                                                color:white;
                                            "
                                        >

                                            @if(($colTotals2[$datel] ?? 0) > 0)

                                                <a
                                                    href="{{ route(
                                                        'billing.detail',
                                                        [
                                                            'billing_ke' => 2,
                                                            'datel' => $datel,
                                                            'status' => 'Blm Bayar'
                                                        ]
                                                    ) }}"
                                                    class="text-white fw-bold"
                                                    style="
                                                        text-decoration:underline;
                                                    "
                                                >

                                                    {{
                                                        number_format(
                                                            $colTotals2[$datel]
                                                        )
                                                    }}

                                                </a>

                                            @else

                                                0

                                            @endif

                                        </td>

                                    @endforeach


                                    <td
                                        class="text-center"
                                        style="
                                            background-color:#176B87;
                                            color:white;
                                        "
                                    >

                                        @if($grandTotalAll2 > 0)

                                            <a
                                                href="{{ route(
                                                    'billing.detail',
                                                    [
                                                        'billing_ke' => 2,
                                                        'status' => 'Blm Bayar'
                                                    ]
                                                ) }}"
                                                class="text-white fw-bold"
                                                style="
                                                    text-decoration:underline;
                                                "
                                            >

                                                {{
                                                    number_format(
                                                        $grandTotalAll2
                                                    )
                                                }}

                                            </a>

                                        @else

                                            0

                                        @endif

                                    </td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>

            </div>

        </div>

    @endif

</div>

@endsection