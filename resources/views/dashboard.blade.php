@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .container-fluid {
        overflow-x: hidden !important;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    .row {
        margin-left: -5px !important;
        margin-right: -5px !important;
    }
    .row > * {
        padding-left: 5px !important;
        padding-right: 5px !important;
    }
    .card {
        width: 100% !important;
        overflow: hidden !important;
    }
    .card-body {
        padding: 15px 12px !important;
    }
    .card-body h3 {
        font-size: 1.3rem !important;
    }
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .table-responsive table {
        min-width: 600px !important;
        width: 100% !important;
    }
    .table-responsive table th,
    .table-responsive table td {
        white-space: nowrap !important;
        padding: 8px 10px !important;
        font-size: 0.85rem !important;
    }
    .badge {
        font-size: 0.7rem !important;
        padding: 4px 10px !important;
    }
    .chart-container {
        height: 220px !important;
    }
    .col-xl-2, .col-lg-4, .col-md-6 {
        flex: 0 0 auto !important;
        width: 100% !important;
    }
    @media (min-width: 768px) {
        .col-md-6 { width: 50% !important; }
    }
    @media (min-width: 992px) {
        .col-lg-4 { width: 33.333% !important; }
    }
    @media (min-width: 1200px) {
        .col-xl-2 { width: 16.666% !important; }
    }
    .progress {
        height: 4px !important;
    }
    .card .card-header {
        padding: 10px 15px !important;
    }
    .card .card-header h6 {
        font-size: 0.9rem !important;
    }
    .transition-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .transition-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 40px rgba(0,0,0,0.10) !important;
    }
    .billing-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .billing-link:hover .card {
        border-color: #E2001A !important;
    }
    .table-hotd th, .table-hotd td {
        padding: 4px 6px !important;
        font-size: 0.65rem !important;
        text-align: center;
    }
    .table-hotd .bg-belum { background-color: #dc3545; color: white; }
    .table-hotd .bg-sudah { background-color: #28a745; color: white; }
    .table-hotd .bg-total { background-color: #6c757d; color: white; }
    .hotd-row {
        cursor: pointer !important;
        transition: background-color 0.2s;
    }
    .hotd-row:hover {
        background-color: #f0f8ff !important;
    }
    #detailContent .table {
        font-size: 0.7rem;
    }
    #detailContent .table th,
    #detailContent .table td {
        padding: 4px 6px;
        vertical-align: middle;
        white-space: nowrap;
    }
    #detailContent .summary-card {
        border-radius: 8px;
        padding: 10px 15px;
    }
</style>

