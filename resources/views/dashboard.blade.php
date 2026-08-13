@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-2 overflow-hidden" style="padding-top: 20px;">

    {{-- FILTER FORM --}}
    <div class="card border-0 shadow-sm mb-4 w-100 overflow-hidden">
        <div class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary d-flex align-items-center" style="font-size:0.9rem;">
                <div class="bg-primary me-2" style="width: 8px; height: 16px; border-radius: 2px;"></div>
                Filter Dashboard
            </h6>
            <span class="badge bg-primary text-white px-2 py-1 rounded-pill" style="font-size:0.7rem;">
                <i class="bi bi-info-circle me-1"></i> Data Filter
            </span>
        </div>
        <div class="card-body p-3 pt-0">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Datel (Telda)</label>
                    <select name="datel" id="filter-datel" class="form-select form-select-sm border-light-subtle text-muted" style="font-size: 0.8rem;">
                        <option value="">Semua Datel</option>
                        @foreach($datelsList ?? [] as $d)
                            <option value="{{ $d }}" {{ request('datel') == $d ? 'selected' : '' }}>{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Agency</label>
                    <select name="agency" id="filter-agency" class="form-select form-select-sm border-light-subtle text-muted" style="font-size: 0.8rem;">
                        <option value="">Semua Agency</option>
                        @foreach($agenciesList ?? [] as $a)
                            <option value="{{ $a }}" {{ request('agency') == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted mb-1" style="font-size: 0.75rem; font-weight: 600;">Sales Agency</label>
                    <select name="sales" id="filter-sales" class="form-select form-select-sm border-light-subtle text-muted" style="font-size: 0.8rem;">
                        <option value="">Semua Sales</option>
                        @foreach($salesList ?? [] as $s)
                            <option value="{{ $s }}" {{ request('sales') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white flex-grow-1 shadow-sm" style="background-color: navy;">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary flex-grow-1 shadow-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- STATISTICS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100 w-100 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-people fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Customer Belum Bayar</h6>
                            <h3 class="mb-0 fs-4">{{ number_format($totalBelumLunas ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100 w-100 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-cash-stack fs-3 text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Tagihan Belum Bayar</h6>
                            <h3 class="mb-0 fs-4">Rp {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100 w-100 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-building fs-3 text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Agency</h6>
                            <h3 class="mb-0 fs-4">{{ number_format($totalAgency ?? 0) }}</h3>
                            <small class="text-muted" style="font-size:0.7rem;">{{ number_format($totalSales ?? 0) }} Sales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-0 shadow-sm h-100 w-100 overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-clock-history fs-3 text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="text-muted mb-0" style="font-size:0.75rem;">Total Saldo</h6>
                            <h3 class="mb-0 fs-4">Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</h3>
                            <small class="text-muted" style="font-size:0.7rem;">Sisa tagihan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BILLING SUMMARY 1-6 --}}
    <div class="card border-0 shadow-sm mb-4 w-100 overflow-hidden">
        <div class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                <i class="bi bi-file-invoice-dollar me-2"></i> Rekap Billing Customer 1-6 (HOTD)
            </h6>
            <span class="badge bg-info text-white px-2 py-1" style="font-size:0.7rem;">
                <i class="bi bi-info-circle me-1"></i> Billing 1-6
            </span>
        </div>
        <div class="card-body p-3">
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
                                        <h4 class="mb-0 mt-1" style="font-size:1.3rem;">{{ number_format($billing->belum_lunas ?? 0) }}</h4>
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

    {{-- ============================================ --}}
    {{-- HOTD - DETAIL BILLING PER DATEL --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm mb-4 w-100 overflow-hidden">
        <div class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                <i class="bi bi-table me-2"></i> Detail Billing 1-6 per Datel (HOTD)
                <small class="text-muted ms-2" style="font-size:0.7rem;">Klik baris untuk lihat detail</small>
            </h6>
            <span class="badge bg-success px-2 py-1" style="font-size:0.7rem;">
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
                    <table class="table table-bordered table-hover table-sm mb-0 w-100" style="min-width: 600px;">
                        <thead>
                            <tr>
                                <th rowspan="3" class="text-center align-middle text-nowrap px-2 py-1" style="min-width:100px; font-size: 0.65rem;">DATEL</th>
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
                                    <th class="text-center text-nowrap px-2 py-1" style="min-width:35px; font-size: 0.65rem;">SL</th>
                                    <th class="text-center text-nowrap px-2 py-1" style="min-width:70px; font-size: 0.65rem;">Rp</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
    @foreach($datels as $datel)
        @php
            $billingKeForDatel = $datelBillingMap[$datel] ?? 1;
        @endphp
        <tr class="table-light">
            <td class="text-center text-nowrap px-2 py-1" style="font-size: 0.65rem;"><strong>{{ $datel }}</strong></td>
            @foreach(range(1, 6) as $billingKe)
                @php
                    $data = $grouped->get($billingKe, collect())->firstWhere('datel', $datel);
                    $blm = $data->blm_bayar ?? 0;
                    $blmRp = $data->blm_bayar_rp ?? 0;
                @endphp
                <td class="text-center text-primary text-decoration-underline text-nowrap px-2 py-1" 
                    style="font-size: 0.65rem;cursor:pointer;"
                    onclick="showDetail('{{ $datel }}', {{ $billingKe }})">
                    {{ $blm }}
                </td>
                <td class="text-center text-primary text-decoration-underline text-nowrap px-2 py-1" 
                    style="font-size:0.6rem;cursor:pointer;"
                    onclick="showDetail('{{ $datel }}', {{ $billingKe }})">
                    Rp {{ number_format($blmRp, 0, ',', '.') }}
                </td>
            @endforeach
            @php
                $totalBlm = 0; $totalBlmRp = 0;
                foreach(range(1, 6) as $billingKe) {
                    $data = $grouped->get($billingKe, collect())->firstWhere('datel', $datel);
                    if ($data) {
                        $totalBlm += $data->blm_bayar ?? 0;
                        $totalBlmRp += $data->blm_bayar_rp ?? 0;
                    }
                }
            @endphp
            <td class="text-center fw-bold text-nowrap px-2 py-1" style="font-size: 0.65rem;">{{ $totalBlm }}</td>
            <td class="text-center fw-bold text-nowrap px-2 py-1" style="font-size:0.6rem;">Rp {{ number_format($totalBlmRp, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</tbody>
                        <tfoot class="table-secondary fw-bold">
                            <tr>
                                <td class="text-center text-nowrap px-2 py-1" style="font-size: 0.65rem;">GRAND TOTAL</td>
                                @foreach(range(1, 6) as $billingKe)
                                    @php
                                        $total = $billingTotals[$billingKe] ?? ['blm_bayar' => 0, 'blm_bayar_rp' => 0];
                                    @endphp
                                    <td class="text-center text-nowrap px-2 py-1" style="font-size: 0.65rem;">{{ $total['blm_bayar'] }}</td>
                                    <td class="text-center text-nowrap px-2 py-1" style="font-size:0.6rem;">Rp {{ number_format($total['blm_bayar_rp'] ?? 0, 0, ',', '.') }}</td>
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

    {{-- STATUS CHART REMOVED --}}

    {{-- RECENT CUSTOMERS --}}
    <div class="card border-0 shadow-sm w-100 overflow-hidden mb-5">
        <div class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary" style="font-size:0.9rem;">
                <i class="bi bi-people me-2"></i> 10 Customer Terbaru
            </h6>
            <span class="badge bg-primary px-2 py-1" style="font-size:0.7rem;">{{ count($latestCustomers ?? []) }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive w-100">
                <table class="table table-hover table-striped mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="px-2 py-1 text-nowrap" style="font-size:0.65rem;">#</th>
                            <th class="px-2 py-1 text-nowrap" style="font-size:0.65rem;">SND</th>
                            <th class="px-2 py-1 text-nowrap" style="font-size:0.65rem;">Nama</th>
                            <th class="px-2 py-1 text-nowrap" style="font-size:0.65rem;">Agency</th>
                            <th class="px-2 py-1 text-nowrap" style="font-size:0.65rem;">Billing</th>
                            <th class="px-2 py-1 text-nowrap" style="font-size:0.65rem;">Status</th>
                            <th class="text-end px-2 py-1 text-nowrap" style="font-size:0.65rem;">Tagihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($latestCustomers ?? []) as $index => $customer)
                            <tr>
                                <td class="px-2 py-1 text-nowrap" style="font-size:0.8rem;">{{ $index + 1 }}</td>
                                <td class="px-2 py-1 text-nowrap"><code style="font-size:0.7rem;">{{ $customer->snd }}</code></td>
                                <td class="px-2 py-1 text-nowrap" style="font-size:0.8rem;">{{ Str::limit($customer->nama, 25) }}</td>
                                <td class="px-2 py-1 text-nowrap" style="font-size:0.8rem;">{{ $customer->agency_psb ?: ($customer->agency ?: '-') }}</td>
                                <td class="px-2 py-1 text-nowrap">
                                    <span class="badge bg-{{ ($customer->billing_ke ?? 0) <= 2 ? 'primary' : 'secondary' }} px-2 py-1" style="font-size:0.6rem;">
                                        B{{ $customer->billing_ke }}
                                    </span>
                                </td>
                                <td class="px-2 py-1 text-nowrap">
                                    <span class="badge bg-{{ $customer->status_bayar == 'Sdh Bayar' ? 'success' : 'danger' }} px-2 py-1" style="font-size:0.6rem;">
                                        {{ $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                                    </span>
                                </td>
                                <td class="text-end px-2 py-1 text-nowrap" style="font-size:0.8rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 px-2 text-nowrap">
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
                <button type="button" class="btn btn-success rounded" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
// AJAX Cascading Filters
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const datelSelect = document.getElementById('filter-datel');
    const agencySelect = document.getElementById('filter-agency');
    const salesSelect = document.getElementById('filter-sales');

    if (datelSelect && agencySelect && salesSelect) {
        // Event listener saat Datel berubah
        datelSelect.addEventListener('change', function() {
            const datel = this.value;
            
            // 1. Reset dropdown Agency dan Sales Agency
            agencySelect.innerHTML = '<option value="">Semua Agency</option>';
            salesSelect.innerHTML = '<option value="">Semua Sales</option>';
            
            // 2. Fetch Agency berdasarkan Datel baru
            if (datel) {
                agencySelect.disabled = true;
                agencySelect.innerHTML = '<option value="">Memuat Agency...</option>';
            }
            fetch(`{{ route('filter.agencies') }}?datel=${encodeURIComponent(datel)}`)
                .then(res => res.json())
                .then(data => {
                    agencySelect.disabled = false;
                    agencySelect.innerHTML = '<option value="">Semua Agency</option>';
                    data.forEach(agency => {
                        agencySelect.innerHTML += `<option value="${agency}">${agency}</option>`;
                    });
                })
                .catch(err => {
                    agencySelect.disabled = false;
                    agencySelect.innerHTML = '<option value="">Semua Agency</option>';
                    console.error('Error fetching agencies:', err);
                });
                
            // 3. Fetch Sales Agency berdasarkan Datel baru
            if (datel) {
                salesSelect.disabled = true;
                salesSelect.innerHTML = '<option value="">Memuat Sales...</option>';
            }
            fetch(`{{ route('filter.sales') }}?datel=${encodeURIComponent(datel)}`)
                .then(res => res.json())
                .then(data => {
                    salesSelect.disabled = false;
                    salesSelect.innerHTML = '<option value="">Semua Sales</option>';
                    data.forEach(sales => {
                        salesSelect.innerHTML += `<option value="${sales}">${sales}</option>`;
                    });
                })
                .catch(err => {
                    salesSelect.disabled = false;
                    salesSelect.innerHTML = '<option value="">Semua Sales</option>';
                    console.error('Error fetching sales:', err);
                });
        });

        // Event listener saat Agency berubah
        agencySelect.addEventListener('change', function() {
            const datel = datelSelect.value;
            const agency = this.value;
            
            // Reset Sales Agency
            salesSelect.innerHTML = '<option value="">Semua Sales</option>';
            if (agency || datel) {
                salesSelect.disabled = true;
                salesSelect.innerHTML = '<option value="">Memuat Sales...</option>';
            }
            
            fetch(`{{ route('filter.sales') }}?datel=${encodeURIComponent(datel)}&agency=${encodeURIComponent(agency)}`)
                .then(res => res.json())
                .then(data => {
                    salesSelect.disabled = false;
                    salesSelect.innerHTML = '<option value="">Semua Sales</option>';
                    data.forEach(sales => {
                        salesSelect.innerHTML += `<option value="${sales}">${sales}</option>`;
                    });
                })
                .catch(err => {
                    salesSelect.disabled = false;
                    salesSelect.innerHTML = '<option value="">Semua Sales</option>';
                    console.error('Error fetching sales:', err);
                });
        });
    }
});
</script>
@endpush