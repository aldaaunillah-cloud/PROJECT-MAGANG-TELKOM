@extends('layouts.app')

@section('title', 'Data Customer')

@section('content')
<div class="container-fluid">
    {{-- ============================================================ --}}
    {{-- FILTER --}}
    {{-- ============================================================ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-funnel fs-4 text-primary me-2"></i>
                <h6 class="mb-0 fw-bold text-primary">Filter Data Customer</h6>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Nama / SND / Alamat" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status Bayar</label>
                    <select name="status_bayar" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="Sdh Bayar" {{ request('status_bayar') == 'Sdh Bayar' ? 'selected' : '' }}>Sudah Bayar</option>
                        <option value="Blm Bayar" {{ request('status_bayar') == 'Blm Bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Datel</label>
                    <select name="datel" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($filters['datel'] ?? [] as $datel)
                            <option value="{{ $datel }}" {{ request('datel') == $datel ? 'selected' : '' }}>
                                {{ $datel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Agency</label>
                    <select name="agency" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($filters['agency'] ?? [] as $agency)
                            <option value="{{ $agency }}" {{ request('agency') == $agency ? 'selected' : '' }}>
                                {{ $agency }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                    <a href="{{ route('customers.export.excel') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-file-excel"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-table fs-4 text-primary me-2"></i>
                <h6 class="mb-0 fw-bold text-primary">Data Customer</h6>
            </div>
            <span class="badge bg-light text-dark">{{ $customers->total() }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>SND</th>
                            <th>Nama</th>
                            <th>Datel</th>
                            <th>Agency</th>
                            <th>Sales</th>
                            <th>Billing</th>
                            <th>Status</th>
                            <th class="text-end">Tagihan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                            <tr>
                                <td>{{ $customers->firstItem() + $index }}</td>
                                <td><code>{{ $customer->snd }}</code></td>
                                <td>
                                    <strong>{{ $customer->nama }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($customer->alamat ?? '-', 30) }}</small>
                                </td>
                                <td>{{ $customer->datel ?? '-' }}</td>
                                <td>{{ $customer->agency_psb ?: ($customer->agency ?: '-') }}</td>
                                <td>{{ $customer->sales_agency ?: ($customer->sales ?: '-') }}</td>
                                <td>
                                    <span class="badge bg-info">B{{ $customer->billing_ke }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = $customer->status_bayar == 'Sdh Bayar' ? 'success' : 'danger';
                                        $statusText = $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="#" class="btn btn-outline-primary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($customer->ssl_file)
                                            <a href="{{ route('customers.download-ssl', $customer->id) }}" 
                                               class="btn btn-outline-success" title="Download SSL" target="_blank">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 d-block text-muted"></i>
                                    <p class="text-muted mt-2">Tidak ada data customer</p>
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
                        Menampilkan {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} 
                        dari {{ $customers->total() }} data
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