<div class="container-fluid">

    {{-- STATISTICS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-people fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Customer</h6>
                            <h3 class="mb-0" style="font-size:1.5rem;">{{ number_format($totalCustomer ?? 0) }}</h3>
                            <small class="text-success" style="font-size:0.7rem;">Lunas: {{ number_format($totalLunas ?? 0) }}</small>
                            <small class="text-danger ms-2" style="font-size:0.7rem;">Belum: {{ number_format($totalBelumLunas ?? 0) }}</small>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ $persentaseLunas ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted" style="font-size:0.7rem;">{{ number_format($persentaseLunas ?? 0, 1) }}% Lunas</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-cash-stack fs-3 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Tagihan</h6>
                            <h3 class="mb-0" style="font-size:1.2rem;">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</h3>
                            <small class="text-muted" style="font-size:0.7rem;">{{ number_format($persentaseLunas ?? 0, 1) }}% Terbayar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-building fs-3 text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Agency</h6>
                            <h3 class="mb-0" style="font-size:1.5rem;">{{ number_format($totalAgency ?? 0) }}</h3>
                            <small class="text-muted" style="font-size:0.7rem;">{{ number_format($totalSales ?? 0) }} Sales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-clock-history fs-3 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Saldo</h6>
                            <h3 class="mb-0" style="font-size:1.2rem;">Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</h3>
                            <small class="text-muted" style="font-size:0.7rem;">Sisa tagihan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BILLING SUMMARY 1-6 --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                <i class="bi bi-file-invoice-dollar me-2"></i> Rekap Billing Customer 1-6 (HOTD)
            </h6>
            <span class="badge bg-info text-white" style="font-size:0.7rem;">
                <i class="bi bi-info-circle me-1"></i> Billing 1-6
            </span>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @if(!empty($billingSummary) && is_iterable($billingSummary))
                    @forelse($billingSummary as $billing)
                        <div class="col-xl-2 col-lg-4 col-md-6">
                            <a href="{{ route('billing.detail', $billing->billing_ke) }}" class="billing-link">
                                <div class="card border-0 shadow-sm h-100 transition-card" style="background:#f8f9fa;">
                                    <div class="card-body text-center p-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Billing {{ $billing->billing_ke ?? 0 }}</h6>
                                            <span class="badge bg-{{ ($billing->billing_ke ?? 0) <= 2 ? 'primary' : 'secondary' }}" style="font-size:0.6rem;">
                                                {{ ($billing->billing_ke ?? 0) <= 2 ? 'Awal' : 'Berikutnya' }}
                                            </span>
                                        </div>
                                        <h4 class="mb-0 mt-1" style="font-size:1.3rem;">{{ number_format($billing->total_customer ?? 0) }}</h4>
                                        <small class="text-muted" style="font-size:0.65rem;">Customer</small>
                                        <hr class="my-1">
                                        <div class="text-success fw-bold" style="font-size:0.75rem;">
                                            Rp {{ number_format($billing->total_tagihan ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-success" style="font-size:0.6rem;">
                                                <i class="bi bi-check-circle-fill"></i> {{ number_format($billing->lunas ?? 0) }}
                                            </small>
                                            <small class="text-danger" style="font-size:0.6rem;">
                                                <i class="bi bi-x-circle-fill"></i> {{ number_format($billing->belum_lunas ?? 0) }}
                                            </small>
                                        </div>
                                        <div class="progress mt-1" style="height: 3px;">
                                            @php
                                                $total = $billing->total_customer ?? 0;
                                                $lunas = $billing->lunas ?? 0;
                                                $percent = $total > 0 ? ($lunas / $total) * 100 : 0;
                                            @endphp
                                            <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <small class="text-muted" style="font-size:0.6rem;">{{ number_format($percent, 1) }}% Lunas</small>
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

    {{-- ============================================ --}}
    {{-- HOTD - DETAIL BILLING PER DATEL --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                <i class="bi bi-table me-2"></i> Detail Billing 1-6 per Datel (HOTD)
                <small class="text-muted ms-2" style="font-size:0.7rem;">Klik baris untuk lihat detail</small>
            </h6>
            <span class="badge bg-success" style="font-size:0.7rem;">
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

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm table-hotd mb-0">
                        <thead>
                            <tr>
                                <th rowspan="3" style="vertical-align:middle;min-width:100px;">DATEL</th>
                                @foreach(range(1, 6) as $billingKe)
                                    <th colspan="6" style="text-align:center;background-color:{{ $colors[$billingKe-1] }};color:white;min-width:150px;">
                                        Billing {{ $billingKe }}
                                    </th>
                                @endforeach
                                <th colspan="2" rowspan="3" style="vertical-align:middle;text-align:center;background-color:#6c757d;color:white;min-width:100px;">
                                    TOTAL ALL
                                </th>
                            </tr>
                            <tr>
                                @foreach(range(1, 6) as $billingKe)
                                    <th colspan="3" style="text-align:center;background-color:#dc3545;color:white;">Belum Bayar</th>
                                    <th colspan="3" style="text-align:center;background-color:#28a745;color:white;">Sudah Bayar</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach(range(1, 6) as $billingKe)
                                    <th style="text-align:center;min-width:35px;">SL</th>
                                    <th style="text-align:center;min-width:70px;">Rp</th>
                                    <th style="text-align:center;min-width:35px;">%</th>
                                    <th style="text-align:center;min-width:35px;">SL</th>
                                    <th style="text-align:center;min-width:70px;">Rp</th>
                                    <th style="text-align:center;min-width:35px;">%</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
    @foreach($datels as $datel)
        @php
            $billingKeForDatel = $datelBillingMap[$datel] ?? 1;
        @endphp
        <tr>
            <td><strong>{{ $datel }}</strong></td>
            @foreach(range(1, 6) as $billingKe)
                @php
                    $data = $grouped->get($billingKe, collect())->firstWhere('datel', $datel);
                    $totalCust = $billingTotals[$billingKe]['total_customer'] ?? 1;
                    $blm = $data->blm_bayar ?? 0;
                    $sdh = $data->sdh_bayar ?? 0;
                    $blmRp = $data->blm_bayar_rp ?? 0;
                    $sdhRp = $data->sdh_bayar_rp ?? 0;
                @endphp
                <td>{{ $blm }}</td>
                <td class="clickable-rp" 
                    style="font-size:0.6rem;white-space:nowrap;cursor:pointer;color:#0d6efd;text-decoration:underline;"
                    onclick="showDetail('{{ $datel }}', {{ $billingKe }})">
                    Rp {{ number_format($blmRp, 0, ',', '.') }}
                </td>
                <td>{{ $totalCust > 0 ? round(($blm / $totalCust) * 100) : 0 }}%</td>
                <td>{{ $sdh }}</td>
                <td class="clickable-rp" 
                    style="font-size:0.6rem;white-space:nowrap;cursor:pointer;color:#0d6efd;text-decoration:underline;"
                    onclick="showDetail('{{ $datel }}', {{ $billingKe }})">
                    Rp {{ number_format($sdhRp, 0, ',', '.') }}
                </td>
                <td>{{ $totalCust > 0 ? round(($sdh / $totalCust) * 100) : 0 }}%</td>
            @endforeach
            @php
                $totalBlm = 0; $totalSdh = 0; $totalBlmRp = 0; $totalSdhRp = 0;
                foreach(range(1, 6) as $billingKe) {
                    $data = $grouped->get($billingKe, collect())->firstWhere('datel', $datel);
                    if ($data) {
                        $totalBlm += $data->blm_bayar ?? 0;
                        $totalSdh += $data->sdh_bayar ?? 0;
                        $totalBlmRp += $data->blm_bayar_rp ?? 0;
                        $totalSdhRp += $data->sdh_bayar_rp ?? 0;
                    }
                }
            @endphp
            <td style="font-weight:bold;">{{ $totalBlm + $totalSdh }}</td>
            <td style="font-weight:bold;font-size:0.6rem;white-space:nowrap;">Rp {{ number_format($totalBlmRp + $totalSdhRp, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td>GRAND TOTAL</td>
                                @foreach(range(1, 6) as $billingKe)
                                    @php
                                        $total = $billingTotals[$billingKe] ?? ['blm_bayar' => 0, 'sdh_bayar' => 0, 'blm_bayar_rp' => 0, 'sdh_bayar_rp' => 0, 'total_customer' => 1];
                                    @endphp
                                    <td>{{ $total['blm_bayar'] }}</td>
                                    <td style="font-size:0.6rem;white-space:nowrap;">Rp {{ number_format($total['blm_bayar_rp'] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $total['total_customer'] > 0 ? round((($total['blm_bayar'] ?? 0) / $total['total_customer']) * 100) : 0 }}%</td>
                                    <td>{{ $total['sdh_bayar'] }}</td>
                                    <td style="font-size:0.6rem;white-space:nowrap;">Rp {{ number_format($total['sdh_bayar_rp'] ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $total['total_customer'] > 0 ? round((($total['sdh_bayar'] ?? 0) / $total['total_customer']) * 100) : 0 }}%</td>
                                @endforeach
                                @php
                                    $allBlm = 0; $allSdh = 0; $allBlmRp = 0; $allSdhRp = 0;
                                    foreach(range(1, 6) as $billingKe) {
                                        $total = $billingTotals[$billingKe] ?? ['blm_bayar' => 0, 'sdh_bayar' => 0, 'blm_bayar_rp' => 0, 'sdh_bayar_rp' => 0];
                                        $allBlm += $total['blm_bayar'];
                                        $allSdh += $total['sdh_bayar'];
                                        $allBlmRp += $total['blm_bayar_rp'];
                                        $allSdhRp += $total['sdh_bayar_rp'];
                                    }
                                @endphp
                                <td style="background-color:#6c757d;color:white;">{{ $allBlm + $allSdh }}</td>
                                <td style="background-color:#6c757d;color:white;font-size:0.6rem;white-space:nowrap;">Rp {{ number_format($allBlmRp + $allSdhRp, 0, ',', '.') }}</td>
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

    {{-- STATUS CHART --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-2">
                    <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                        <i class="bi bi-check-circle me-2 text-success"></i> Status Pembayaran
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size:0.65rem;">Status</th>
                                    <th class="text-end" style="font-size:0.65rem;">Total</th>
                                    <th class="text-end" style="font-size:0.65rem;">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($statusBayar ?? []) as $status)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $status->status_bayar == 'Sdh Bayar' ? 'success' : 'danger' }}" style="font-size:0.65rem;">
                                                {{ $status->status_bayar }}
                                            </span>
                                        </td>
                                        <td class="text-end" style="font-size:0.8rem;">{{ number_format($status->total) }}</td>
                                        <td class="text-end" style="font-size:0.8rem;">
                                            {{ number_format(($totalCustomer > 0) ? (($status->total / $totalCustomer) * 100) : 0, 1) }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-2 text-muted" style="font-size:0.8rem;">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-2">
                    <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                        <i class="bi bi-pie-chart me-2"></i> Grafik Status Pembayaran
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="chart-container" style="height: 200px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT CUSTOMERS --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                <i class="bi bi-people me-2"></i> 10 Customer Terbaru
            </h6>
            <span class="badge bg-primary" style="font-size:0.7rem;">{{ count($latestCustomers ?? []) }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:0.65rem;">#</th>
                            <th style="font-size:0.65rem;">SND</th>
                            <th style="font-size:0.65rem;">Nama</th>
                            <th style="font-size:0.65rem;">Agency</th>
                            <th style="font-size:0.65rem;">Billing</th>
                            <th style="font-size:0.65rem;">Status</th>
                            <th class="text-end" style="font-size:0.65rem;">Tagihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($latestCustomers ?? []) as $index => $customer)
                            <tr>
                                <td style="font-size:0.8rem;">{{ $index + 1 }}</td>
                                <td><code style="font-size:0.7rem;">{{ $customer->snd }}</code></td>
                                <td style="font-size:0.8rem;">{{ Str::limit($customer->nama, 25) }}</td>
                                <td style="font-size:0.8rem;">{{ $customer->agency ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ ($customer->billing_ke ?? 0) <= 2 ? 'primary' : 'secondary' }}" style="font-size:0.6rem;">
                                        B{{ $customer->billing_ke }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $customer->status_bayar == 'Sdh Bayar' ? 'success' : 'danger' }}" style="font-size:0.6rem;">
                                        {{ $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                                    </span>
                                </td>
                                <td class="text-end" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3">
                                    <i class="bi bi-inbox fs-3 text-muted d-block mb-1"></i>
                                    <span class="text-muted" style="font-size:0.8rem;">Tidak ada data customer</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
                    <span class="badge bg-light text-dark ms-3">
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
                <button type="button" class="btn btn-info" onclick="exportDetailToExcel()">
                    <i class="bi bi-file-excel me-1"></i> Export Excel
                </button>
                <button type="button" class="btn btn-success" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ============================================
// CHART STATUS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const statusData = @json($statusBayar ?? []);
    if (statusData.length > 0) {
        const ctx = document.getElementById('statusChart').getContext('2d');
        const colors = ['#28a745', '#dc3545', '#ffc107', '#17a2b8'];
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: statusData.map(x => x.status_bayar),
                datasets: [{
                    data: statusData.map(x => x.total),
                    backgroundColor: colors.slice(0, statusData.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            usePointStyle: true,
                            font: { size: 10 }
                        }
                    }
                }
            }
        });
    }
});

// ============================================
// FUNGSI SHOW DETAIL - Dipanggil saat klik Rupiah
// ============================================
function showDetail(datel, billingKe) {
    console.log('Detail diklik! Datel:', datel, 'Billing:', billingKe);
    
    // Update judul modal
    document.getElementById('detailBillingKe').textContent = billingKe;
    document.getElementById('detailDatel').textContent = datel;
    
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
    
    // AJAX request dengan fetch
    fetch(`/hotd-detail/${billingKe}/${encodeURIComponent(datel)}`)
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
            <div class="col-md-2 col-4">
                <div class="card bg-primary text-white summary-card">
                    <div class="card-body py-2">
                        <small>Total Customer</small>
                        <h5 class="mb-0">${response.total_customer}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="card bg-success text-white summary-card">
                    <div class="card-body py-2">
                        <small>Sudah Bayar</small>
                        <h5 class="mb-0">${response.total_sdh_bayar}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-4">
                <div class="card bg-danger text-white summary-card">
                    <div class="card-body py-2">
                        <small>Belum Bayar</small>
                        <h5 class="mb-0">${response.total_blm_bayar}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card bg-warning text-dark summary-card">
                    <div class="card-body py-2">
                        <small>Total Tagihan</small>
                        <h5 class="mb-0">Rp ${new Intl.NumberFormat('id-ID').format(response.total_tagihan)}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card bg-info text-white summary-card">
                    <div class="card-body py-2">
                        <small>Total Saldo</small>
                        <h5 class="mb-0">Rp ${new Intl.NumberFormat('id-ID').format(response.total_saldo)}</h5>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="detailTable" style="font-size:0.7rem;">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2" style="vertical-align:middle;min-width:35px;">#</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:70px;">Status</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:90px;">SND</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:90px;">SND Group</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:90px;">NCLI</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:130px;">Nama</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:160px;">Alamat</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:60px;">STO</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:70px;">Datel</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">Produk</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:70px;">Eksepsi</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:70px;">New Bill</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:70px;">Usage</th>
                        <th colspan="1" class="text-center">Tagihan</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:90px;">Saldo</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:50px;">Umur</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:70px;">Billing</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:70px;">Paid L11</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">Tgl Paid</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:90px;">Paid Rp</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">Coll Agent</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">Tgl Klaim</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:90px;">Amount Klaim</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">User Klaim</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">Tgl Paid N-1</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">Agency PSB</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:80px;">Sales Agency</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:60px;">PPP</th>
                        <th rowspan="2" style="vertical-align:middle;min-width:90px;">Caring</th>
                    </tr>
                    <tr>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    customers.forEach((cust, index) => {
        html += `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <span class="badge ${cust.status_bayar == 'Sdh Bayar' ? 'bg-success' : 'bg-danger'}" style="font-size:0.6rem;">
                        ${cust.status_bayar || '-'}
                    </span>
                </td>
                <td><code>${cust.snd || '-'}</code></td>
                <td>${cust.snd_group || '-'}</td>
                <td>${cust.ncli || '-'}</td>
                <td style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${cust.nama || ''}">
                    ${cust.nama || '-'}
                </td>
                <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${cust.alamat || ''}">
                    ${cust.alamat || '-'}
                </td>
                <td>${cust.sto || '-'}</td>
                <td>${cust.datel || '-'}</td>
                <td>${cust.produk || '-'}</td>
                <td>${cust.eksepsi_desc || '-'}</td>
                <td>${cust.desc_newbill || '-'}</td>
                <td>${cust.usage_desc || '-'}</td>
                <td class="text-end fw-bold">${cust.tag_total ? new Intl.NumberFormat('id-ID').format(cust.tag_total) : '0'}</td>
                <td class="text-end">${cust.saldo ? new Intl.NumberFormat('id-ID').format(cust.saldo) : '0'}</td>
                <td>${cust.umur_customer || '-'}</td>
                <td>${cust.billing_ke || '-'}</td>
                <td>${cust.paid_l11 || '-'}</td>
                <td>${cust.tgl_paid || '-'}</td>
                <td class="text-end">${cust.paid_rp ? new Intl.NumberFormat('id-ID').format(cust.paid_rp) : '0'}</td>
                <td>${cust.coll_agent || '-'}</td>
                <td>${cust.tgl_klaim || '-'}</td>
                <td class="text-end">${cust.amount_klaim ? new Intl.NumberFormat('id-ID').format(cust.amount_klaim) : '0'}</td>
                <td>${cust.user_klaim || '-'}</td>
                <td>${cust.tgl_paid_n1 || '-'}</td>
                <td>${cust.agency_psb || '-'}</td>
                <td>${cust.sales_agency || '-'}</td>
                <td>${cust.ppp || '-'}</td>
                <td>${cust.caring_mybrains || '-'}</td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
                <tfoot class="table-secondary fw-bold">
                    <tr>
                        <td colspan="13" class="text-end">TOTAL</td>
                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(response.total_tagihan)}</td>
                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(response.total_saldo)}</td>
                        <td colspan="14"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    
    document.getElementById('detailContent').innerHTML = html;
    document.getElementById('totalCustomerCount').textContent = response.total_customer;
}

// ============================================
// FUNGSI EXPORT EXCEL
// ============================================
function exportDetailToExcel() {
    const table = document.getElementById('detailTable');
    if (!table) {
        alert('Tidak ada data untuk di-export');
        return;
    }
    
    if (typeof XLSX !== 'undefined') {
        const wb = XLSX.utils.table_to_book(table, { sheet: "Detail Customer" });
        const billingKe = document.getElementById('detailBillingKe').textContent;
        const datel = document.getElementById('detailDatel').textContent;
        XLSX.writeFile(wb, `hotd_billing_${billingKe}_${datel}.xlsx`);
    } else {
        const range = document.createRange();
        range.selectNode(table);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        alert('Data sudah di-copy ke clipboard. Silakan paste di Excel.');
    }
}
</script>
@endpush