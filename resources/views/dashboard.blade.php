@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .ssl-grid-cell {
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .ssl-grid-cell:hover {
        color: #0b5ed7 !important;
        text-decoration: underline !important;
        font-weight: bold !important;
    }
</style>
<div class="container-fluid px-4" style="padding-top: 20px;">

    {{-- HEADER BRANDING --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-1" style="font-size: 1.5rem; color: #000361 !important;">PROSES PEMBAYARAN BILLING 1- 6</h3>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                Per Telkom Daerah - Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                @if(request('datel')) | Datel: <span class="fw-bold text-primary">{{ request('datel') }}</span> @endif
                @if(request('agency')) | Agency: <span class="fw-bold text-primary">{{ request('agency') }}</span> @endif
                @if(request('sales')) | Sales: <span class="fw-bold text-primary">{{ request('sales') }}</span> @endif
            </p>
        </div>
        <div>
            <div class="card border-0 shadow-sm px-3 py-2 d-flex flex-row align-items-center gap-2" style="background-color: #0b2240; color: white; border-radius: 8px;">
                <i class="bi bi-calendar3 fs-5"></i>
                <div style="font-size: 0.75rem; text-align: left;">
                    <div class="text-white-50">Update Terakhir</div>
                    <div class="fw-bold">{{ \App\Models\Customer::max('updated_at') ? \Carbon\Carbon::parse(\App\Models\Customer::max('updated_at'))->translatedFormat('d F Y H.i') . ' WIB' : '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- BANNER IMAGE --}}
    <div class="banner p-0 mb-4 overflow-hidden border-0" style="border-radius: 16px; height: 160px; box-shadow: none;">
        <img src="{{ asset('image/halaman pertama.png') }}" class="w-100 h-100" style="object-fit: cover;">
    </div>

    {{-- FILTER FORM --}}
    <div class="card mb-4 w-100">
        <div class="card-body p-3">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-3 align-items-end" id="dashboardFilterForm">
                <div class="col-md-3">
                    <label class="form-label mb-1 text-primary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">DATEL</label>
                    <select name="datel" id="filter-datel" class="form-select form-select-sm border-light-subtle text-muted" style="font-size: 0.8rem;" onchange="filterChange('datel')">
                        <option value="">Semua Datel</option>
                        @foreach($datelsList ?? [] as $d)
                            <option value="{{ $d }}" {{ request('datel') == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1 text-primary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">AGENCY</label>
                    <select name="agency" id="filter-agency" class="form-select form-select-sm border-light-subtle text-muted" style="font-size: 0.8rem;" onchange="filterChange('agency')">
                        <option value="">Semua Agency</option>
                        @foreach($agenciesList ?? [] as $a)
                            <option value="{{ $a }}" {{ request('agency') == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1 text-primary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">SALES</label>
                    <select name="sales" id="filter-sales" class="form-select form-select-sm border-light-subtle text-muted" style="font-size: 0.8rem;" onchange="filterChange('sales')">
                        <option value="">Semua Sales Agency</option>
                        @foreach($salesList ?? [] as $s)
                            <option value="{{ $s }}" {{ request('sales') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white flex-grow-1 shadow-sm d-flex align-items-center justify-content-center" style="background-color: #0b2240; font-size: 0.8rem;">
                        <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light flex-grow-1 shadow-sm d-flex align-items-center justify-content-center border" style="background-color: white; color: #333; font-size: 0.8rem;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- DYNAMIC STATISTICS CARDS --}}
    <div class="row g-3 mb-4">
        @if(request('sales'))
            {{-- Card 1: TOTAL CUSTOMER --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #f0f7ff; color: #000361; border: 1px solid #cce3fd !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-people-fill fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL CUSTOMER</h6>
                                <h3 class="mb-0 fw-bold fs-4">{{ number_format($totalCustomer ?? 0) }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">{{ Str::limit(request('sales'), 20) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card 2: BELUM BAYAR (SSL) --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #fffdf5; color: #b45309; border: 1px solid #fef3c7 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">BELUM BAYAR (SSL)</h6>
                                <h3 class="mb-0 fw-bold fs-4">{{ number_format($totalBelumBayarSales ?? 0) }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">{{ Str::limit(request('sales'), 20) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: TOTAL TAGIHAN --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #f2fcf5; color: #15803d; border: 1px solid #d1fae5 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-cash-stack fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL TAGIHAN</h6>
                                <h3 class="mb-0 fw-bold fs-4">Rp {{ number_format($totalTagihanSales ?? 0, 0, ',', '.') }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">{{ Str::limit(request('sales'), 20) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: TOTAL SALDO --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #fbf2ff; color: #7e22ce; border: 1px solid #f3e8ff !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-wallet2 fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL SALDO</h6>
                                <h3 class="mb-0 fw-bold fs-4">Rp {{ number_format($totalSaldoSales ?? 0, 0, ',', '.') }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">{{ Str::limit(request('sales'), 20) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Default/Datel/Agency View --}}
            {{-- Card 1: TOTAL CUSTOMER (SSL) --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #f0f7ff; color: #000361; border: 1px solid #cce3fd !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-people-fill fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL CUSTOMER (SSL)</h6>
                                <h3 class="mb-0 fw-bold fs-4">{{ number_format($totalBelumLunas ?? 0) }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">
                                    @if(request('datel') || request('agency'))
                                        {{ request('datel') ?: 'Semua Datel' }}@if(request('agency')) | {{ Str::limit(request('agency'), 12) }}@endif
                                    @else
                                        Semua Billing
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card 2: TOTAL TAGIHAN --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #f2fcf5; color: #15803d; border: 1px solid #d1fae5 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-cash-stack fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL TAGIHAN</h6>
                                <h3 class="mb-0 fw-bold fs-4">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">
                                    @if(request('datel') || request('agency'))
                                        {{ request('datel') ?: 'Semua Datel' }}@if(request('agency')) | {{ Str::limit(request('agency'), 12) }}@endif
                                    @else
                                        Semua Billing
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: TOTAL SALES AGENCY --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #fbf2ff; color: #7e22ce; border: 1px solid #f3e8ff !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-person-badge fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL SALES AGENCY</h6>
                                <h3 class="mb-0 fw-bold fs-4">{{ number_format($totalSales ?? 0) }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">
                                    @if(request('datel') || request('agency'))
                                        {{ request('datel') ?: 'Semua Datel' }}@if(request('agency')) | {{ Str::limit(request('agency'), 12) }}@endif
                                    @else
                                        Sales
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 4: TOTAL AGENCY --}}
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card border-0 h-100 w-100 overflow-hidden" style="background-color: #fffdf5; color: #b45309; border: 1px solid #fef3c7 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bi bi-building fs-2"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold" style="font-size:0.75rem; letter-spacing: 0.5px;">TOTAL AGENCY</h6>
                                <h3 class="mb-0 fw-bold fs-4">{{ number_format($totalAgency ?? 0) }}</h3>
                                <small style="font-size:0.7rem; opacity: 0.85;">
                                    @if(request('datel') || request('agency'))
                                        {{ request('datel') ?: 'Semua Datel' }}@if(request('agency')) | {{ Str::limit(request('agency'), 12) }}@endif
                                    @else
                                        Customer
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- DYNAMIC CONTENT TABLES --}}
    @if(request('sales'))
        {{-- CASE 3: Sales View - Daftar Customer Lengkap (Lunas & Belum Bayar) --}}
        <div class="card border-0 shadow-sm w-100 overflow-hidden mb-5">
            <div class="card-header bg-white border-0 py-3 px-3">
                <h6 class="mb-0 fw-bold text-primary">
                    DAFTAR CUSTOMER - {{ strtoupper(request('sales')) }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive w-100">
                    <table class="table table-hover table-striped align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">NO</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">CUSTOMER</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">DATEL</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">AGENCY</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">BILLING</th>
                                <th class="px-3 py-2 text-nowrap text-end" style="font-size:0.7rem;">TAGIHAN</th>
                                <th class="px-3 py-2 text-nowrap text-end" style="font-size:0.7rem;">SALDO</th>
                                <th class="px-3 py-2 text-nowrap text-center" style="font-size:0.7rem;">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesCustomers as $index => $customer)
                                <tr>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $salesCustomers->firstItem() + $index }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem; font-weight: 600; color: #000361;">{{ $customer->nama }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->datel }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->agency_psb ?: '-' }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">Billing {{ $customer->billing_ke }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->status_bayar == 'Sdh Bayar' ? 0 : ($customer->tag_total ?? 0), 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-{{ $customer->status_bayar == 'Sdh Bayar' ? 'success' : 'danger' }} px-2 py-1" style="font-size:0.65rem;">
                                            {{ $customer->status_bayar == 'Sdh Bayar' ? 'Lunas' : 'Belum Bayar' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox fs-2 text-muted d-block mb-1"></i>
                                        <span class="text-muted" style="font-size:0.85rem;">Tidak ada customer untuk sales agency ini</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Menampilkan {{ $salesCustomers->firstItem() ?? 0 }} - {{ $salesCustomers->lastItem() ?? 0 }} dari {{ number_format($salesCustomers->total()) }} data</small>
                    <div>{{ $salesCustomers->links() }}</div>
                </div>
            </div>
        </div>

    @elseif(request('agency'))
        {{-- CASE 2: Agency View - Daftar Customer Belum Bayar --}}
        <div class="card border-0 shadow-sm w-100 overflow-hidden mb-5">
            <div class="card-header bg-white border-0 py-3 px-3">
                <h6 class="mb-0 fw-bold text-primary">
                    REKAP CUSTOMER BELUM LUNAS - {{ strtoupper(request('agency')) }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive w-100">
                    <table class="table table-hover table-striped align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">NO</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">NCLI</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">NAMA CUSTOMER</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">DATEL</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">PRODUK</th>
                                <th class="px-3 py-2 text-nowrap text-end" style="font-size:0.7rem;">TAG_INET</th>
                                <th class="px-3 py-2 text-nowrap text-end" style="font-size:0.7rem;">TAG_TLP</th>
                                <th class="px-3 py-2 text-nowrap text-end" style="font-size:0.7rem;">TAG_TOTAL</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">SALES AGENCY</th>
                                <th class="px-3 py-2 text-nowrap text-end" style="font-size:0.7rem;">SALDO</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">BILLING KE-</th>
                                <th class="px-3 py-2 text-nowrap text-center" style="font-size:0.7rem;">STATUS BAYAR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agencyCustomers as $index => $customer)
                                <tr>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $agencyCustomers->firstItem() + $index }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->ncli ?: '-' }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem; font-weight: 600; color: #000361;">{{ $customer->nama }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->datel }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->produk }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_inet ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_tlp ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem; font-weight: bold;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->sales_agency ?: '-' }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2">
                                        <span class="badge bg-{{ ($customer->billing_ke ?? 0) <= 2 ? 'primary' : 'secondary' }} px-2 py-1" style="font-size:0.65rem;">
                                            Billing {{ $customer->billing_ke }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-danger px-2 py-1" style="font-size:0.65rem;">
                                            Belum Bayar
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4">
                                        <i class="bi bi-inbox fs-2 text-muted d-block mb-1"></i>
                                        <span class="text-muted" style="font-size:0.85rem;">Tidak ada customer belum lunas untuk agency ini</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Menampilkan {{ $agencyCustomers->firstItem() ?? 0 }} - {{ $agencyCustomers->lastItem() ?? 0 }} dari {{ number_format($agencyCustomers->total()) }} data</small>
                    <div>{{ $agencyCustomers->links() }}</div>
                </div>
            </div>
        </div>

    @else
        {{-- CASE 1: Default/Datel View --}}
        @if(!request('datel') && !request('agency') && !request('sales'))
            {{-- BILLING SUMMARY 1-6 CARDS (ORIGINAL) --}}
            <div class="card mb-4 w-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem; color: #000361 !important;">
                        <i class="bi bi-file-invoice-dollar me-2"></i> Rekap Billing Customer 1-6 (HOTD)
                    </h6>
                    <span class="badge text-white px-2 py-1" style="font-size:0.7rem; background-color: #0b2240;">
                        <i class="bi bi-info-circle me-1"></i> Billing 1-6
                    </span>
                </div>
                <div class="card-body p-3 pt-0">
                    <div class="row g-2">
                        @if(!empty($billingSummary) && is_iterable($billingSummary))
                            @forelse($billingSummary as $billing)
                                <div class="col-xl-2 col-lg-4 col-md-6">
                                    <a href="{{ route('billing.detail', ['billing_ke' => $billing->billing_ke, 'datel' => request('datel'), 'agency' => request('agency'), 'sales' => request('sales')]) }}" class="text-decoration-none text-dark d-block border border-light-subtle rounded shadow-sm">
                                        <div class="card border-0 h-100 w-100 overflow-hidden" style="background:#f8f9fa;">
                                            <div class="card-body text-center p-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="text-muted mb-0" style="font-size:0.75rem;">Billing {{ $billing->billing_ke ?? 0 }}</h6>
                                                    <span class="badge bg-{{ ($billing->billing_ke ?? 0) <= 2 ? 'primary' : 'secondary' }} px-2 py-1" style="font-size:0.6rem;">
                                                        {{ ($billing->billing_ke ?? 0) <= 2 ? 'Awal' : 'Berikutnya' }}
                                                    </span>
                                                </div>
                                                <h4 class="mb-0 mt-1" style="font-size:1.3rem; color: #000361;">{{ number_format($billing->belum_lunas ?? 0) }}</h4>
                                                <small class="text-muted" style="font-size:0.65rem;">Customer Belum Bayar</small>
                                                <hr class="my-1">
                                                <div class="text-danger fw-bold" style="font-size:0.75rem;">
                                                    Rp {{ number_format($billing->total_tagihan ?? 0, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0" style="font-size:0.8rem;">Tidak ada data billing</div>
                                </div>
                            @endforelse
                        @else
                            <div class="col-12">
                                <div class="alert alert-info mb-0" style="font-size:0.8rem;">Data billing sedang dimuat...</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2D GRID MATRIX TABLE Detail Billing 1-6 per Datel (HOTD) (ORIGINAL) --}}
            <div class="card mb-4 w-100 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem; color: #000361 !important;">
                        <i class="bi bi-table me-2"></i> Detail Billing 1-6 per Datel (HOTD)
                        <small class="text-muted ms-2" style="font-size:0.7rem;">Klik baris untuk lihat detail</small>
                    </h6>
                    <span class="badge text-white px-2 py-1" style="font-size:0.7rem; background-color: #0b2240;">
                        <i class="bi bi-database me-1"></i> {{ count($hotdData ?? []) }} Data
                    </span>
                </div>
                <div class="card-body p-2">
                    @if(!empty($hotdData) && count($hotdData) > 0)
                        @php
                            $grouped = $hotdData->groupBy('billing_ke');
                            $datels = $hotdData->pluck('datel')->unique()->sort()->values();
                            $colors = ['#E2001A', '#2F3A4A', '#28a745', '#ffc107', '#17a2b8', '#dc3545'];
                            
                            $billingTotals = [];
                            foreach ($grouped as $billingKe => $items) {
                                $billingTotals[$billingKe] = [
                                    'total_customer' => $items->sum('total_customer'),
                                    'total_tagihan' => $items->sum('total_tagihan'),
                                    'blm_bayar' => $items->sum('blm_bayar'),
                                    'sdh_bayar' => $items->sum('sdh_bayar'),
                                    'blm_bayar_rp' => $items->sum('blm_bayar_rp'),
                                    'sdh_bayar_rp' => $items->sum('sdh_bayar_rp'),
                                ];
                            }

                            $datelBillingMap = [];
                            foreach ($datels as $d) {
                                $billingsForDatel = $grouped->filter(function($items, $key) use ($d) {
                                    return $items->contains('datel', $d);
                                })->keys()->toArray();
                                
                                if (!empty($billingsForDatel)) {
                                    $datelBillingMap[$d] = $billingsForDatel[0];
                                }
                            }
                        @endphp

                        <div class="table-responsive w-100" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                            <table class="table table-bordered table-hover table-sm mb-0 w-100" style="min-width: 600px; font-size: 0.72rem; border-color: #cbd5e1;">
                                <thead>
                                    <tr>
                                        <th rowspan="3" class="text-center align-middle text-nowrap px-2 py-1 text-dark" style="min-width:100px; font-size: 0.65rem; background-color: #f8f9fa;">DATEL</th>
                                        @foreach(range(1, 6) as $billingKe)
                                            <th colspan="2" class="text-center text-white text-nowrap px-2 py-1" style="background-color:{{ $colors[$billingKe-1] }}; min-width:100px; font-size: 0.65rem;">
                                                Billing {{ $billingKe }}
                                            </th>
                                        @endforeach
                                        <th colspan="2" rowspan="3" class="text-center align-middle text-white text-nowrap px-2 py-1" style="background-color:#6c757d; min-width:100px; font-size: 0.65rem;">
                                            TOTAL ALL
                                        </th>
                                    </tr>
                                    <tr>
                                        @foreach(range(1, 6) as $billingKe)
                                            <th colspan="2" class="text-center text-white text-nowrap px-2 py-1" style="background-color:#dc3545; font-size: 0.65rem;">Belum Bayar</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach(range(1, 6) as $billingKe)
                                            <th class="text-center text-nowrap px-2 py-1 text-dark" style="min-width:35px; font-size: 0.65rem; background-color: #f8f9fa;">SSL</th>
                                            <th class="text-center text-nowrap px-2 py-1 text-dark" style="min-width:70px; font-size: 0.65rem; background-color: #f8f9fa;">RUPIAH</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datels as $datelItem)
                                        <tr style="cursor:pointer;">
                                            <td class="fw-bold text-nowrap px-2 py-1 text-dark" style="font-size: 0.7rem; color: #000361;">{{ $datelItem }}</td>
                                            @foreach(range(1, 6) as $billingKe)
                                                @php
                                                    $item = $grouped->get($billingKe)?->firstWhere('datel', $datelItem);
                                                    $blmVal = $item ? $item->blm_bayar : 0;
                                                    $rpVal = $item ? $item->blm_bayar_rp : 0;
                                                @endphp
                                                <td class="text-center text-nowrap px-2 py-1 text-dark ssl-grid-cell" style="font-size: 0.75rem;" onclick="showDetail('{{ $datelItem }}', {{ $billingKe }})">
                                                    {{ $blmVal > 0 ? $blmVal : '' }}
                                                </td>
                                                <td class="text-end text-nowrap px-2 py-1 text-danger ssl-grid-cell" style="font-size:0.65rem;" onclick="showDetail('{{ $datelItem }}', {{ $billingKe }})">
                                                    {{ $rpVal > 0 ? 'Rp ' . number_format($rpVal, 0, ',', '.') : '' }}
                                                </td>
                                            @endforeach
                                            @php
                                                $rowBlm = 0; $rowBlmRp = 0;
                                                foreach(range(1, 6) as $billingKe) {
                                                    $item = $grouped->get($billingKe)?->firstWhere('datel', $datelItem);
                                                    $rowBlm += $item ? $item->blm_bayar : 0;
                                                    $rowBlmRp += $item ? $item->blm_bayar_rp : 0;
                                                }
                                            @endphp
                                            <td class="text-center text-white text-nowrap px-2 py-1" style="background-color:#868e96; font-size: 0.75rem;">{{ $rowBlm }}</td>
                                            <td class="text-end text-white text-nowrap px-2 py-1" style="background-color:#868e96; font-size:0.65rem;">Rp {{ number_format($rowBlmRp, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold text-dark">
                                        <td class="px-2 py-1 text-nowrap" style="font-size: 0.7rem; background-color: #eaeaea;">TOTAL</td>
                                        @foreach(range(1, 6) as $billingKe)
                                            @php
                                                $total = $billingTotals[$billingKe] ?? ['blm_bayar' => 0, 'blm_bayar_rp' => 0];
                                            @endphp
                                            <td class="text-center text-nowrap px-2 py-1" style="font-size: 0.65rem; background-color: #eaeaea;">{{ $total['blm_bayar'] }}</td>
                                            <td class="text-center text-nowrap px-2 py-1 text-danger" style="font-size:0.6rem; background-color: #eaeaea;">Rp {{ number_format($total['blm_bayar_rp'] ?? 0, 0, ',', '.') }}</td>
                                        @endforeach
                                        @php
                                            $allBlm = 0; $allBlmRp = 0;
                                            foreach(range(1, 6) as $billingKe) {
                                                $total = $billingTotals[$billingKe] ?? ['blm_bayar' => 0, 'blm_bayar_rp' => 0];
                                                $allBlm += $total['blm_bayar'];
                                                $allBlmRp += $total['blm_bayar_rp'];
                                            }
                                        @endphp
                                        <td class="text-center text-white text-nowrap px-2 py-1" style="background-color:#6c757d; font-size: 0.65rem;">{{ $allBlm }}</td>
                                        <td class="text-center text-white text-nowrap px-2 py-1" style="background-color:#6c757d; font-size:0.6rem;">Rp {{ number_format($allBlmRp, 0, ',', '.') }}</td>
                                     </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0" style="font-size:0.8rem;">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Tidak ada data HOTD. Pastikan data customer sudah di-sync.
                        </div>
                    @endif
                </div>
            </div>


        @else
            {{-- Tampilan 1D Datel Terpilih --}}
            <div class="card border-0 shadow-sm w-100 overflow-hidden mb-5">
                <div class="card-header bg-white border-0 py-3 px-3">
                    <h6 class="mb-0 fw-bold text-primary">
                        REKAP BILLING 1 - 6 - {{ request('datel') ? strtoupper(request('datel')) : 'NASIONAL' }}
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive w-100">
                        <table class="table table-hover table-bordered align-middle mb-0 w-100">
                            <thead class="table-light">
                                <tr style="background-color: #f8f9fa;">
                                    <th class="px-3 py-2 text-center text-nowrap" style="font-size:0.75rem; font-weight: bold; width: 30%;">BILLING</th>
                                    <th class="px-3 py-2 text-center text-nowrap" style="font-size:0.75rem; font-weight: bold; width: 20%;">SSL</th>
                                    <th class="px-3 py-2 text-center text-nowrap" style="font-size:0.75rem; font-weight: bold; width: 30%;">RUPIAH</th>
                                    <th class="px-3 py-2 text-center text-nowrap" style="font-size:0.75rem; font-weight: bold; width: 20%;">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalSsl = 0;
                                    $totalRp = 0;
                                    $totalAllCust = 0;
                                    $totalUnpaidCust = 0;
                                @endphp
                                @forelse($rekapBilling ?? [] as $rekap)
                                    @php
                                        $totalSsl += $rekap->unpaid_cust;
                                        $totalRp += $rekap->unpaid_rp;
                                        $totalAllCust += $rekap->total_cust;
                                        $totalUnpaidCust += $rekap->unpaid_cust;
                                        
                                        $rate = $rekap->total_cust > 0 
                                            ? (($rekap->total_cust - $rekap->unpaid_cust) / $rekap->total_cust) * 100 
                                            : 100;
                                    @endphp
                                    <tr onclick="showDetail('{{ request('datel') ?: 'Nasional' }}', {{ $rekap->billing_ke }})" style="cursor: pointer;" title="Klik untuk lihat detail customer">
                                        <td class="px-3 py-2 fw-semibold text-primary" style="font-size:0.85rem;">BILLING KE {{ $rekap->billing_ke }}</td>
                                        <td class="px-3 py-2 text-center fw-bold" style="font-size:0.85rem; color: #000361;">{{ number_format($rekap->unpaid_cust) }}</td>
                                        <td class="px-3 py-2 text-end fw-semibold text-danger" style="font-size:0.85rem;">Rp {{ number_format($rekap->unpaid_rp, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-center fw-bold text-success" style="font-size:0.85rem;">{{ number_format($rate, 2, ',', '.') }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="bi bi-inbox fs-2 text-muted d-block mb-1"></i>
                                            <span class="text-muted" style="font-size:0.85rem;">Tidak ada data rekap billing</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($rekapBilling) && count($rekapBilling) > 0)
                                @php
                                    $totalRate = $totalAllCust > 0 
                                        ? (($totalAllCust - $totalUnpaidCust) / $totalAllCust) * 100 
                                        : 100;
                                @endphp
                                <tfoot>
                                    <tr class="table-light fw-bold" style="background-color: #eaeaea;">
                                        <td class="px-3 py-2" style="font-size:0.85rem; font-weight: bold;">TOTAL</td>
                                        <td class="px-3 py-2 text-center" style="font-size:0.85rem; font-weight: bold;">{{ number_format($totalSsl) }}</td>
                                        <td class="px-3 py-2 text-end text-danger" style="font-size:0.85rem; font-weight: bold;">Rp {{ number_format($totalRp, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-center text-success" style="font-size:0.85rem; font-weight: bold;">{{ number_format($totalRate, 2, ',', '.') }}%</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- PRINT FLOATING DOWNLOAD BUTTON (Klik Unduh) AT BOTTOM RIGHT --}}
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button class="btn btn-primary btn-lg shadow-lg d-flex align-items-center justify-content-center gap-2 rounded-circle" 
                style="background-color: #0b2240; border-color: #0b2240; width: 60px; height: 60px;" 
                onclick="exportDashboardPdf()" 
                title="Cetak Halaman ke PDF">
            <i class="bi bi-download fs-4"></i>
        </button>
    </div>
</div>
@endsection

{{-- ============================================ --}}
{{-- MODAL DETAIL HOTD --}}
{{-- ============================================ --}}
<div class="modal fade" id="hotdDetailModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white sticky-top" style="z-index:1050;">
                <h5 class="modal-title">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Detail Customer - Billing <span id="detailBillingKe" class="fw-bold"></span>
                    <span class="mx-2">|</span>
                    Datel: <span id="detailDatel" class="fw-bold"></span>
                    <span class="badge bg-light text-dark ms-3 px-2 py-1">
                        <i class="bi bi-database me-1"></i>
                        <span id="totalCustomerCount">0</span> Customer
                    </span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent" style="padding:15px;background:#f8f9fa;">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat data detail...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
                <a id="btnExportExcel" href="#" class="btn btn-info rounded text-white">
                    <i class="bi bi-file-excel me-1"></i> Export Excel
                </a>
                <button type="button" class="btn btn-primary rounded" onclick="exportDetailPdf()">
                    <i class="bi bi-download me-1"></i> Klik Unduh
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// STATUS CHART REMOVED

// ============================================
// FUNGSI SHOW DETAIL - Dipanggil saat klik Rupiah
// ============================================
function showDetail(datel, billingKe) {
    console.log('Detail diklik! Datel:', datel, 'Billing:', billingKe);
    
    // Update judul modal
    document.getElementById('detailBillingKe').textContent = billingKe;
    document.getElementById('detailDatel').textContent = datel;

    const agency = document.getElementById('filter-agency') ? document.getElementById('filter-agency').value : '';
    const sales = document.getElementById('filter-sales') ? document.getElementById('filter-sales').value : '';
    
    // Tampilkan modal dengan loading
    const modal = new bootstrap.Modal(document.getElementById('hotdDetailModal'), {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();
    
    document.getElementById('detailContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data detail untuk Billing ${billingKe} - Datel ${datel}...</p>
        </div>
    `;
    
    let url = `/hotd-detail/${billingKe}/${encodeURIComponent(datel)}`;
    const params = new URLSearchParams();
    if (agency) params.append('agency', agency);
    if (sales) params.append('sales', sales);
    if (params.toString()) {
        url += '?' + params.toString();
    }

    // Set URL untuk tombol Export Excel
    let exportUrl = `/hotd-detail/${billingKe}/${encodeURIComponent(datel)}/export`;
    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }
    const btnExport = document.getElementById('btnExportExcel');
    if (btnExport) {
        btnExport.href = exportUrl;
    }

    // AJAX request dengan fetch
    fetch(url)
        .then(response => response.json())
        .then(response => {
            console.log('Response:', response);
            if (response.customers && response.customers.length > 0) {
                renderDetailTable(response);
            } else {
                document.getElementById('detailContent').innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Tidak ada data customer untuk Billing ${response.billing_ke} - Datel ${response.datel}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('detailContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Gagal memuat data detail: ${error.message}
                </div>
            `;
        });
}

// ============================================
// FUNGSI RENDER DETAIL TABLE
// ============================================
function renderDetailTable(response) {
    const customers = response.customers;
    
    let html = `
        <div class="row g-2 mb-3">
            <div class="col-md-3 col-6">
                <div class="card bg-primary text-white border-0 rounded shadow-sm">
                    <div class="card-body py-2 px-3">
                        <small>Total Customer Belum Bayar</small>
                        <h5 class="mb-0">${response.total_blm_bayar}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card bg-warning text-dark border-0 rounded shadow-sm">
                    <div class="card-body py-2 px-3">
                        <small>Total Tagihan</small>
                        <h5 class="mb-0">Rp ${new Intl.NumberFormat('id-ID').format(response.total_tagihan)}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card bg-info text-white border-0 rounded shadow-sm">
                    <div class="card-body py-2 px-3">
                        <small>Total Saldo</small>
                        <h5 class="mb-0">Rp ${new Intl.NumberFormat('id-ID').format(response.total_saldo)}</h5>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive w-100" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table table-bordered table-hover table-sm w-100" id="detailTable" style="font-size:0.7rem; min-width: 600px;">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:35px;">#</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:70px;">Status</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:90px;">SND</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:90px;">SND Group</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:90px;">NCLI</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:130px;">Nama</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:160px;">Alamat</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:60px;">STO</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:70px;">Datel</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">Produk</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:70px;">Eksepsi</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:70px;">New Bill</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:70px;">Usage</th>
                        <th colspan="1" class="text-center text-nowrap px-2 py-1">Tagihan</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:90px;">Saldo</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:50px;">Umur</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:70px;">Billing</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:70px;">Paid L11</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">Tgl Paid</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:90px;">Paid Rp</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">Coll Agent</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">Tgl Klaim</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:90px;">Amount Klaim</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">User Klaim</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">Tgl Paid N-1</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">Agency PSB</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:80px;">Sales Agency</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:60px;">PPP</th>
                        <th rowspan="2" class="align-middle text-nowrap px-2 py-1" style="min-width:90px;">Caring</th>
                    </tr>
                    <tr>
                        <th class="text-center text-nowrap px-2 py-1">Total</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    customers.forEach((cust, index) => {
        html += `
            <tr>
                <td class="text-nowrap px-2 py-1">${index + 1}</td>
                <td class="text-nowrap px-2 py-1">
                    <span class="badge ${cust.status_bayar == 'Sdh Bayar' ? 'bg-success' : 'bg-danger'} px-2 py-1" style="font-size:0.6rem;">
                        ${cust.status_bayar || '-'}
                    </span>
                </td>
                <td class="text-nowrap px-2 py-1"><code>${cust.snd || '-'}</code></td>
                <td class="text-nowrap px-2 py-1">${cust.snd_group || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.ncli || '-'}</td>
                <td class="text-nowrap px-2 py-1" style="max-width:130px;overflow:hidden;text-overflow:ellipsis;" title="${cust.nama || ''}">
                    ${cust.nama || '-'}
                </td>
                <td class="text-nowrap px-2 py-1" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;" title="${cust.alamat || ''}">
                    ${cust.alamat || '-'}
                </td>
                <td class="text-nowrap px-2 py-1">${cust.sto || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.datel || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.produk || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.eksepsi_desc || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.desc_newbill || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.usage_desc || '-'}</td>
                <td class="text-end fw-bold text-nowrap px-2 py-1">${cust.tag_total ? new Intl.NumberFormat('id-ID').format(cust.tag_total) : '0'}</td>
                <td class="text-end text-nowrap px-2 py-1">${cust.saldo ? new Intl.NumberFormat('id-ID').format(cust.saldo) : '0'}</td>
                <td class="text-nowrap px-2 py-1">${cust.umur_customer || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.billing_ke || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.paid_l11 || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.tgl_paid || '-'}</td>
                <td class="text-end text-nowrap px-2 py-1">${cust.paid_rp ? new Intl.NumberFormat('id-ID').format(cust.paid_rp) : '0'}</td>
                <td class="text-nowrap px-2 py-1">${cust.coll_agent || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.tgl_klaim || '-'}</td>
                <td class="text-end text-nowrap px-2 py-1">${cust.amount_klaim ? new Intl.NumberFormat('id-ID').format(cust.amount_klaim) : '0'}</td>
                <td class="text-nowrap px-2 py-1">${cust.user_klaim || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.tgl_paid_n1 || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.agency_psb || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.sales_agency || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.ppp || '-'}</td>
                <td class="text-nowrap px-2 py-1">${cust.caring_mybrains || '-'}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td colspan="13" class="text-end text-nowrap px-2 py-1">TOTAL</td>
                        <td class="text-end text-nowrap px-2 py-1">${new Intl.NumberFormat('id-ID').format(response.total_tagihan)}</td>
                        <td class="text-end text-nowrap px-2 py-1">${new Intl.NumberFormat('id-ID').format(response.total_saldo)}</td>
                        <td colspan="14" class="text-nowrap px-2 py-1"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = html;
    document.getElementById('totalCustomerCount').textContent = response.total_customer;
}

// ============================================
// FUNGSI EXPORT EXCEL (Sudah Diganti ke Backend)
// ============================================
// Fungsi exportDetailToExcel dihapus karena sekarang menggunakan tag <a> yang mengarah ke route backend Laravel
// ============================================
// Filter reset logic and submission
function filterChange(type) {
    if (type === 'datel') {
        const agencySelect = document.getElementById('filter-agency');
        const salesSelect = document.getElementById('filter-sales');
        if (agencySelect) agencySelect.value = '';
        if (salesSelect) salesSelect.value = '';
    } else if (type === 'agency') {
        const salesSelect = document.getElementById('filter-sales');
        if (salesSelect) salesSelect.value = '';
    }
    document.getElementById('dashboardFilterForm').submit();
}

// Function to export detail modal content as PDF
// Function to export detail modal content as PDF (Optimized and Neat)
function exportDetailPdf() {
    const billingKe = document.getElementById('detailBillingKe').textContent;
    const datel = document.getElementById('detailDatel').textContent;
    
    // Read from the active table rows
    const rows = document.querySelectorAll('#detailTable tbody tr');
    let tableRowsHtml = '';
    
    rows.forEach((row) => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 28) {
            const no = cells[0].textContent.trim();
            const status = cells[1].textContent.trim();
            const snd = cells[2].textContent.trim();
            const nama = cells[5].textContent.trim();
            const datelVal = cells[8].textContent.trim();
            const billing = 'B' + cells[16].textContent.replace('B', '').trim();
            const tagihan = cells[13].textContent.trim();
            const agencyVal = cells[25].textContent.trim();
            const salesVal = cells[26].textContent.trim();
            
            const statusBg = status === 'Sdh Bayar' ? '#d1e7dd' : '#f8d7da';
            const statusColor = status === 'Sdh Bayar' ? '#0f5132' : '#842029';
            
            tableRowsHtml += `
                <tr>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">${no}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">
                        <span style="background-color: ${statusBg}; color: ${statusColor}; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 7.5px; display: inline-block;">
                            ${status}
                        </span>
                    </td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; font-family: monospace;">${snd}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; font-weight: bold; color: #000361;">${nama}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px;">${datelVal}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px;">${agencyVal}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px;">${salesVal}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: center;">${billing}</td>
                    <td style="border: 1px solid #dee2e6; padding: 4px; text-align: right; font-weight: bold; color: #dc3545;">Rp ${tagihan}</td>
                </tr>
            `;
        }
    });

    // Get total tagihan and total saldo from tfoot
    const totalTagihanCell = document.querySelector('#detailTable tfoot tr td:nth-child(2)');
    const totalTagihan = totalTagihanCell ? totalTagihanCell.textContent.trim() : '0';

    // Get cards stats to keep it neat
    const cardBelumBayar = document.querySelector('#detailContent .card.bg-primary h5') ? document.querySelector('#detailContent .card.bg-primary h5').textContent.trim() : '0';
    const cardTagihan = document.querySelector('#detailContent .card.bg-warning h5') ? document.querySelector('#detailContent .card.bg-warning h5').textContent.trim() : 'Rp 0';
    const cardSaldo = document.querySelector('#detailContent .card.bg-info h5') ? document.querySelector('#detailContent .card.bg-info h5').textContent.trim() : 'Rp 0';

    // Create temporary styled print wrapper
    const pdfWrapper = document.createElement('div');
    pdfWrapper.style.padding = '15px';
    pdfWrapper.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    pdfWrapper.style.backgroundColor = '#ffffff';
    
    // Add header branding & simplified layout
    pdfWrapper.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000361; padding-bottom: 8px; margin-bottom: 15px;">
            <div>
                <h4 style="margin: 0; color: #000361; font-weight: bold; font-size: 15px;">LAPORAN DETAIL CUSTOMER - BILLING 1 - 6</h4>
                <div style="font-size: 10px; color: #6c757d; margin-top: 3px;">Detail Billing: ${billingKe} | Datel: ${datel}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 13px; font-weight: bold; color: #dc3545; letter-spacing: 0.5px;">CONFIDENTIAL</div>
                <div style="font-size: 8px; color: #868e96;">Telkom Customer Management System</div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1; border: 1px solid #dee2e6; border-radius: 5px; padding: 6px 10px; background-color: #f8f9fa;">
                <div style="font-size: 8px; color: #6c757d; text-transform: uppercase;">Customer Belum Bayar</div>
                <div style="font-size: 13px; font-weight: bold; color: #000361; margin-top: 1px;">${cardBelumBayar}</div>
            </div>
            <div style="flex: 1; border: 1px solid #dee2e6; border-radius: 5px; padding: 6px 10px; background-color: #f8f9fa;">
                <div style="font-size: 8px; color: #6c757d; text-transform: uppercase;">Total Tagihan</div>
                <div style="font-size: 13px; font-weight: bold; color: #c2410c; margin-top: 1px;">${cardTagihan}</div>
            </div>
            <div style="flex: 1; border: 1px solid #dee2e6; border-radius: 5px; padding: 6px 10px; background-color: #f8f9fa;">
                <div style="font-size: 8px; color: #6c757d; text-transform: uppercase;">Total Saldo</div>
                <div style="font-size: 13px; font-weight: bold; color: #0891b2; margin-top: 1px;">${cardSaldo}</div>
            </div>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 8.5px; border: 1px solid #dee2e6;">
            <thead>
                <tr style="background-color: #0b2240; color: #ffffff;">
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center; width: 4%;">#</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center; width: 10%;">STATUS</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 12%;">SND</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 22%;">NAMA CUSTOMER</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 12%;">DATEL</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 14%;">AGENCY</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: left; width: 12%;">SALES</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: center; width: 6%;">BILLING</th>
                    <th style="border: 1px solid #dee2e6; padding: 5px; text-align: right; width: 12%;">TAGIHAN</th>
                </tr>
            </thead>
            <tbody>
                ${tableRowsHtml}
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="8" style="border: 1px solid #dee2e6; padding: 5px; text-align: right; color: #0b2240; font-size: 9px;">TOTAL TAGIHAN</td>
                    <td style="border: 1px solid #dee2e6; padding: 5px; text-align: right; color: #dc3545; font-size: 9px;">Rp ${totalTagihan}</td>
                </tr>
            </tfoot>
        </table>
        
        <div style="border-top: 1px dashed #dee2e6; padding-top: 8px; margin-top: 15px; text-align: center; font-size: 8px; color: #868e96;">
            Laporan Detail ini diunduh secara resmi melalui Telkom Customer Management System.<br>
            © ${new Date().getFullYear()} Telkom Indonesia. All Rights Reserved.
        </div>
    `;
    
    // Append to body temporarily
    document.body.appendChild(pdfWrapper);
    
    const opt = {
        margin:       6,
        filename:     `Laporan_Detail_Billing_${billingKe}_${datel.replace(/[^a-z0-9]/gi, '_')}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.2, useCORS: true, logging: false },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
    
    html2pdf().from(pdfWrapper).set(opt).save().then(() => {
        document.body.removeChild(pdfWrapper);
    }).catch(err => {
        console.error(err);
        document.body.removeChild(pdfWrapper);
        alert('Gagal menghasilkan file PDF.');
    });
}

// Function to export whole dashboard page layout (stats & tables) as PDF
function exportDashboardPdf() {
    const pdfWrapper = document.createElement('div');
    pdfWrapper.style.padding = '20px';
    pdfWrapper.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    pdfWrapper.style.backgroundColor = '#ffffff';
    
    // Grab headers, statistics and tables
    const title = document.querySelector('h3.fw-bold') ? document.querySelector('h3.fw-bold').textContent : 'PROSES PEMBAYARAN BILLING 1- 6';
    const subtitle = document.querySelector('p.text-muted') ? document.querySelector('p.text-muted').textContent : 'Per Telkom Daerah';
    const statsHTML = document.querySelector('.row.g-3.mb-4') ? document.querySelector('.row.g-3.mb-4').outerHTML : '';
    const tableHTML = document.querySelector('.card.border-0.shadow-sm.w-100.overflow-hidden.mb-5') ? document.querySelector('.card.border-0.shadow-sm.w-100.overflow-hidden.mb-5').outerHTML : '';
    
    pdfWrapper.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000361; padding-bottom: 10px; margin-bottom: 20px;">
            <div>
                <h4 style="margin: 0; color: #000361; font-weight: bold; font-size: 16px;">${title}</h4>
                <div style="font-size: 11px; color: #6c757d; margin-top: 4px;">${subtitle}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 14px; font-weight: bold; color: #dc3545; letter-spacing: 0.5px;">LAPORAN REKAPITULASI</div>
                <div style="font-size: 9px; color: #868e96;">Telkom Customer Management System</div>
            </div>
        </div>
        
        <div style="margin-bottom: 25px;">
            ${statsHTML}
        </div>
        
        <div style="margin-bottom: 20px;">
            ${tableHTML}
        </div>
        
        <div style="border-top: 1px dashed #dee2e6; padding-top: 10px; margin-top: 30px; text-align: center; font-size: 9px; color: #868e96;">
            Laporan Rekap ini diunduh secara resmi melalui Telkom Customer Management System.<br>
            © ${new Date().getFullYear()} Telkom Indonesia. All Rights Reserved.
        </div>
    `;
    
    // Remove forms, buttons, pagination inside container
    pdfWrapper.querySelectorAll('.pagination, .btn, button, select, input, #dashboardFilterForm, #btnExportExcel').forEach(el => el.remove());
    
    document.body.appendChild(pdfWrapper);
    
    const opt = {
        margin:       10,
        filename:     `Laporan_Rekap_Dashboard_${new Date().toISOString().slice(0,10)}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.5, useCORS: true, logging: false },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
    
    html2pdf().from(pdfWrapper).set(opt).save().then(() => {
        document.body.removeChild(pdfWrapper);
    }).catch(err => {
        console.error(err);
        document.body.removeChild(pdfWrapper);
        alert('Gagal menghasilkan file PDF.');
    });
}
</script>
@endpush