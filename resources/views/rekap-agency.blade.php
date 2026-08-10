@extends('layouts.app')

@section('title', 'Rekap Agency Billing 1-2')

@section('content')
<style>
    .container-fluid { overflow-x: hidden !important; padding-left: 8px !important; padding-right: 8px !important; }
    .row { margin-left: -4px !important; margin-right: -4px !important; }
    .row > * { padding-left: 4px !important; padding-right: 4px !important; }
    .card-body { padding: 12px !important; }
    .table-responsive { overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; }
    .table-responsive table { min-width: 1000px !important; width: 100% !important; }
    .table-responsive table th, .table-responsive table td { 
        white-space: nowrap !important; 
        padding: 4px 6px !important; 
        font-size: 0.7rem !important; 
        vertical-align: middle !important;
    }
    .badge { font-size: 0.6rem !important; padding: 3px 8px !important; }
    .form-label { font-size: 0.7rem !important; margin-bottom: 2px !important; }
    .form-control, .form-select { font-size: 0.75rem !important; padding: 4px 8px !important; }
    .btn-sm { font-size: 0.7rem !important; padding: 4px 10px !important; }
    .card-header { padding: 8px 12px !important; }
    .card-header h6 { font-size: 0.8rem !important; }
    .summary-card .card-body { padding: 6px 8px !important; }
    .summary-card h5 { font-size: 0.9rem !important; margin-bottom: 0 !important; }
    .summary-card small { font-size: 0.6rem !important; }
    .table-bordered th, .table-bordered td { border: 1px solid #dee2e6 !important; }
    .bg-agency { background-color: #f8f9fa; }
    .fw-600 { font-weight: 600; }
    @media (max-width: 768px) {
        .table-responsive table { min-width: 900px !important; }
        .table-responsive table th, .table-responsive table td { font-size: 0.6rem !important; padding: 3px 4px !important; }
    }
    .pagination {
        margin-bottom: 0 !important;
        gap: 2px !important;
    }
    .pagination .page-item .page-link {
        padding: 3px 8px !important;
        font-size: 0.65rem !important;
        border-radius: 4px !important;
    }
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-building me-2"></i> Rekap Agency Billing 1 & 2
            </h6>
            <span class="badge bg-primary" style="font-size:0.65rem;">
                <i class="bi bi-info-circle me-1"></i> Billing 1 & 2
            </span>
        </div>
        <div class="card-body">
            {{-- FILTER --}}
            <form method="GET" action="{{ route('rekap.agency') }}" class="row g-2 mb-3">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold">Agency</label>

                    <select name="agency_psb"
                            class="form-select"
                            onchange="this.form.submit()">

                        <option value="">Semua Agency</option>

                        @foreach($filters['agency_psb'] as $agency)
                            <option value="{{ $agency }}"
                                {{ request('agency_psb') == $agency ? 'selected' : '' }}>
                                {{ $agency }}
                            </option>
                        @endforeach

                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold">Sales Agency</label>

                    <select name="sales_agency"
                            class="form-select"
                            onchange="this.form.submit()">

                        <option value="">Semua Sales</option>

                        @foreach($filters['sales_agency'] as $sales)
                            <option value="{{ $sales }}"
                                {{ request('sales_agency') == $sales ? 'selected' : '' }}>
                                {{ $sales }}
                            </option>
                        @endforeach

                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 col-sm-6 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-50">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('rekap.agency') }}" class="btn btn-secondary btn-sm w-50">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>

            {{-- SUMMARY --}}
            <div class="row g-2 mb-3">
                <div class="col-md-3 col-6">
                    <div class="card bg-light border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-muted">Total Customer</small>
                            <h5 class="mb-0">{{ number_format($summary['total_customer'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success bg-opacity-10 border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-success">Sudah Bayar</small>
                            <h5 class="mb-0 text-success">{{ number_format($summary['total_sudah_bayar'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-danger bg-opacity-10 border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-danger">Belum Bayar</small>
                            <h5 class="mb-0 text-danger">{{ number_format($summary['total_belum_bayar'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-info bg-opacity-10 border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-info">Total Saldo</small>
                            <h5 class="mb-0 text-info" style="font-size:0.75rem;">Rp {{ number_format($summary['total_saldo'] ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE LENGKAP SEPERTI SPREADSHEET --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" style="vertical-align:middle;min-width:40px;">#</th>
                            <th rowspan="2" style="vertical-align:middle;min-width:200px;">Agency PSB</th>
                            <th rowspan="2" style="vertical-align:middle;min-width:150px;">Sales Agency</th>
                            <th colspan="2" class="text-center" style="background-color:#E2001A;color:white;">Billing 1</th>
                            <th colspan="2" class="text-center" style="background-color:#2F3A4A;color:white;">Billing 2</th>
                            <th colspan="2" class="text-center" style="background-color:#28a745;color:white;">Total</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="background-color:#E2001A;color:white;">SSL</th>
                            <th class="text-end" style="background-color:#E2001A;color:white;">Saldo</th>
                            <th class="text-center" style="background-color:#2F3A4A;color:white;">SSL</th>
                            <th class="text-end" style="background-color:#2F3A4A;color:white;">Saldo</th>
                            <th class="text-center" style="background-color:#28a745;color:white;">SSL</th>
                            <th class="text-end" style="background-color:#28a745;color:white;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotalBilling1Ssl = 0;
                            $grandTotalBilling1Saldo = 0;
                            $grandTotalBilling2Ssl = 0;
                            $grandTotalBilling2Saldo = 0;
                            $grandTotalSsl = 0;
                            $grandTotalSaldo = 0;
                            $lastAgency = null;
                        @endphp

                        @forelse($rekap as $index => $item)
                            @php
                                $isSameAgency = ($lastAgency === $item->agency_psb);
                                $grandTotalBilling1Ssl += $item->billing_1_ssl ?? 0;
                                $grandTotalBilling1Saldo += $item->billing_1_saldo ?? 0;
                                $grandTotalBilling2Ssl += $item->billing_2_ssl ?? 0;
                                $grandTotalBilling2Saldo += $item->billing_2_saldo ?? 0;
                                $grandTotalSsl += $item->total_ssl ?? 0;
                                $grandTotalSaldo += $item->total_saldo ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $rekap->firstItem() + $index }}</td>
                                <td>
                                    @if(!$isSameAgency)
                                        <strong>{{ $item->agency_psb }}</strong>
                                    @endif
                                </td>
                                <td>{{ $item->sales_agency ?? '-' }}</td>
                                <td class="text-center">{{ number_format($item->billing_1_ssl ?? 0) }}</td>
                                <td class="text-end">Rp {{ number_format($item->billing_1_saldo ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">{{ number_format($item->billing_2_ssl ?? 0) }}</td>
                                <td class="text-end">Rp {{ number_format($item->billing_2_saldo ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center fw-bold">{{ number_format($item->total_ssl ?? 0) }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->total_saldo ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $lastAgency = $item->agency_psb;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-3">
                                    <i class="bi bi-inbox fs-3 text-muted d-block mb-1"></i>
                                    <span class="text-muted">Tidak ada data agency</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">GRAND TOTAL</td>
                            <td class="text-center">{{ number_format($grandTotalBilling1Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotalBilling1Saldo, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($grandTotalBilling2Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotalBilling2Saldo, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($grandTotalSsl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotalSaldo, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                <div>
                    <small class="text-muted" style="font-size:0.65rem;">
                        Menampilkan {{ $rekap->firstItem() ?? 0 }} - {{ $rekap->lastItem() ?? 0 }} 
                        dari {{ number_format($rekap->total()) }} data
                    </small>
                </div>
                <div>
                    {{ $rekap->appends(request()->query())->links('partials.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection