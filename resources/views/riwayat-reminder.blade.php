@extends('layouts.app')

@section('title', 'Riwayat Reminder')

@section('content')
<style>
    .container-fluid { overflow-x: hidden !important; padding-left: 8px !important; padding-right: 8px !important; }
    .row { margin-left: -4px !important; margin-right: -4px !important; }
    .row > * { padding-left: 4px !important; padding-right: 4px !important; }
    .card-body { padding: 12px !important; }
    .table-responsive { overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; }
    .table-responsive table { min-width: 900px !important; width: 100% !important; }
    .table-responsive table th, .table-responsive table td { 
        white-space: nowrap !important; 
        padding: 4px 6px !important; 
        font-size: 0.7rem !important; 
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
    code { font-size: 0.65rem !important; }
    @media (max-width: 768px) {
        .table-responsive table { min-width: 750px !important; }
        .table-responsive table th, .table-responsive table td { font-size: 0.55rem !important; padding: 3px 4px !important; }
    }
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center flex-wrap">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-clock-history me-2"></i> Riwayat Reminder
            </h6>
            <div class="d-flex gap-1 flex-wrap">
                <button class="btn btn-success btn-sm" onclick="exportExcel()">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </button>
                <button class="btn btn-danger btn-sm" onclick="exportPDF()">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </button>
                <button class="btn btn-secondary btn-sm" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reminders.index') }}" class="row g-2 mb-3" id="filterForm">
                <div class="col-md-3 col-sm-6">
                    <label class="form-label"><i class="bi bi-search me-1"></i> Cari</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Nama, SND, atau SA..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label"><i class="bi bi-filter me-1"></i> Status</label>
                    <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua</option>
                        <option value="klaim" {{ request('status') == 'klaim' ? 'selected' : '' }}>Klaim</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Sudah Kirim</option>
                        <option value="paid_n1" {{ request('status') == 'paid_n1' ? 'selected' : '' }}>Paid N-1</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label"><i class="bi bi-building me-1"></i> Agency</label>
                    <select name="agency" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua</option>
                        @foreach($filters['agency'] ?? [] as $agency)
                            <option value="{{ $agency }}" {{ request('agency') == $agency ? 'selected' : '' }}>
                                {{ $agency }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label"><i class="bi bi-person me-1"></i> SA</label>
                    <select name="sales" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua</option>
                        @foreach($filters['sales'] ?? [] as $sales)
                            <option value="{{ $sales }}" {{ request('sales') == $sales ? 'selected' : '' }}>
                                {{ $sales }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-50">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('reminders.index') }}" class="btn btn-secondary btn-sm w-50">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>

            <div class="row g-2 mb-3">
                <div class="col-md-3 col-6">
                    <div class="card bg-light border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-muted">Total Data</small>
                            <h5 class="mb-0">{{ number_format($customers->total()) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-success bg-opacity-10 border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-success">Terkirim</small>
                            <h5 class="mb-0 text-success">{{ number_format($customers->where('tgl_paid', '!=', null)->count()) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-warning bg-opacity-10 border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-warning">Proses</small>
                            <h5 class="mb-0 text-warning">{{ number_format($customers->where('tgl_klaim', '!=', null)->whereNull('tgl_paid')->count()) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card bg-danger bg-opacity-10 border-0 summary-card">
                        <div class="card-body text-center">
                            <small class="text-danger">Belum</small>
                            <h5 class="mb-0 text-danger">{{ number_format($customers->whereNull('tgl_klaim')->whereNull('tgl_paid')->count()) }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>SND</th>
                            <th>Nama</th>
                            <th>SA</th>
                            <th>Agency</th>
                            <th>Datel</th>
                            <th>Billing</th>
                            <th>Status</th>
                            <th class="text-end">Tagihan</th>
                            <th>Tgl Reminder</th>
                            <th>Status Reminder</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                        <tr>
                            <td>{{ $customers->firstItem() + $index }}</td>
                            <td><code class="bg-light p-1 rounded">{{ $customer->snd ?? '-' }}</code></td>
                            <td>{{ Str::limit($customer->nama ?? '-', 20) }}</td>
                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ $customer->sales ?? '-' }}</span></td>
                            <td>{{ $customer->agency ?? '-' }}</td>
                            <td>{{ Str::limit($customer->datel ?? '-', 10) }}</td>
                            <td>
                                @if($customer->billing_ke)
                                    <span class="badge bg-{{ $customer->billing_ke <= 2 ? 'primary' : 'secondary' }}">
                                        B{{ $customer->billing_ke }}
                                    </span>
                                @else - @endif
                            </td>
                            <td>
                                @if($customer->status_bayar == 'Sdh Bayar')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif($customer->status_bayar == 'Blm Bayar')
                                    <span class="badge bg-danger">Belum</span>
                                @else
                                    <span class="badge bg-secondary">{{ $customer->status_bayar ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-end" style="font-size:0.65rem;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                            <td style="font-size:0.6rem;">
                                @if($customer->tgl_paid)
                                    {{ \Carbon\Carbon::parse($customer->tgl_paid)->format('d/m/Y') }}
                                @elseif($customer->tgl_klaim)
                                    {{ \Carbon\Carbon::parse($customer->tgl_klaim)->format('d/m/Y') }}
                                @else - @endif
                            </td>
                            <td>
                                @if($customer->tgl_paid)
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Terkirim</span>
                                @elseif($customer->tgl_klaim)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill me-1"></i> Proses</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Belum</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-3">
                                <i class="bi bi-inbox fs-3 text-muted d-block mb-1"></i>
                                <span class="text-muted" style="font-size:0.8rem;">Tidak ada data reminder</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
                <div>
                    <small class="text-muted" style="font-size:0.65rem;">
                        Menampilkan {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} 
                        dari {{ number_format($customers->total()) }} data
                    </small>
                </div>
                <div>
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function exportExcel() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("reminders.export-excel") }}?' + params.toString();
    }
    function exportPDF() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("reminders.export-pdf") }}?' + params.toString();
    }
</script>
@endpush
@endsection