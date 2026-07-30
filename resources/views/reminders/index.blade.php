@extends('layouts.app')

@section('title', 'Riwayat Reminder - Belum Bayar')

@section('content')
<div class="container-fluid">
    
    {{-- ============================================ --}}
    {{-- FILTER --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-funnel fs-4 text-primary me-2"></i>
                <h6 class="mb-0 fw-bold text-primary">Filter Reminder</h6>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Nama / SND / Agency" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Agency</label>
                    <select name="agency" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($agencies ?? [] as $agency)
                            <option value="{{ $agency }}" {{ request('agency') == $agency ? 'selected' : '' }}>
                                {{ $agency }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sales</label>
                    <select name="sales" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($sales ?? [] as $sale)
                            <option value="{{ $sale }}" {{ request('sales') == $sale ? 'selected' : '' }}>
                                {{ $sale }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Billing Ke</label>
                    <select name="billing_ke" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($billingList ?? [] as $bill)
                            <option value="{{ $bill }}" {{ request('billing_ke') == $bill ? 'selected' : '' }}>
                                Billing {{ $bill }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('reminders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABLE --}}
    {{-- ============================================ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-clock-history fs-4 text-danger me-2"></i>
                <h6 class="mb-0 fw-bold text-danger">Customer Belum Bayar</h6>
            </div>
            <span class="badge bg-danger">{{ $reminders->total() }} Customer</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>SND</th>
                            <th>Nama Customer</th>
                            <th>Agency</th>
                            <th>Sales</th>
                            <th>Billing</th>
                            <th class="text-end">Tagihan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reminders as $index => $customer)
                            <tr>
                                <td>{{ $reminders->firstItem() + $index }}</td>
                                <td><code>{{ $customer->snd }}</code></td>
                                <td>
                                    <strong>{{ $customer->nama }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $customer->datel ?? '-' }}</small>
                                </td>
                                <td>{{ $customer->agency ?? '-' }}</td>
                                <td>{{ $customer->sales ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">B{{ $customer->billing_ke }}</span>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge bg-danger">Belum Bayar</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-check-circle fs-1 d-block text-success"></i>
                                    <p class="text-muted mt-2">Semua customer sudah bayar! 🎉</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Menampilkan {{ $reminders->firstItem() ?? 0 }} - {{ $reminders->lastItem() ?? 0 }} 
                        dari {{ $reminders->total() }} customer belum bayar
                    </small>
                </div>
                <div>
                    {{ $reminders->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection