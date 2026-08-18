@extends('layouts.app')

@section('title', 'Billing ' . $billing_ke . ' - Detail Customer')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important; border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold text-primary-custom">
                <i class="bi bi-file-invoice me-2"></i> 
                Billing {{ $billing_ke }} - Detail Customer
            </h6>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('billing.detail.export-excel', array_merge(['billing_ke' => $billing_ke], request()->query())) }}" class="btn btn-sm btn-success text-white px-3" style="border-radius: 8px;">
                    <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Unduh Excel
                </a>
                <a href="{{ route('billing.detail.print-pdf', array_merge(['billing_ke' => $billing_ke], request()->query())) }}" target="_blank" class="btn btn-sm btn-danger text-white px-3" style="border-radius: 8px;">
                    <i class="bi bi-printer-fill me-1"></i> Cetak / PDF
                </a>
                <span class="badge bg-primary px-3 py-2" style="font-size: 0.8rem; border-radius: 8px;">{{ number_format($customers->total()) }} Customer</span>
            </div>
        </div>
        <div class="card-body p-4">
            <form method="GET" class="row g-3 mb-4 p-3 bg-light rounded-3" style="border: 1px solid #f1f5f9;">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">Agency</label>
                    <select name="agency" class="form-select form-select-sm">
                        <option value="">Semua Agency</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency }}" {{ request('agency') == $agency ? 'selected' : '' }}>
                                {{ $agency }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary" style="font-size: 0.85rem;">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4" style="border-radius: 8px;">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('billing.detail', $billing_ke) }}" class="btn btn-secondary btn-sm px-4" style="border-radius: 8px;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle table-bordered" style="border-color: #f1f5f9;">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-2 text-nowrap" style="font-size:0.75rem;">#</th>
                            <th class="px-3 py-2 text-nowrap" style="font-size:0.75rem;">SND</th>
                            <th class="px-3 py-2 text-nowrap" style="font-size:0.75rem;">NAMA CUSTOMER</th>
                            <th class="px-3 py-2 text-nowrap" style="font-size:0.75rem;">AGENCY</th>
                            <th class="px-3 py-2 text-nowrap text-center" style="font-size:0.75rem;">STATUS</th>
                            <th class="text-end px-3 py-2 text-nowrap" style="font-size:0.75rem;">TAGIHAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                        <tr>
                            <td class="px-3 py-2 text-nowrap" style="font-size: 0.8rem;">{{ $customers->firstItem() + $index }}</td>
                            <td class="px-3 py-2 text-nowrap"><code style="font-size: 0.75rem;">{{ $customer->snd }}</code></td>
                            <td class="px-3 py-2 text-nowrap fw-semibold" style="font-size: 0.8rem; color: #000361;">{{ $customer->nama }}</td>
                            <td class="px-3 py-2 text-nowrap" style="font-size: 0.8rem;">{{ $customer->agency_psb ?: ($customer->agency ?: '-') }}</td>
                            <td class="px-3 py-2 text-nowrap text-center">
                                <span class="badge bg-{{ $customer->status_bayar == 'Sdh Bayar' ? 'success' : 'danger' }} px-2 py-1" style="font-size: 0.65rem; border-radius: 6px;">
                                    {{ $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                                </span>
                            </td>
                            <td class="text-end px-3 py-2 text-nowrap text-danger fw-bold" style="font-size: 0.8rem;">
                                Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                <span class="text-muted" style="font-size: 0.85rem;">Tidak ada data customer di billing ini</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                <div>
                    <small class="text-muted" style="font-size: 0.8rem;">
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
@endsection