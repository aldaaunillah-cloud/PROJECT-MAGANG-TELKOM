@extends('layouts.app')

@section('title', 'Rekap Agency Billing 1-6')

@section('content')
<style>
    .container-fluid { overflow-x: hidden !important; padding-left: 8px !important; padding-right: 8px !important; }
    .row { margin-left: -4px !important; margin-right: -4px !important; }
    .row > * { padding-left: 4px !important; padding-right: 4px !important; }
    .card-body { padding: 15px !important; }
    .table-responsive { overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; }
    .table-responsive table { min-width: 1400px !important; width: 100% !important; }
    .table-responsive table th, .table-responsive table td { 
        white-space: nowrap !important; 
        padding: 5px 8px !important; 
        font-size: 0.72rem !important; 
        vertical-align: middle !important;
    }
    .badge { font-size: 0.65rem !important; padding: 4px 8px !important; }
    .form-label { font-size: 0.75rem !important; margin-bottom: 3px !important; }
    .form-control, .form-select { font-size: 0.8rem !important; padding: 5px 10px !important; }
    .btn-sm { font-size: 0.75rem !important; padding: 5px 12px !important; }
    .card-header { padding: 12px 15px !important; }
    .card-header h6 { font-size: 0.9rem !important; }
    .summary-card .card-body { padding: 8px 12px !important; }
    .summary-card h5 { font-size: 1rem !important; margin-bottom: 0 !important; }
    .summary-card small { font-size: 0.65rem !important; }
    .table-bordered th, .table-bordered td { border: 1px solid #dee2e6 !important; }
    .bg-agency { background-color: #f8f9fa; }
    .fw-600 { font-weight: 600; }
    @media (max-width: 768px) {
        .table-responsive table { min-width: 1200px !important; }
    }
    .pagination {
        margin-bottom: 0 !important;
        gap: 3px !important;
    }
    .pagination .page-item .page-link {
        padding: 4px 10px !important;
        font-size: 0.7rem !important;
        border-radius: 6px !important;
    }
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important; border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="mb-0 fw-bold text-primary-custom">
                <i class="bi bi-building me-2"></i> Rekap Agency Billing 1 Hingga 6
            </h6>
            <span class="badge bg-primary" style="font-size:0.7rem; border-radius: 6px;">
                <i class="bi bi-info-circle me-1"></i> Billing 1 - 6
            </span>
        </div>
        <div class="card-body">
            {{-- FILTER --}}
            <form method="GET" action="{{ route('rekap.agency') }}" class="row g-2 mb-3 p-3 bg-light rounded-3" style="border: 1px solid #f1f5f9;">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold text-secondary">Agency</label>
                    <select name="agency_psb" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Agency</option>
                        @foreach($filters['agency_psb'] as $agency)
                            <option value="{{ $agency }}" {{ request('agency_psb') == $agency ? 'selected' : '' }}>
                                {{ $agency }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold text-secondary">Sales Agency</label>
                    <select name="sales_agency" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Sales</option>
                        @foreach($filters['sales_agency'] as $sales)
                            <option value="{{ $sales }}" {{ request('sales_agency') == $sales ? 'selected' : '' }}>
                                {{ $sales }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label fw-bold text-secondary">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3 col-sm-6 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-50" style="border-radius: 8px;">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('rekap.agency') }}" class="btn btn-secondary btn-sm w-50" style="border-radius: 8px;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>

            {{-- SUMMARY --}}
            <div class="row g-2 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card bg-light border-0 summary-card rounded-3" style="border: 1px solid #e2e8f0 !important;">
                        <div class="card-body text-center">
                            <small class="text-muted">Total Customer (Belum Bayar)</small>
                            <h5 class="mb-0 fw-bold mt-1 text-dark">{{ number_format($summary['total_customer'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success bg-opacity-10 border-0 summary-card rounded-3" style="border: 1px solid #d1e7dd !important;">
                        <div class="card-body text-center">
                            <small class="text-success">Sudah Bayar</small>
                            <h5 class="mb-0 text-success fw-bold mt-1">{{ number_format($summary['total_sudah_bayar'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-danger bg-opacity-10 border-0 summary-card rounded-3" style="border: 1px solid #f8d7da !important;">
                        <div class="card-body text-center">
                            <small class="text-danger">Belum Bayar</small>
                            <h5 class="mb-0 text-danger fw-bold mt-1">{{ number_format($summary['total_belum_bayar'] ?? 0) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-info bg-opacity-10 border-0 summary-card rounded-3" style="border: 1px solid #cff4fc !important;">
                        <div class="card-body text-center">
                            <small class="text-info">Total Saldo</small>
                            <h5 class="mb-0 text-info fw-bold mt-1">Rp {{ number_format($summary['total_saldo'] ?? 0, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE LENGKAP SEPERTI SPREADSHEET --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" style="vertical-align:middle;min-width:45px;" class="text-center">#</th>
                            <th rowspan="2" style="vertical-align:middle;min-width:200px;">Agency PSB</th>
                            <th rowspan="2" style="vertical-align:middle;min-width:150px;">Sales Agency</th>
                            <th colspan="2" class="text-center" style="background-color:#E2001A;color:white;">Billing 1</th>
                            <th colspan="2" class="text-center" style="background-color:#2F3A4A;color:white;">Billing 2</th>
                            <th colspan="2" class="text-center" style="background-color:#28a745;color:white;">Billing 3</th>
                            <th colspan="2" class="text-center" style="background-color:#ffc107;color:black;">Billing 4</th>
                            <th colspan="2" class="text-center" style="background-color:#17a2b8;color:white;">Billing 5</th>
                            <th colspan="2" class="text-center" style="background-color:#dc3545;color:white;">Billing 6</th>
                            <th colspan="2" class="text-center" style="background-color:#6c757d;color:white;">Total</th>
                        </tr>
                        <tr>
                            <!-- Billing 1 -->
                            <th class="text-center" style="background-color:#E2001A;color:white;width:60px;">SSL</th>
                            <th class="text-end" style="background-color:#E2001A;color:white;width:100px;">Saldo</th>
                            <!-- Billing 2 -->
                            <th class="text-center" style="background-color:#2F3A4A;color:white;width:60px;">SSL</th>
                            <th class="text-end" style="background-color:#2F3A4A;color:white;width:100px;">Saldo</th>
                            <!-- Billing 3 -->
                            <th class="text-center" style="background-color:#28a745;color:white;width:60px;">SSL</th>
                            <th class="text-end" style="background-color:#28a745;color:white;width:100px;">Saldo</th>
                            <!-- Billing 4 -->
                            <th class="text-center" style="background-color:#ffc107;color:black;width:60px;">SSL</th>
                            <th class="text-end" style="background-color:#ffc107;color:black;width:100px;">Saldo</th>
                            <!-- Billing 5 -->
                            <th class="text-center" style="background-color:#17a2b8;color:white;width:60px;">SSL</th>
                            <th class="text-end" style="background-color:#17a2b8;color:white;width:100px;">Saldo</th>
                            <!-- Billing 6 -->
                            <th class="text-center" style="background-color:#dc3545;color:white;width:60px;">SSL</th>
                            <th class="text-end" style="background-color:#dc3545;color:white;width:100px;">Saldo</th>
                            <!-- Total -->
                            <th class="text-center" style="background-color:#6c757d;color:white;width:60px;">SSL</th>
                            <th class="text-end" style="background-color:#6c757d;color:white;width:100px;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal1Ssl = 0; $grandTotal1Saldo = 0;
                            $grandTotal2Ssl = 0; $grandTotal2Saldo = 0;
                            $grandTotal3Ssl = 0; $grandTotal3Saldo = 0;
                            $grandTotal4Ssl = 0; $grandTotal4Saldo = 0;
                            $grandTotal5Ssl = 0; $grandTotal5Saldo = 0;
                            $grandTotal6Ssl = 0; $grandTotal6Saldo = 0;
                            $grandTotalSsl = 0;  $grandTotalSaldo = 0;
                            $lastAgency = null;
                        @endphp

                        @forelse($rekap as $index => $item)
                            @php
                                $isSameAgency = ($lastAgency === $item->agency_psb);
                                
                                $grandTotal1Ssl += $item->billing_1_ssl ?? 0;
                                $grandTotal1Saldo += $item->billing_1_saldo ?? 0;
                                
                                $grandTotal2Ssl += $item->billing_2_ssl ?? 0;
                                $grandTotal2Saldo += $item->billing_2_saldo ?? 0;
                                
                                $grandTotal3Ssl += $item->billing_3_ssl ?? 0;
                                $grandTotal3Saldo += $item->billing_3_saldo ?? 0;
                                
                                $grandTotal4Ssl += $item->billing_4_ssl ?? 0;
                                $grandTotal4Saldo += $item->billing_4_saldo ?? 0;
                                
                                $grandTotal5Ssl += $item->billing_5_ssl ?? 0;
                                $grandTotal5Saldo += $item->billing_5_saldo ?? 0;
                                
                                $grandTotal6Ssl += $item->billing_6_ssl ?? 0;
                                $grandTotal6Saldo += $item->billing_6_saldo ?? 0;
                                
                                $grandTotalSsl += $item->total_ssl ?? 0;
                                $grandTotalSaldo += $item->total_saldo ?? 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $rekap->firstItem() + $index }}</td>
                                <td>
                                    @if(!$isSameAgency)
                                        <strong>{{ $item->agency_psb }}</strong>
                                    @endif
                                </td>
                                <td>{{ $item->sales_agency ?? '-' }}</td>
                                
                                <!-- Billing 1 -->
                                <td class="text-center">
                                    @if(($item->billing_1_ssl ?? 0) > 0)
                                        <a href="{{ route('billing.detail', ['billing_ke' => 1, 'agency' => $item->agency_psb, 'sales' => $item->sales_agency, 'status' => 'Blm Bayar']) }}" class="text-decoration-underline fw-bold text-primary">
                                            {{ number_format($item->billing_1_ssl) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->billing_1_saldo ?? 0, 0, ',', '.') }}</td>
                                
                                <!-- Billing 2 -->
                                <td class="text-center">
                                    @if(($item->billing_2_ssl ?? 0) > 0)
                                        <a href="{{ route('billing.detail', ['billing_ke' => 2, 'agency' => $item->agency_psb, 'sales' => $item->sales_agency, 'status' => 'Blm Bayar']) }}" class="text-decoration-underline fw-bold text-primary">
                                            {{ number_format($item->billing_2_ssl) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->billing_2_saldo ?? 0, 0, ',', '.') }}</td>

                                <!-- Billing 3 -->
                                <td class="text-center">
                                    @if(($item->billing_3_ssl ?? 0) > 0)
                                        <a href="{{ route('billing.detail', ['billing_ke' => 3, 'agency' => $item->agency_psb, 'sales' => $item->sales_agency, 'status' => 'Blm Bayar']) }}" class="text-decoration-underline fw-bold text-primary">
                                            {{ number_format($item->billing_3_ssl) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->billing_3_saldo ?? 0, 0, ',', '.') }}</td>

                                <!-- Billing 4 -->
                                <td class="text-center">
                                    @if(($item->billing_4_ssl ?? 0) > 0)
                                        <a href="{{ route('billing.detail', ['billing_ke' => 4, 'agency' => $item->agency_psb, 'sales' => $item->sales_agency, 'status' => 'Blm Bayar']) }}" class="text-decoration-underline fw-bold text-primary">
                                            {{ number_format($item->billing_4_ssl) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->billing_4_saldo ?? 0, 0, ',', '.') }}</td>

                                <!-- Billing 5 -->
                                <td class="text-center">
                                    @if(($item->billing_5_ssl ?? 0) > 0)
                                        <a href="{{ route('billing.detail', ['billing_ke' => 5, 'agency' => $item->agency_psb, 'sales' => $item->sales_agency, 'status' => 'Blm Bayar']) }}" class="text-decoration-underline fw-bold text-primary">
                                            {{ number_format($item->billing_5_ssl) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->billing_5_saldo ?? 0, 0, ',', '.') }}</td>

                                <!-- Billing 6 -->
                                <td class="text-center">
                                    @if(($item->billing_6_ssl ?? 0) > 0)
                                        <a href="{{ route('billing.detail', ['billing_ke' => 6, 'agency' => $item->agency_psb, 'sales' => $item->sales_agency, 'status' => 'Blm Bayar']) }}" class="text-decoration-underline fw-bold text-primary">
                                            {{ number_format($item->billing_6_ssl) }}
                                        </a>
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="text-end">Rp {{ number_format($item->billing_6_saldo ?? 0, 0, ',', '.') }}</td>
                                
                                <!-- Total -->
                                <td class="text-center fw-bold">{{ number_format($item->total_ssl ?? 0) }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->total_saldo ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $lastAgency = $item->agency_psb;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="17" class="text-center py-5">
                                    <i class="bi bi-inbox fs-3 text-muted d-block mb-1"></i>
                                    <span class="text-muted" style="font-size: 0.85rem;">Tidak ada data agency</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-secondary fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">GRAND TOTAL</td>
                            
                            <!-- B1 -->
                            <td class="text-center">{{ number_format($grandTotal1Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotal1Saldo, 0, ',', '.') }}</td>
                            
                            <!-- B2 -->
                            <td class="text-center">{{ number_format($grandTotal2Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotal2Saldo, 0, ',', '.') }}</td>

                            <!-- B3 -->
                            <td class="text-center">{{ number_format($grandTotal3Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotal3Saldo, 0, ',', '.') }}</td>

                            <!-- B4 -->
                            <td class="text-center">{{ number_format($grandTotal4Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotal4Saldo, 0, ',', '.') }}</td>

                            <!-- B5 -->
                            <td class="text-center">{{ number_format($grandTotal5Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotal5Saldo, 0, ',', '.') }}</td>

                            <!-- B6 -->
                            <td class="text-center">{{ number_format($grandTotal6Ssl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotal6Saldo, 0, ',', '.') }}</td>
                            
                            <!-- Total -->
                            <td class="text-center">{{ number_format($grandTotalSsl) }}</td>
                            <td class="text-end">Rp {{ number_format($grandTotalSaldo, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div>
                    <small class="text-muted" style="font-size:0.75rem;">
                        Menampilkan {{ $rekap->firstItem() ?? 0 }} - {{ $rekap->lastItem() ?? 0 }} 
                        dari {{ number_format($rekap->total()) }} data
                    </small>
                </div>
                <div>
                    {{ $rekap->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection