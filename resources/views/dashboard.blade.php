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
            <form action="{{ route('dashboard') }}" method="GET" id="dashboardFilterForm">
                {{-- SECTION 1: SEARCH --}}
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label mb-1 text-primary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">PENCARIAN CUSTOMER (NAMA/SND/DLL)</label>
                        <input type="text" name="search" id="filter-search" class="form-control form-control-sm border-light-subtle text-muted" style="font-size: 0.8rem;" placeholder="Cari..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm text-white w-100 shadow-sm d-flex align-items-center justify-content-center" style="background-color: #0b5ed7; font-size: 0.8rem;">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                    </div>
                </div>

                {{-- SECTION 2: FILTER --}}
                <div class="row g-2 align-items-end border-top pt-3">
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
                            <option value="">Semua Sales</option>
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
                </div>
            </form>
        </div>
    </div>

    {{-- DYNAMIC STATISTICS CARDS --}}
    <div class="row g-3 mb-4">
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
                                @if(request('sales'))
                                    {{ Str::limit(request('sales'), 20) }}
                                @elseif(request('agency'))
                                    {{ Str::limit(request('agency'), 20) }}
                                @elseif(request('datel'))
                                    {{ request('datel') }}
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
                                Belum Lunas
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
                                @if(request('sales'))
                                    {{ Str::limit(request('sales'), 18) }}
                                @else
                                    Sales Agency
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
                                @if(request('agency'))
                                    {{ Str::limit(request('agency'), 18) }}
                                @else
                                    Agency PSB
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DYNAMIC CONTENT TABLES --}}
    @if(request('sales'))
        {{-- CASE 3: Sales View - Daftar Customer Belum Lunas --}}
        <div class="card border-0 shadow-sm w-100 overflow-hidden mb-5">
            <div class="card-header bg-white border-0 py-3 px-3">
                <h6 class="mb-0 fw-bold text-primary">
                    DAFTAR CUSTOMER BELUM LUNAS - {{ strtoupper(request('sales')) }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive w-100">
                    <table class="table table-hover table-striped align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">NO</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">SND</th>
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
                                    <td class="px-3 py-2 text-nowrap" style="font-size:0.8rem; font-family: monospace;">{{ $customer->snd ?: '-' }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem; font-weight: 600; color: #000361;">{{ $customer->nama }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->datel }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->agency_psb ?: '-' }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">Billing {{ $customer->billing_ke }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-danger px-2 py-1" style="font-size:0.65rem;">
                                            Belum Bayar
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-inbox fs-2 text-muted d-block mb-1"></i>
                                        <span class="text-muted" style="font-size:0.85rem;">Tidak ada customer belum lunas untuk sales agency ini</span>
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
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">SND</th>
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
                                    <td class="px-3 py-2 align-middle" style="font-size:0.8rem;">{{ $agencyCustomers->firstItem() + $index }}</td>
                                    <td class="px-3 py-2 align-middle text-nowrap" style="font-size:0.8rem;">{{ $customer->snd ?: '-' }}</td>
                                    <td class="px-3 py-2 align-middle" style="font-size:0.8rem; font-weight: 600; color: #000361;">{{ $customer->nama }}</td>
                                    <td class="px-3 py-2 align-middle text-nowrap" style="font-size:0.8rem;">{{ $customer->datel }}</td>
                                    <td class="px-3 py-2 align-middle text-nowrap" style="font-size:0.8rem;">{{ $customer->produk }}</td>
                                    <td class="px-3 py-2 align-middle text-end text-nowrap" style="font-size:0.8rem;">
                                        <span style="font-size:0.75em; margin-right:2px;" class="text-muted">Rp</span>{{ number_format($customer->tag_inet ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 align-middle text-end text-nowrap" style="font-size:0.8rem;">
                                        <span style="font-size:0.75em; margin-right:2px;" class="text-muted">Rp</span>{{ number_format($customer->tag_tlp ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 align-middle text-end text-nowrap" style="font-size:0.8rem; font-weight: bold;">
                                        <span style="font-size:0.75em; margin-right:2px;" class="text-muted">Rp</span>{{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 align-middle text-nowrap" style="font-size:0.8rem;">{{ $customer->sales_agency ?: '-' }}</td>
                                    <td class="px-3 py-2 align-middle text-end text-nowrap" style="font-size:0.8rem;">
                                        <span style="font-size:0.75em; margin-right:2px;" class="text-muted">Rp</span>{{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 align-middle text-nowrap">
                                        <span class="badge bg-{{ ($customer->billing_ke ?? 0) <= 2 ? 'primary' : 'secondary' }} px-2 py-1" style="font-size:0.65rem;">
                                            Billing {{ $customer->billing_ke }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle text-center text-nowrap">
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

    @elseif(request('search'))
        {{-- CASE 4: Search View - Daftar Customer Hasil Pencarian --}}
        <div class="card border-0 shadow-sm w-100 overflow-hidden mb-5">
            <div class="card-header bg-white border-0 py-3 px-3">
                <h6 class="mb-0 fw-bold text-primary">
                    HASIL PENCARIAN CUSTOMER BELUM LUNAS - "{{ request('search') }}"
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive w-100">
                    <table class="table table-hover table-striped align-middle mb-0 w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">NO</th>
                                <th class="px-3 py-2 text-nowrap" style="font-size:0.7rem;">SND</th>
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
                            @forelse($searchCustomers as $index => $customer)
                                <tr>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $searchCustomers->firstItem() + $index }}</td>
                                    <td class="px-3 py-2 text-nowrap" style="font-size:0.8rem; font-family: monospace;">{{ $customer->snd ?: '-' }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem; font-weight: 600; color: #000361;">{{ $customer->nama }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->datel }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">{{ $customer->agency_psb ?: '-' }}</td>
                                    <td class="px-3 py-2" style="font-size:0.8rem;">Billing {{ $customer->billing_ke }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-danger px-2 py-1" style="font-size:0.65rem;">
                                            Belum Bayar
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-search fs-2 text-muted d-block mb-1"></i>
                                        <span class="text-muted" style="font-size:0.85rem;">Data tidak ditemukan</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <small class="text-muted">Menampilkan {{ $searchCustomers->firstItem() ?? 0 }} - {{ $searchCustomers->lastItem() ?? 0 }} dari {{ number_format($searchCustomers->total()) }} data</small>
                    <div>{{ $searchCustomers->links() }}</div>
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
                                            <td class="text-center text-white text-nowrap px-2 py-1 ssl-grid-cell" style="background-color:#868e96; font-size: 0.75rem;" onclick="showDetail('{{ $datelItem }}', 'All')">{{ $rowBlm }}</td>
                                            <td class="text-end text-white text-nowrap px-2 py-1 ssl-grid-cell" style="background-color:#868e96; font-size:0.65rem;" onclick="showDetail('{{ $datelItem }}', 'All')">Rp {{ number_format($rowBlmRp, 0, ',', '.') }}</td>
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
                                            <td class="text-center text-nowrap px-2 py-1 ssl-grid-cell" style="font-size: 0.65rem; background-color: #eaeaea;" onclick="showDetail('Nasional', {{ $billingKe }})">{{ $total['blm_bayar'] }}</td>
                                            <td class="text-center text-nowrap px-2 py-1 text-danger ssl-grid-cell" style="font-size:0.6rem; background-color: #eaeaea;" onclick="showDetail('Nasional', {{ $billingKe }})">Rp {{ number_format($total['blm_bayar_rp'] ?? 0, 0, ',', '.') }}</td>
                                        @endforeach
                                        @php
                                            $allBlm = 0; $allBlmRp = 0;
                                            foreach(range(1, 6) as $billingKe) {
                                                $total = $billingTotals[$billingKe] ?? ['blm_bayar' => 0, 'blm_bayar_rp' => 0];
                                                $allBlm += $total['blm_bayar'];
                                                $allBlmRp += $total['blm_bayar_rp'];
                                            }
                                        @endphp
                                        <td class="text-center text-white text-nowrap px-2 py-1 ssl-grid-cell" style="background-color:#6c757d; font-size: 0.65rem;" onclick="showDetail('Nasional', 'All')">{{ $allBlm }}</td>
                                        <td class="text-center text-white text-nowrap px-2 py-1 ssl-grid-cell" style="background-color:#6c757d; font-size:0.6rem;" onclick="showDetail('Nasional', 'All')">Rp {{ number_format($allBlmRp, 0, ',', '.') }}</td>
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
let currentModalCustomers = []; // Simpan data customer modal secara global
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
    currentModalCustomers = response.customers;
    const customers = currentModalCustomers;
    
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
                        <th rowspan="2" class="align-middle text-center text-nowrap px-2 py-1" style="min-width:70px; background-color: #0b2240; color: white;">Aksi</th>
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
                <td class="text-nowrap px-2 py-1">
                    <span>${cust.ncli || '-'}</span>
                    <button type="button"
                            class="btn btn-sm ${(cust.jumlah_snd || 1) > 1 ? 'btn-primary' : 'btn-secondary'} ms-1 px-2 py-0"
                            style="font-size:0.58rem; border-radius:999px;"
                            onclick="showSndGroupDetail(${index})"
                            title="Klik untuk lihat rincian SND">
                            ${cust.jumlah_snd || 1} SND
                    </button>
                </td>
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
                <td class="text-center text-nowrap px-2 py-1">
                    <button type="button" class="btn btn-sm btn-primary px-2 py-0" style="font-size:0.65rem;" onclick="exportSingleCustomerPdf(${index})">
                        <i class="bi bi-download"></i> Unduh
                    </button>
                </td>
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
                        <td colspan="15" class="text-nowrap px-2 py-1"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = html;
    document.getElementById('totalCustomerCount').textContent = response.total_customer;
}


// ============================================
// DETAIL SND DALAM GROUP NCLI
// ============================================
function showSndGroupDetail(index) {
    const cust = currentModalCustomers[index];

    if (!cust) {
        alert('Data customer tidak ditemukan.');
        return;
    }

    const details = Array.isArray(cust.detail_snd) ? cust.detail_snd : [];

    const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

    const detailRows = details.map((item, detailIndex) => `
        <tr>
            <td class="text-center">${detailIndex + 1}</td>
            <td><code>${item.snd || '-'}</code></td>
            <td><code>${item.snd_group || '-'}</code></td>
            <td>${item.produk || '-'}</td>
            <td class="text-end fw-semibold">Rp ${formatRupiah(item.tag_total)}</td>
        </tr>
    `).join('');

    const overlay = document.createElement('div');
    overlay.id = 'sndGroupDetailOverlay';
    overlay.style.position = 'fixed';
    overlay.style.inset = '0';
    overlay.style.zIndex = '2000';
    overlay.style.background = 'rgba(15, 23, 42, 0.45)';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.style.padding = '20px';

    overlay.innerHTML = `
        <div class="card border-0 shadow-lg" style="width:min(720px, 96vw); border-radius:16px; overflow:hidden;">
            <div class="card-header bg-white d-flex justify-content-between align-items-start py-3 px-4">
                <div>
                    <h6 class="mb-1 fw-bold" style="color:#000361;">
                        <i class="bi bi-diagram-3-fill me-2"></i>Detail SND dalam Grup
                    </h6>
                    <div class="small text-muted">
                        NCLI: <span class="fw-bold text-dark">${cust.ncli || '-'}</span>
                    </div>
                </div>
                <button type="button"
                        class="btn-close"
                        onclick="closeSndGroupDetail()"
                        aria-label="Close"></button>
            </div>

            <div class="card-body p-4">
                <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="small text-muted">Jumlah SND dalam NCLI</div>
                        <div class="fw-bold">${details.length} SND</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Total Grup</div>
                        <div class="fw-bold text-primary">
                            Rp ${formatRupiah(cust.tag_total)}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:50px;">#</th>
                                <th>SND</th>
                                <th>SND Group</th>
                                <th>Produk</th>
                                <th class="text-end">Tagihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${detailRows}
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL</td>
                                <td class="text-end text-primary">
                                    Rp ${formatRupiah(cust.tag_total)}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="small text-muted mt-3">
                    NCLI yang sama ditampilkan sebagai satu customer, sedangkan tagihan seluruh SND di dalam grup tetap dijumlahkan.
                </div>
            </div>

            <div class="card-footer bg-white text-end py-3 px-4">
                <button type="button"
                        class="btn btn-secondary btn-sm px-3"
                        onclick="closeSndGroupDetail()">
                    Tutup
                </button>
            </div>
        </div>
    `;

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closeSndGroupDetail();
        }
    });

    document.body.appendChild(overlay);
}

function closeSndGroupDetail() {
    const overlay = document.getElementById('sndGroupDetailOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// ============================================
// FUNGSI EXPORT EXCEL (Sudah Diganti ke Backend)
// ============================================
// Fungsi exportDetailToExcel dihapus karena sekarang menggunakan tag <a> yang mengarah ke route backend Laravel
// ============================================
// Filter reset logic and submission
function filterChange(type) {
    const datelVal = document.getElementById('filter-datel').value;
    const agencySelect = document.getElementById('filter-agency');
    const salesSelect = document.getElementById('filter-sales');

    if (type === 'datel') {
        agencySelect.innerHTML = '<option value="">Memuat...</option>';
        salesSelect.innerHTML = '<option value="">Memuat...</option>';
        
        // Fetch Agencies
        fetch(`/filter/agencies?datel=${encodeURIComponent(datelVal)}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">Semua Agency</option>';
                data.forEach(item => {
                    html += `<option value="${item}">${item}</option>`;
                });
                agencySelect.innerHTML = html;
            });
            
        // Fetch Sales
        fetch(`/filter/sales?datel=${encodeURIComponent(datelVal)}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">Semua Sales</option>';
                data.forEach(item => {
                    html += `<option value="${item}">${item}</option>`;
                });
                salesSelect.innerHTML = html;
            });
    } else if (type === 'agency') {
        const agencyVal = agencySelect.value;
        salesSelect.innerHTML = '<option value="">Memuat...</option>';
        
        // Fetch Sales
        fetch(`/filter/sales?datel=${encodeURIComponent(datelVal)}&agency=${encodeURIComponent(agencyVal)}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">Semua Sales</option>';
                data.forEach(item => {
                    html += `<option value="${item}">${item}</option>`;
                });
                salesSelect.innerHTML = html;
            });
    }
}

// Function to export detail modal content as PDF
// Function to export detail modal content as PDF
function exportDetailPdf() {
    const billingKe = document.getElementById('detailBillingKe').textContent;
    const datel = document.getElementById('detailDatel').textContent;
    
    // Create temporary styled print wrapper
    const pdfWrapper = document.createElement('div');
    pdfWrapper.style.padding = '20px';
    pdfWrapper.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    pdfWrapper.style.backgroundColor = '#ffffff';
    
    // Add header branding and custom styles for PDF output
    pdfWrapper.innerHTML = `
        <style>
            /* Reset table width and layout to fit landscape A4 page */
            #detailTable {
                width: 100% !important;
                min-width: 100% !important;
                table-layout: fixed !important;
                border-collapse: collapse !important;
                margin-top: 15px !important;
            }
            #detailTable th, #detailTable td {
                font-size: 7px !important;
                padding: 4px 5px !important;
                word-wrap: break-word !important;
                white-space: normal !important;
            }
            #detailTable th {
                background-color: #000361 !important;
                color: #ffffff !important;
                border: 1px solid #000361 !important;
                text-align: left !important;
            }
            #detailTable td {
                border: 1px solid #e2e8f0 !important;
            }
            #detailTable tr:nth-child(even) td {
                background-color: #f8fafc !important;
            }
            
            /* Precise column widths for PDF.
               SND Group and NCLI are intentionally included for auditability. */
            #detailTable th:nth-child(1), #detailTable td:nth-child(1) { width: 3% !important; text-align: center !important; }
            #detailTable th:nth-child(2), #detailTable td:nth-child(2) { width: 7% !important; text-align: center !important; }
            #detailTable th:nth-child(3), #detailTable td:nth-child(3) { width: 10% !important; }
            #detailTable th:nth-child(4), #detailTable td:nth-child(4) { width: 10% !important; }
            #detailTable th:nth-child(5), #detailTable td:nth-child(5) { width: 8% !important; }
            #detailTable th:nth-child(6), #detailTable td:nth-child(6) { width: 15% !important; font-weight: 600 !important; }
            #detailTable th:nth-child(7), #detailTable td:nth-child(7) { width: 17% !important; }
            #detailTable th:nth-child(8), #detailTable td:nth-child(8) { width: 7% !important; }
            #detailTable th:nth-child(9), #detailTable td:nth-child(9) { width: 8% !important; }
            #detailTable th:nth-child(10), #detailTable td:nth-child(10) { width: 7% !important; }
            #detailTable th:nth-child(14), #detailTable td:nth-child(14) { width: 8% !important; text-align: right !important; font-weight: 700 !important; color: #000361 !important; }

            /* Hide tfoot and double header sub-row */
            #detailTable tfoot {
                display: none !important;
            }
            #detailTable thead tr:nth-child(2) {
                display: none !important;
            }

            /* Hide unnecessary columns to fit landscape A4 page.
               Columns 4 (SND Group) and 5 (NCLI) stay visible. */
            #detailTable th:nth-child(11), #detailTable td:nth-child(11),
            #detailTable th:nth-child(12), #detailTable td:nth-child(12),
            #detailTable th:nth-child(13), #detailTable td:nth-child(13),
            #detailTable th:nth-child(15), #detailTable td:nth-child(15),
            #detailTable th:nth-child(16), #detailTable td:nth-child(16),
            #detailTable th:nth-child(17), #detailTable td:nth-child(17),
            #detailTable th:nth-child(18), #detailTable td:nth-child(18),
            #detailTable th:nth-child(19), #detailTable td:nth-child(19),
            #detailTable th:nth-child(20), #detailTable td:nth-child(20),
            #detailTable th:nth-child(21), #detailTable td:nth-child(21),
            #detailTable th:nth-child(22), #detailTable td:nth-child(22),
            #detailTable th:nth-child(23), #detailTable td:nth-child(23),
            #detailTable th:nth-child(24), #detailTable td:nth-child(24),
            #detailTable th:nth-child(25), #detailTable td:nth-child(25),
            #detailTable th:nth-child(26), #detailTable td:nth-child(26),
            #detailTable th:nth-child(27), #detailTable td:nth-child(27),
            #detailTable th:nth-child(28), #detailTable td:nth-child(28),
            #detailTable th:nth-child(29), #detailTable td:nth-child(29) {
                display: none !important;
            }
            
            /* Summary cards layout inside PDF */
            .row.g-2.mb-3 {
                display: flex !important;
                flex-direction: row !important;
                gap: 15px !important;
                margin-bottom: 20px !important;
            }
            .row.g-2.mb-3 > div {
                flex: 1 !important;
                width: 32% !important;
            }
            .card {
                border: 1px solid #e2e8f0 !important;
                border-radius: 8px !important;
                padding: 10px 15px !important;
                box-shadow: none !important;
            }
            .bg-primary {
                background-color: #f8fafc !important;
                border-left: 4px solid #000361 !important;
                color: #000361 !important;
            }
            .bg-warning {
                background-color: #f8fafc !important;
                border-left: 4px solid #e2001a !important;
                color: #e2001a !important;
            }
            .bg-info {
                background-color: #f8fafc !important;
                border-left: 4px solid #0284c7 !important;
                color: #0284c7 !important;
            }
            .card-body {
                padding: 0 !important;
            }
            .card small {
                font-size: 8px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                color: #64748b !important;
                display: block !important;
                margin-bottom: 4px !important;
            }
            .card h5 {
                font-size: 14px !important;
                font-weight: 700 !important;
                margin: 0 !important;
            }
            code {
                font-family: 'Consolas', 'Courier New', Courier, monospace !important;
                font-size: 8px !important;
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                padding: 2px 4px !important;
                border-radius: 4px !important;
            }
        </style>
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000361; padding-bottom: 10px; margin-bottom: 20px;">
            <div>
                <h4 style="margin: 0; color: #000361; font-weight: bold; font-size: 16px;">PROSES PEMBAYARAN BILLING 1 - 6</h4>
                <div style="font-size: 11px; color: #6c757d; margin-top: 4px;">Detail Billing: ${billingKe} | Datel: ${datel}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 14px; font-weight: bold; color: #dc3545; letter-spacing: 0.5px;">CONFIDENTIAL</div>
                <div style="font-size: 9px; color: #868e96;">Telkom Customer Management System</div>
            </div>
        </div>
        
        <div style="margin-bottom: 15px;">
            ${document.getElementById('detailContent').innerHTML}
        </div>
        
        <div style="border-top: 1px dashed #dee2e6; padding-top: 10px; margin-top: 20px; text-align: center; font-size: 9px; color: #868e96;">
            Laporan Detail ini diunduh secara resmi melalui Telkom Customer Management System.<br>
            © ${new Date().getFullYear()} Telkom Indonesia. All Rights Reserved.
        </div>
    `;
    
    // Remove pagination, buttons, forms inside print container
    pdfWrapper.querySelectorAll('.pagination, .btn, button, select, input, #btnExportExcel').forEach(el => el.remove());
    
    const originalScrollX = window.scrollX;
    const originalScrollY = window.scrollY;
    window.scrollTo(0, 0);
    document.body.insertBefore(pdfWrapper, document.body.firstChild);
    
    const opt = {
        margin:       10,
        filename:     `Laporan_Detail_Billing_${billingKe}_${datel.replace(/[^a-z0-9]/gi, '_')}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.5, useCORS: true, logging: false, scrollX: 0, scrollY: 0 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
    
    setTimeout(() => {
        html2pdf().from(pdfWrapper).set(opt).save().then(() => {
            document.body.removeChild(pdfWrapper);
            window.scrollTo(originalScrollX, originalScrollY);
        }).catch(err => {
            console.error(err);
            document.body.removeChild(pdfWrapper);
            window.scrollTo(originalScrollX, originalScrollY);
            alert('Gagal menghasilkan file PDF.');
        });
    }, 150);
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
    
    // Grab all cards that represent data (excluding the filter card)
    const dataCards = Array.from(document.querySelectorAll('.container-fluid > .card'))
        .filter(card => !card.querySelector('#dashboardFilterForm'));

    let cardsHTML = '';
    dataCards.forEach(card => {
        cardsHTML += `<div style="margin-bottom: 25px;">${card.outerHTML}</div>`;
    });
    
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
        
        <div>
            ${cardsHTML}
        </div>
        
        <div style="border-top: 1px dashed #dee2e6; padding-top: 10px; margin-top: 30px; text-align: center; font-size: 9px; color: #868e96;">
            Laporan Rekap ini diunduh secara resmi melalui Telkom Customer Management System.<br>
            © ${new Date().getFullYear()} Telkom Indonesia. All Rights Reserved.
        </div>
    `;
    
    // Remove forms, buttons, pagination inside container
    pdfWrapper.querySelectorAll('.pagination, .btn, button, select, input, #dashboardFilterForm, #btnExportExcel').forEach(el => el.remove());
    
    const originalScrollX = window.scrollX;
    const originalScrollY = window.scrollY;
    window.scrollTo(0, 0);
    document.body.insertBefore(pdfWrapper, document.body.firstChild);
    
    const opt = {
        margin:       10,
        filename:     `Laporan_Rekap_Dashboard_${new Date().toISOString().slice(0,10)}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.5, useCORS: true, logging: false, scrollX: 0, scrollY: 0 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
    
    setTimeout(() => {
        html2pdf().from(pdfWrapper).set(opt).save().then(() => {
            document.body.removeChild(pdfWrapper);
            window.scrollTo(originalScrollX, originalScrollY);
        }).catch(err => {
            console.error(err);
            document.body.removeChild(pdfWrapper);
            window.scrollTo(originalScrollX, originalScrollY);
            alert('Gagal menghasilkan file PDF.');
        });
    }, 150);
}

// Function to export single customer card receipt / invoice layout as PDF
function exportSingleCustomerPdf(index) {
    if (!currentModalCustomers || !currentModalCustomers[index]) {
        alert('Data customer tidak ditemukan.');
        return;
    }
    
    const cust = currentModalCustomers[index];
    
    const pdfWrapper = document.createElement('div');
    pdfWrapper.style.width = '800px';
    pdfWrapper.style.padding = '20px';
    pdfWrapper.style.margin = '0 auto';
    pdfWrapper.style.fontFamily = "'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif";
    pdfWrapper.style.backgroundColor = '#ffffff';
    
    // Set up single customer invoice HTML (exact Figma mockup replica)
    pdfWrapper.innerHTML = `
        <style>
            .invoice-container {
                width: 100% !important;
                background-color: #ffffff !important;
                color: #1e293b !important;
                box-sizing: border-box !important;
            }
            
            /* Top Card */
            .top-card {
                display: flex !important;
                align-items: center !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 12px !important;
                padding: 18px 25px !important;
                margin-bottom: 20px !important;
                background: #ffffff !important;
            }
            .top-card-left {
                flex: 1.5 !important;
            }
            .top-card-divider {
                width: 1px !important;
                height: 50px !important;
                background-color: #cbd5e1 !important;
                margin: 0 30px !important;
            }
            .top-card-right {
                flex: 1 !important;
                text-align: center !important;
            }
            .label-muted {
                font-size: 13px !important;
                color: #64748b !important;
                font-weight: 500 !important;
                margin-bottom: 8px !important;
            }
            .value-bold {
                font-size: 18px !important;
                font-weight: 700 !important;
                color: #0f172a !important;
            }
            .status-badge-pill {
                display: inline-block !important;
                padding: 6px 24px !important;
                font-size: 11px !important;
                font-weight: 700 !important;
                border-radius: 20px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                background-color: ${cust.status_bayar == 'Sdh Bayar' ? '#dcfce7' : '#fee2e2'} !important;
                color: ${cust.status_bayar == 'Sdh Bayar' ? '#15803d' : '#ef4444'} !important;
                border: 1px solid ${cust.status_bayar == 'Sdh Bayar' ? '#bbf7d0' : '#fecaca'} !important;
            }
            
            /* General Cards */
            .info-card {
                border: 1px solid #cbd5e1 !important;
                border-radius: 10px !important;
                overflow: hidden !important;
                background: #ffffff !important;
                margin-bottom: 20px !important;
            }
            .info-card-header {
                background-color: #eff6ff !important; /* Soft Telkom Blue background */
                border-bottom: 1px solid #cbd5e1 !important;
                padding: 10px 15px !important;
                font-size: 11px !important;
                font-weight: 700 !important;
                color: #1e40af !important; /* Blue text */
                display: flex !important;
                align-items: center !important;
                letter-spacing: 0.5px !important;
            }
            .info-card-header i {
                font-size: 13px !important;
                margin-right: 8px !important;
                color: #1e40af !important;
            }
            
            /* Two Column Row */
            .two-col-row {
                display: flex !important;
                gap: 20px !important;
                margin-bottom: 20px !important;
            }
            .two-col-row > div {
                flex: 1 !important;
                margin-bottom: 0 !important;
            }
            
            /* Card Table styles */
            .card-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .card-table td {
                padding: 10px 15px !important;
                font-size: 11px !important;
                border-bottom: 1px solid #e2e8f0 !important;
                color: #334155 !important;
            }
            .card-table tr:last-child td {
                border-bottom: none !important;
            }
            .card-table td.col-label {
                width: 35% !important;
                color: #64748b !important;
            }
            .card-table td.col-val {
                font-weight: 500 !important;
                color: #0f172a !important;
            }
            
            /* Highlighted Row for Tagihan Total */
            .total-row td {
                background-color: #eff6ff !important;
                color: #1e40af !important;
                font-weight: 700 !important;
            }
            .total-row td.col-val {
                color: #1e40af !important;
                font-weight: 700 !important;
            }
            
            /* Address body */
            .address-body {
                padding: 15px !important;
                font-size: 11px !important;
                color: #334155 !important;
                line-height: 1.5 !important;
                background-color: #ffffff !important;
            }
        </style>
        
        <div class="invoice-container">
            <!-- Top Card -->
            <div class="top-card">
                <div class="top-card-left">
                    <div class="label-muted">Nama Customer</div>
                    <div class="value-bold">${cust.nama || '-'}</div>
                </div>
                <div class="top-card-divider"></div>
                <div class="top-card-right">
                    <div class="label-muted">Status</div>
                    <span class="status-badge-pill">${cust.status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar'}</span>
                </div>
            </div>
            
            <!-- Two Column Section -->
            <div class="two-col-row">
                <!-- Informasi Customer -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-person-fill"></i> INFORMASI CUSTOMER
                    </div>
                    <table class="card-table">
                        <tr>
                            <td class="col-label">NCLI</td>
                            <td class="col-val">${cust.ncli || '-'}</td>
                        </tr>
                        <tr>
                            <td class="col-label">SND</td>
                            <td class="col-val">${cust.snd || '-'}</td>
                        </tr>
                        <tr>
                            <td class="col-label">Produk</td>
                            <td class="col-val">${cust.produk || '-'}</td>
                        </tr>
                        <tr>
                            <td class="col-label">Billing</td>
                            <td class="col-val">Billing Ke-${cust.billing_ke || '-'}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Informasi Tagihan -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="bi bi-file-earmark-text-fill"></i> INFORMASI TAGIHAN
                    </div>
                    <table class="card-table">
                        <tr>
                            <td class="col-label">Internet</td>
                            <td class="col-val">Rp ${new Intl.NumberFormat('id-ID').format(cust.tag_inet || 0)}</td>
                        </tr>
                        <tr>
                            <td class="col-label">Telepon</td>
                            <td class="col-val">Rp ${new Intl.NumberFormat('id-ID').format(cust.tag_tlp || 0)}</td>
                        </tr>
                        <tr class="total-row">
                            <td class="col-label" style="color: #1e40af !important;">Total Tagihan</td>
                            <td class="col-val">Rp ${new Intl.NumberFormat('id-ID').format(cust.tag_total || 0)}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Agency & Lokasi -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-geo-alt-fill"></i> AGENCY & LOKASI
                </div>
                <table class="card-table">
                    <tr>
                        <td class="col-label" style="width: 25% !important;">Agency</td>
                        <td class="col-val">${cust.agency_psb || '-'}</td>
                    </tr>
                    <tr>
                        <td class="col-label" style="width: 25% !important;">Sales</td>
                        <td class="col-val">${cust.sales_agency || '-'}</td>
                    </tr>
                    <tr>
                        <td class="col-label" style="width: 25% !important;">STO</td>
                        <td class="col-val">${cust.sto || '-'}</td>
                    </tr>
                    <tr>
                        <td class="col-label" style="width: 25% !important;">DATEL</td>
                        <td class="col-val">${cust.datel || '-'}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Alamat -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-house-door-fill"></i> ALAMAT
                </div>
                <div class="address-body">
                    ${cust.alamat || '-'}
                </div>
            </div>
        </div>
    `;
    
    // Fix html2canvas scroll bug by scrolling to top and inserting at the very top of the body
    const originalScrollX = window.scrollX;
    const originalScrollY = window.scrollY;
    window.scrollTo(0, 0);
    
    // Insert as first child so it sits exactly at coordinate 0,0
    document.body.insertBefore(pdfWrapper, document.body.firstChild);
    
    const opt = {
        margin:       10,
        filename:     `Laporan_Customer_${cust.nama.replace(/[^a-z0-9]/gi, '_')}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2.5, useCORS: true, logging: false, scrollX: 0, scrollY: 0 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    // Beri jeda 150ms agar browser sempat me-render elemen (mencegah bug blank putih)
    setTimeout(() => {
        html2pdf().from(pdfWrapper).set(opt).save().then(() => {
            document.body.removeChild(pdfWrapper);
            window.scrollTo(originalScrollX, originalScrollY);
        }).catch(err => {
            console.error(err);
            document.body.removeChild(pdfWrapper);
            window.scrollTo(originalScrollX, originalScrollY);
            alert('Gagal menghasilkan file PDF.');
        });
    }, 150);
}
</script>
@endpush