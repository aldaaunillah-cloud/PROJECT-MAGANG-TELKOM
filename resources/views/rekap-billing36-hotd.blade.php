@extends('layouts.app')

@section('title', 'BILL 3-6 HOTD')

@section('content')

<style>

    /* ============================================================
       CONTAINER
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


    /* ============================================================
       CARD BILLING
    ============================================================ */

    .billing-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        overflow: hidden;
        background: #ffffff;
    }

    .billing-card .card-header {
        padding: 15px !important;
    }

    .billing-card .card-body {
        padding: 15px !important;
    }


    /* ============================================================
       JUDUL
    ============================================================ */

    .billing-title {
        font-size: 0.9rem !important;
        line-height: 1.3;
        letter-spacing: 0.3px;
    }


    /* ============================================================
       FILTER BILLING
    ============================================================ */

    .billing-filter {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .billing-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        padding: 7px 14px;

        border: 1px solid #dee2e6;
        border-radius: 20px;

        background-color: #ffffff;
        color: #495057;

        font-size: 0.78rem;
        font-weight: 500;

        text-decoration: none !important;

        cursor: pointer;

        transition: all 0.2s ease;
    }

    .billing-tab:hover {
        border-color: #adb5bd;
        color: #212529;
        background-color: #f8f9fa;
    }

    .billing-tab.active {
        background-color: #e9ecef;
        border-color: #ced4da;
        color: #2f3a4a;
    }


    /* ============================================================
       FILTER FORM
    ============================================================ */

    .billing-form {
        border: 1px solid #f1f5f9;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
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


    /* ============================================================
       TABLE
    ============================================================ */

    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .billing-table {
        width: 100% !important;
        min-width: 1400px !important;
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
        text-align: center;
        vertical-align: middle !important;
        color: white !important;
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
       HEADER WARNA BILLING
    ============================================================ */

    .billing-3-header {
        background-color: #E2001A !important;
        color: white !important;
    }

    .billing-4-header {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .billing-5-header {
        background-color: #343a40 !important;
        color: white !important;
    }

    .billing-6-header {
        background-color: #495057 !important;
        color: white !important;
    }

    .billing-total-header {
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
        font-weight: bold;
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

        background-color: #ffffff;
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
        color: #ffffff;
        font-weight: 600;
    }

    .billing-page-link.disabled {
        background-color: #f8f9fa;
        color: #adb5bd;
        border-color: #dee2e6;
        cursor: not-allowed;
    }


    /* ============================================================
       BILLING SECTION
    ============================================================ */

    .billing-section {
        width: 100%;
    }


    /* ============================================================
       RESPONSIVE
    ============================================================ */

    @media (max-width: 768px) {

        .billing-filter {
            gap: 5px;
        }

        .billing-tab {
            padding: 6px 11px;
            font-size: 0.72rem;
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

        .billing-table {
            min-width: 1200px !important;
        }

    }

</style>


<div class="container-fluid">

    {{-- ============================================================
        TAB PILIH BILLING
    ============================================================ --}}

    <div class="billing-filter">

        <button type="button"
                class="billing-tab active"
                data-target="billing36">

            <span>
                <i class="bi bi-record-circle me-1"></i>
                Billing 3 & 6
            </span>

        </button>


        <button type="button"
                class="billing-tab"
                data-target="billing3">

            <span>
                <i class="bi bi-circle me-1"></i>
                Billing Ke-3
            </span>

        </button>


        <button type="button"
                class="billing-tab"
                data-target="billing4">

            <span>
                <i class="bi bi-circle me-1"></i>
                Billing Ke-4
            </span>

        </button>


        <button type="button"
                class="billing-tab"
                data-target="billing5">

            <span>
                <i class="bi bi-circle me-1"></i>
                Billing Ke-5
            </span>

        </button>


        <button type="button"
                class="billing-tab"
                data-target="billing6">

            <span>
                <i class="bi bi-circle me-1"></i>
                Billing Ke-6
            </span>

        </button>

    </div>



    {{-- ============================================================
        BILLING 3 & 6
    ============================================================ --}}

    <div id="billing36" class="billing-section">

        <div class="billing-card card border-0 shadow-sm">

            {{-- ====================================================
                JUDUL
            ===================================================== --}}

            <div class="card-header bg-white border-0
                        d-flex justify-content-center
                        align-items-center flex-wrap gap-2">

                <h6 class="mb-0 fw-bold text-primary-custom text-center billing-title">

                    PENYELESAIAN BILLING 3 - 6

                    <br>

                    AGENCY WITEL PRIANGAN TIMUR

                </h6>

            </div>


            {{-- ====================================================
                FILTER + TABEL
            ===================================================== --}}

            <div class="card-body">

                {{-- FILTER --}}

                <form method="GET"
                      action="{{ route('rekap.agency.billing36') }}"
                      class="billing-form row g-2 mb-3">

                    {{-- AGENCY --}}

                    <div class="col-md-5 col-sm-6">

                        <label class="form-label fw-bold text-secondary">
                            Agency
                        </label>

                        <select name="agency_psb"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">

                            <option value="">
                                Semua Agency
                            </option>

                            @foreach($filters['agency_psb'] as $agency)

                                <option value="{{ $agency }}"
                                    {{ request('agency_psb') == $agency ? 'selected' : '' }}>

                                    {{ $agency }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- SALES AGENCY --}}

                    <div class="col-md-5 col-sm-6">

                        <label class="form-label fw-bold text-secondary">
                            Sales Agency
                        </label>

                        <select name="sales_agency"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">

                            <option value="">
                                Semua Sales
                            </option>

                            @foreach($filters['sales_agency'] as $sales)

                                <option value="{{ $sales }}"
                                    {{ request('sales_agency') == $sales ? 'selected' : '' }}>

                                    {{ $sales }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- RESET --}}

                    <div class="col-md-2 col-sm-12 d-flex align-items-end">

                        <a href="{{ route('rekap.agency.billing36') }}"
                           class="btn btn-secondary btn-sm w-100"
                           style="border-radius: 8px;">

                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset

                        </a>

                    </div>

                </form>



                {{-- ====================================================
                    TABEL BILLING 3 - 6
                ===================================================== --}}

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped mb-0 billing-table">

                        <thead>

                            <tr>

                                <th rowspan="2" 
                                    class="text-center" 
                                    style="min-width:50px; color:#000000 !important;"> 
                                
                                    NO 
                                
                                </th> 


                                <th rowspan="2" 
                                    style="min-width:220px; color:#000000 !important;"> 
                                
                                    AGENCY PSB 
                                
                                </th> 


                                <th rowspan="2" 
                                    style="min-width:200px; color:#000000 !important;"> 
                                
                                    SALES AGENCY 
                                
                                </th>


                                {{-- BILLING 3 --}}

                                <th colspan="2"
                                    class="billing-3-header">

                                    BILLING 3

                                </th>


                                {{-- BILLING 4 --}}

                                <th colspan="2"
                                    class="billing-4-header">

                                    BILLING 4

                                </th>


                                {{-- BILLING 5 --}}

                                <th colspan="2"
                                    class="billing-5-header">

                                    BILLING 5

                                </th>


                                {{-- BILLING 6 --}}

                                <th colspan="2"
                                    class="billing-6-header">

                                    BILLING 6

                                </th>


                                {{-- TOTAL --}}

                                <th colspan="2"
                                    class="billing-total-header">

                                    TOTAL

                                </th>

                            </tr>


                            <tr>

                                {{-- BILLING 3 --}}

                                <th class="billing-3-header">
                                    SSL
                                </th>

                                <th class="billing-3-header">
                                    SALDO
                                </th>


                                {{-- BILLING 4 --}}

                                <th class="billing-4-header">
                                    SSL
                                </th>

                                <th class="billing-4-header">
                                    SALDO
                                </th>


                                {{-- BILLING 5 --}}

                                <th class="billing-5-header">
                                    SSL
                                </th>

                                <th class="billing-5-header">
                                    SALDO
                                </th>


                                {{-- BILLING 6 --}}

                                <th class="billing-6-header">
                                    SSL
                                </th>

                                <th class="billing-6-header">
                                    SALDO
                                </th>


                                {{-- TOTAL --}}

                                <th class="billing-total-header">
                                    SSL
                                </th>

                                <th class="billing-total-header">
                                    SALDO
                                </th>

                            </tr>

                        </thead>


                        {{-- ====================================================
                            BODY
                        ===================================================== --}}

                        <tbody>

                            @php
                                $previousAgency = null;

                                $grandTotal3Ssl = 0;
                                $grandTotal3Saldo = 0;

                                $grandTotal4Ssl = 0;
                                $grandTotal4Saldo = 0;

                                $grandTotal5Ssl = 0;
                                $grandTotal5Saldo = 0;

                                $grandTotal6Ssl = 0;
                                $grandTotal6Saldo = 0;

                                $grandTotalSsl = 0;
                                $grandTotalSaldo = 0;
                            @endphp


                            @forelse($rekap as $index => $row)

                                @php

                                    $grandTotal3Ssl += $row->billing_3_ssl ?? 0;
                                    $grandTotal3Saldo += $row->billing_3_saldo ?? 0;

                                    $grandTotal4Ssl += $row->billing_4_ssl ?? 0;
                                    $grandTotal4Saldo += $row->billing_4_saldo ?? 0;

                                    $grandTotal5Ssl += $row->billing_5_ssl ?? 0;
                                    $grandTotal5Saldo += $row->billing_5_saldo ?? 0;

                                    $grandTotal6Ssl += $row->billing_6_ssl ?? 0;
                                    $grandTotal6Saldo += $row->billing_6_saldo ?? 0;

                                    $grandTotalSsl += $row->total_ssl ?? 0;
                                    $grandTotalSaldo += $row->total_saldo ?? 0;

                                @endphp


                                <tr>

                                    {{-- NO --}}

                                    <td class="text-center">

                                        {{ $rekap->firstItem() + $index }}

                                    </td>


                                    {{-- AGENCY --}}

                                    <td class="fw-semibold">

                                        @if($previousAgency !== $row->agency_psb)

                                            {{ $row->agency_psb }}

                                        @endif

                                    </td>


                                    {{-- SALES --}}

                                    <td>

                                        {{ $row->sales_agency ?? '-' }}

                                    </td>


                                    {{-- ====================================================
                                        BILLING 3
                                    ===================================================== --}}

                                    <td class="text-center fw-bold">

                                        {{ number_format($row->billing_3_ssl ?? 0) }}

                                    </td>


                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $row->billing_3_saldo ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- ====================================================
                                        BILLING 4
                                    ===================================================== --}}

                                    <td class="text-center fw-bold">

                                        {{ number_format($row->billing_4_ssl ?? 0) }}

                                    </td>


                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $row->billing_4_saldo ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- ====================================================
                                        BILLING 5
                                    ===================================================== --}}

                                    <td class="text-center fw-bold">

                                        {{ number_format($row->billing_5_ssl ?? 0) }}

                                    </td>


                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $row->billing_5_saldo ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- ====================================================
                                        BILLING 6
                                    ===================================================== --}}

                                    <td class="text-center fw-bold">

                                        {{ number_format($row->billing_6_ssl ?? 0) }}

                                    </td>


                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $row->billing_6_saldo ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- ====================================================
                                        TOTAL
                                    ===================================================== --}}

                                    <td class="text-center fw-bold">

                                        {{ number_format($row->total_ssl ?? 0) }}

                                    </td>


                                    <td class="text-end fw-bold">

                                        Rp
                                        {{ number_format(
                                            $row->total_saldo ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>

                                </tr>


                                @php
                                    $previousAgency = $row->agency_psb;
                                @endphp


                            @empty

                                <tr>

                                    <td colspan="13"
                                        class="text-center py-5">

                                        <i class="bi bi-inbox fs-3
                                                  text-muted d-block mb-1">
                                        </i>

                                        <span class="text-muted"
                                              style="font-size:0.85rem;">

                                            Tidak ada data Billing 3-6.

                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        {{-- ====================================================
                            GRAND TOTAL
                        ===================================================== --}}

                        @if($rekap->count() > 0)

                            <tfoot>

                                <tr class="billing-footer">

                                    <td colspan="3"
                                        class="text-end">

                                        GRAND TOTAL

                                    </td>


                                    {{-- BILLING 3 --}}

                                    <td class="text-center">

                                        {{ number_format($grandTotal3Ssl) }}

                                    </td>

                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $grandTotal3Saldo,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- BILLING 4 --}}

                                    <td class="text-center">

                                        {{ number_format($grandTotal4Ssl) }}

                                    </td>

                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $grandTotal4Saldo,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- BILLING 5 --}}

                                    <td class="text-center">

                                        {{ number_format($grandTotal5Ssl) }}

                                    </td>

                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $grandTotal5Saldo,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- BILLING 6 --}}

                                    <td class="text-center">

                                        {{ number_format($grandTotal6Ssl) }}

                                    </td>

                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $grandTotal6Saldo,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>


                                    {{-- TOTAL --}}

                                    <td class="text-center">

                                        {{ number_format($grandTotalSsl) }}

                                    </td>

                                    <td class="text-end">

                                        Rp
                                        {{ number_format(
                                            $grandTotalSaldo,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    </td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>



                {{-- ====================================================
                    PAGINATION
                ===================================================== --}}

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

                        @if ($rekap->onFirstPage())

                            <span class="billing-page-link disabled">

                                <i class="bi bi-chevron-left"></i>

                            </span>

                        @else

                            <a href="{{ $rekap->appends(request()->query())->previousPageUrl() }}"
                               class="billing-page-link">

                                <i class="bi bi-chevron-left"></i>

                            </a>

                        @endif


                        {{-- NOMOR HALAMAN --}}

                        @foreach ($rekap->getUrlRange(
                            max(1, $rekap->currentPage() - 2),
                            min($rekap->lastPage(), $rekap->currentPage() + 2)
                        ) as $page => $url)

                            @if ($page == $rekap->currentPage())

                                <span class="billing-page-link active">

                                    {{ $page }}

                                </span>

                            @else

                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
                                   class="billing-page-link">

                                    {{ $page }}

                                </a>

                            @endif

                        @endforeach


                        {{-- NEXT --}}

                        @if ($rekap->hasMorePages())

                            <a href="{{ $rekap->appends(request()->query())->nextPageUrl() }}"
                               class="billing-page-link">

                                <i class="bi bi-chevron-right"></i>

                            </a>

                        @else

                            <span class="billing-page-link disabled">

                                <i class="bi bi-chevron-right"></i>

                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
        BILLING KE - 3
    ============================================================ --}}

    <div id="billing3" class="billing-section d-none">

        <div class="billing-card card border-0 shadow-sm">

            {{-- JUDUL --}}

            <div class="card-header bg-white border-0 text-center">

                <h6 class="mb-1 fw-bold text-primary-custom billing-title">

                    BILLING KE - 3

                </h6>

                <h6 class="mb-0 fw-bold text-primary-custom billing-title">

                    AGENCY WITEL PRIANGAN TIMUR

                </h6>

            </div>


            {{-- ISI --}}

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped mb-0 billing-table">

                        <thead>

                            <tr>

                                <th class="billing-3-header"
                                    style="min-width:60px;">

                                    NO

                                </th>

                                <th class="billing-3-header"
                                    style="min-width:220px;">

                                    AGENCY

                                </th>


                                @foreach($witelDatels3 as $datel)

                                    <th class="billing-3-header"
                                        style="min-width:120px;">

                                        {{ $datel }}

                                    </th>

                                @endforeach


                                <th class="billing-3-header"
                                    style="min-width:120px;">

                                    TOTAL

                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($witelAgencies3 as $index => $agency)

                                @php
                                    $totalAgency = 0;
                                @endphp

                                <tr>

                                    <td class="text-center">

                                        {{ $index + 1 }}

                                    </td>


                                    <td class="fw-semibold">

                                        {{ $agency }}

                                    </td>


                                    @foreach($witelDatels3 as $datel)

                                        @php

                                            $jumlah =
                                                $witelData3[$agency][$datel] ?? 0;

                                            $totalAgency += $jumlah;

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($jumlah) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center fw-bold">

                                        {{ number_format($totalAgency) }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ count($witelDatels3) + 3 }}"
                                        class="text-center py-5">

                                        <i class="bi bi-inbox fs-3
                                                  text-muted d-block mb-1">
                                        </i>

                                        <span class="text-muted">

                                            Tidak ada data Billing 3.

                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        @if(count($witelAgencies3) > 0)

                            <tfoot>

                                <tr class="billing-footer">

                                    <td colspan="2"
                                        class="text-end">

                                        GRAND TOTAL

                                    </td>


                                    @foreach($witelDatels3 as $datel)

                                        @php

                                            $grandTotal = 0;

                                            foreach ($witelAgencies3 as $agency) {

                                                $grandTotal +=
                                                    $witelData3[$agency][$datel] ?? 0;

                                            }

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($grandTotal) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center">

                                        {{ number_format(
                                            collect($witelAgencies3)
                                                ->sum(function ($agency) use ($witelData3) {

                                                    return collect(
                                                        $witelData3[$agency] ?? []
                                                    )->sum();

                                                })
                                        ) }}

                                    </td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
        BILLING KE - 4
    ============================================================ --}}

    <div id="billing4" class="billing-section d-none">

        <div class="billing-card card border-0 shadow-sm">

            {{-- JUDUL --}}

            <div class="card-header bg-white border-0 text-center">

                <h6 class="mb-1 fw-bold text-primary-custom billing-title">

                    BILLING KE - 4

                </h6>

                <h6 class="mb-0 fw-bold text-primary-custom billing-title">

                    AGENCY WITEL PRIANGAN TIMUR

                </h6>

            </div>


            {{-- ISI --}}

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped mb-0 billing-table">

                        <thead>

                            <tr>

                                <th class="billing-4-header"
                                    style="min-width:60px;">

                                    NO

                                </th>

                                <th class="billing-4-header"
                                    style="min-width:220px;">

                                    AGENCY

                                </th>


                                @foreach($witelDatels4 as $datel)

                                    <th class="billing-4-header"
                                        style="min-width:120px;">

                                        {{ $datel }}

                                    </th>

                                @endforeach


                                <th class="billing-4-header"
                                    style="min-width:120px;">

                                    TOTAL

                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($witelAgencies4 as $index => $agency)

                                @php
                                    $totalAgency = 0;
                                @endphp

                                <tr>

                                    <td class="text-center">

                                        {{ $index + 1 }}

                                    </td>


                                    <td class="fw-semibold">

                                        {{ $agency }}

                                    </td>


                                    @foreach($witelDatels4 as $datel)

                                        @php

                                            $jumlah =
                                                $witelData4[$agency][$datel] ?? 0;

                                            $totalAgency += $jumlah;

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($jumlah) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center fw-bold">

                                        {{ number_format($totalAgency) }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ count($witelDatels4) + 3 }}"
                                        class="text-center py-5">

                                        <i class="bi bi-inbox fs-3
                                                  text-muted d-block mb-1">
                                        </i>

                                        <span class="text-muted">

                                            Tidak ada data Billing 4.

                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        @if(count($witelAgencies4) > 0)

                            <tfoot>

                                <tr class="billing-footer">

                                    <td colspan="2"
                                        class="text-end">

                                        GRAND TOTAL

                                    </td>


                                    @foreach($witelDatels4 as $datel)

                                        @php

                                            $grandTotal = 0;

                                            foreach ($witelAgencies4 as $agency) {

                                                $grandTotal +=
                                                    $witelData4[$agency][$datel] ?? 0;

                                            }

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($grandTotal) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center">

                                        {{ number_format(
                                            collect($witelAgencies4)
                                                ->sum(function ($agency) use ($witelData4) {

                                                    return collect(
                                                        $witelData4[$agency] ?? []
                                                    )->sum();

                                                })
                                        ) }}

                                    </td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
        BILLING KE - 5
    ============================================================ --}}

    <div id="billing5" class="billing-section d-none">

        <div class="billing-card card border-0 shadow-sm">

            {{-- JUDUL --}}

            <div class="card-header bg-white border-0 text-center">

                <h6 class="mb-1 fw-bold text-primary-custom billing-title">

                    BILLING KE - 5

                </h6>

                <h6 class="mb-0 fw-bold text-primary-custom billing-title">

                    AGENCY WITEL PRIANGAN TIMUR

                </h6>

            </div>


            {{-- ISI --}}

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped mb-0 billing-table">

                        <thead>

                            <tr>

                                <th class="billing-5-header"
                                    style="min-width:60px;">

                                    NO

                                </th>

                                <th class="billing-5-header"
                                    style="min-width:220px;">

                                    AGENCY

                                </th>


                                @foreach($witelDatels5 as $datel)

                                    <th class="billing-5-header"
                                        style="min-width:120px;">

                                        {{ $datel }}

                                    </th>

                                @endforeach


                                <th class="billing-5-header"
                                    style="min-width:120px;">

                                    TOTAL

                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($witelAgencies5 as $index => $agency)

                                @php
                                    $totalAgency = 0;
                                @endphp

                                <tr>

                                    <td class="text-center">

                                        {{ $index + 1 }}

                                    </td>


                                    <td class="fw-semibold">

                                        {{ $agency }}

                                    </td>


                                    @foreach($witelDatels5 as $datel)

                                        @php

                                            $jumlah =
                                                $witelData5[$agency][$datel] ?? 0;

                                            $totalAgency += $jumlah;

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($jumlah) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center fw-bold">

                                        {{ number_format($totalAgency) }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ count($witelDatels5) + 3 }}"
                                        class="text-center py-5">

                                        <i class="bi bi-inbox fs-3
                                                  text-muted d-block mb-1">
                                        </i>

                                        <span class="text-muted">

                                            Tidak ada data Billing 5.

                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        @if(count($witelAgencies5) > 0)

                            <tfoot>

                                <tr class="billing-footer">

                                    <td colspan="2"
                                        class="text-end">

                                        GRAND TOTAL

                                    </td>


                                    @foreach($witelDatels5 as $datel)

                                        @php

                                            $grandTotal = 0;

                                            foreach ($witelAgencies5 as $agency) {

                                                $grandTotal +=
                                                    $witelData5[$agency][$datel] ?? 0;

                                            }

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($grandTotal) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center">

                                        {{ number_format(
                                            collect($witelAgencies5)
                                                ->sum(function ($agency) use ($witelData5) {

                                                    return collect(
                                                        $witelData5[$agency] ?? []
                                                    )->sum();

                                                })
                                        ) }}

                                    </td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
        BILLING KE - 6
    ============================================================ --}}

    <div id="billing6" class="billing-section d-none">

        <div class="billing-card card border-0 shadow-sm">

            {{-- JUDUL --}}

            <div class="card-header bg-white border-0 text-center">

                <h6 class="mb-1 fw-bold text-primary-custom billing-title">

                    BILLING KE - 6

                </h6>

                <h6 class="mb-0 fw-bold text-primary-custom billing-title">

                    AGENCY WITEL PRIANGAN TIMUR

                </h6>

            </div>


            {{-- ISI --}}

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped mb-0 billing-table">

                        <thead>

                            <tr>

                                <th class="billing-6-header"
                                    style="min-width:60px;">

                                    NO

                                </th>

                                <th class="billing-6-header"
                                    style="min-width:220px;">

                                    AGENCY

                                </th>


                                @foreach($witelDatels6 as $datel)

                                    <th class="billing-6-header"
                                        style="min-width:120px;">

                                        {{ $datel }}

                                    </th>

                                @endforeach


                                <th class="billing-6-header"
                                    style="min-width:120px;">

                                    TOTAL

                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($witelAgencies6 as $index => $agency)

                                @php
                                    $totalAgency = 0;
                                @endphp

                                <tr>

                                    <td class="text-center">

                                        {{ $index + 1 }}

                                    </td>


                                    <td class="fw-semibold">

                                        {{ $agency }}

                                    </td>


                                    @foreach($witelDatels6 as $datel)

                                        @php

                                            $jumlah =
                                                $witelData6[$agency][$datel] ?? 0;

                                            $totalAgency += $jumlah;

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($jumlah) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center fw-bold">

                                        {{ number_format($totalAgency) }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="{{ count($witelDatels6) + 3 }}"
                                        class="text-center py-5">

                                        <i class="bi bi-inbox fs-3
                                                  text-muted d-block mb-1">
                                        </i>

                                        <span class="text-muted">

                                            Tidak ada data Billing 6.

                                        </span>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        @if(count($witelAgencies6) > 0)

                            <tfoot>

                                <tr class="billing-footer">

                                    <td colspan="2"
                                        class="text-end">

                                        GRAND TOTAL

                                    </td>


                                    @foreach($witelDatels6 as $datel)

                                        @php

                                            $grandTotal = 0;

                                            foreach ($witelAgencies6 as $agency) {

                                                $grandTotal +=
                                                    $witelData6[$agency][$datel] ?? 0;

                                            }

                                        @endphp

                                        <td class="text-center">

                                            {{ number_format($grandTotal) }}

                                        </td>

                                    @endforeach


                                    <td class="text-center">

                                        {{ number_format(
                                            collect($witelAgencies6)
                                                ->sum(function ($agency) use ($witelData6) {

                                                    return collect(
                                                        $witelData6[$agency] ?? []
                                                    )->sum();

                                                })
                                        ) }}

                                    </td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ================================================================
    JAVASCRIPT TAB BILLING
================================================================ --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const tabs = document.querySelectorAll('.billing-tab');

    const sections = document.querySelectorAll('.billing-section');


    tabs.forEach(tab => {

        tab.addEventListener('click', function () {

            const target = this.dataset.target;


            /* ====================================================
               SEMBUNYIKAN SEMUA SECTION
            ==================================================== */

            sections.forEach(section => {

                section.classList.add('d-none');

            });


            /* ====================================================
               TAMPILKAN SECTION YANG DIPILIH
            ==================================================== */

            const selectedSection =
                document.getElementById(target);

            if (selectedSection) {

                selectedSection.classList.remove('d-none');

            }


            /* ====================================================
               RESET SEMUA TAB
            ==================================================== */

            tabs.forEach(button => {

                button.classList.remove('active');


                const icon =
                    button.querySelector('i');

                if (icon) {

                    icon.className =
                        'bi bi-circle me-1';

                }

            });


            /* ====================================================
               AKTIFKAN TAB YANG DIPILIH
            ==================================================== */

            this.classList.add('active');


            const activeIcon =
                this.querySelector('i');

            if (activeIcon) {

                activeIcon.className =
                    'bi bi-record-circle me-1';

            }

        });

    });

});

</script>

@endsection