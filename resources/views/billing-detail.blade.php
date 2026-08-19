@extends('layouts.app')

@section('title', 'Billing ' . $billing_ke . ' - Detail Customer')

@section('content')
<style>
    .container-fluid { overflow-x: hidden !important; padding-left: 8px !important; padding-right: 8px !important; }
    .card-body { padding: 15px !important; }
    .table-responsive { overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; }
    .table-responsive table { min-width: 2500px !important; width: 100% !important; }
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
</style>

<div class="container-fluid">
    <div class="card border-0 shadow-sm" style="border: 1px solid #e2e8f0 !important; border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold text-primary-custom">
                <i class="bi bi-file-invoice me-2"></i> 
                Billing {{ $billing_ke }} - Detail Customer (Database Lengkap)
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
                <div class="col-md-8">
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
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4 w-50" style="border-radius: 8px;">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('billing.detail', $billing_ke) }}" class="btn btn-secondary btn-sm px-4 w-50" style="border-radius: 8px;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle table-bordered" style="border-color: #dee2e6;">
                    <thead class="table-light">
                        <tr class="text-uppercase" style="background-color: #f8f9fa;">
                            <th>#</th>
                            <th>STATUS BAYAR</th>
                            <th>TAG INET</th>
                            <th>TAG TLP</th>
                            <th>TAG TOTAL</th>
                            <th>SND</th>
                            <th>SND GROUP</th>
                            <th>NCLI</th>
                            <th>NAMA CUSTOMER</th>
                            <th>ALAMAT</th>
                            <th>STO</th>
                            <th>DATEL</th>
                            <th>PRODUK</th>
                            <th>EKSEPSI DESC</th>
                            <th>DESC NEWBILL</th>
                            <th>USAGE DESC</th>
                            <th>SALDO</th>
                            <th>UMUR CUSTOMER</th>
                            <th>BILLING KE</th>
                            <th>PAID L11</th>
                            <th>TGL PAID</th>
                            <th>PAID RP</th>
                            <th>COLL AGENT</th>
                            <th>TGL KLAIM</th>
                            <th>AMOUNT KLAIM</th>
                            <th>USER KLAIM</th>
                            <th>TGL PAID N-1</th>
                            <th>AGENCY PSB</th>
                            <th>SALES AGENCY</th>
                            <th>PPP</th>
                            <th>CARING MYBRAINS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                        <tr>
                            <td>{{ $customers->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-{{ $customer->status_bayar == 'Sdh Bayar' ? 'success' : 'danger' }} px-2 py-1" style="font-size: 0.65rem; border-radius: 6px;">
                                    {{ $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($customer->tag_inet ?? 0, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($customer->tag_tlp ?? 0, 0, ',', '.') }}</td>
                            <td class="fw-bold text-danger">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                            <td><code>{{ $customer->snd }}</code></td>
                            <td>{{ $customer->snd_group ?: '-' }}</td>
                            <td>{{ $customer->ncli ?: '-' }}</td>
                            <td class="fw-semibold text-primary-custom" style="min-width: 180px;">{{ $customer->nama }}</td>
                            <td style="min-width: 280px; white-space: normal !important;">{{ $customer->alamat }}</td>
                            <td>{{ $customer->sto ?: '-' }}</td>
                            <td>{{ $customer->datel ?: '-' }}</td>
                            <td>{{ $customer->produk ?: '-' }}</td>
                            <td>{{ $customer->eksepsi_desc ?: '-' }}</td>
                            <td>{{ $customer->desc_newbill ?: '-' }}</td>
                            <td>{{ $customer->usage_desc ?: '-' }}</td>
                            <td class="fw-bold text-secondary">Rp {{ number_format($customer->saldo ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $customer->umur_customer ? $customer->umur_customer . ' Hari' : '-' }}</td>
                            <td class="text-center">B{{ $customer->billing_ke }}</td>
                            <td>{{ $customer->paid_l11 ?: '-' }}</td>
                            <td>{{ $customer->tgl_paid ?: '-' }}</td>
                            <td>Rp {{ number_format($customer->paid_rp ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $customer->coll_agent ?: '-' }}</td>
                            <td>{{ $customer->tgl_klaim ?: '-' }}</td>
                            <td>Rp {{ number_format($customer->amount_klaim ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $customer->user_klaim ?: '-' }}</td>
                            <td>{{ $customer->tgl_paid_n1 ?: '-' }}</td>
                            <td>{{ $customer->agency_psb ?: ($customer->agency ?: '-') }}</td>
                            <td>{{ $customer->sales_agency ?: ($customer->sales ?: '-') }}</td>
                            <td>{{ $customer->ppp ?: '-' }}</td>
                            <td>{{ $customer->caring_mybrains ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="31" class="text-center py-5">
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