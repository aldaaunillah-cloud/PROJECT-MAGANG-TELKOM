@extends('layouts.app')

@section('title', 'Saldo')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Saldo Customer</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('customers.saldo') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-10 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama, NCLI, atau Saldo..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>NCLI</th>
                            <th>Nama</th>
                            <th>Datel</th>
                            <th>Saldo</th>
                            <th>Tagihan</th>
                            <th>Billing Ke</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers ?? [] as $index => $customer)
                        <tr>
                            <td>{{ $customers->firstItem() + $index }}</td>
                            <td>
                                <span class="badge bg-{{ $customer->status_bayar == 'Blm Bayar' ? 'danger' : 'success' }}">
                                    {{ $customer->status_bayar }}
                                </span>
                            </td>
                            <td>{{ $customer->ncli }}</td>
                            <td>{{ Str::limit($customer->nama, 35) }}</td>
                            <td>{{ $customer->datel }}</td>
                            <td class="fw-bold">Rp {{ number_format($customer->saldo, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($customer->tag_total, 0, ',', '.') }}</td>
                            <td>{{ $customer->billing_ke }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data. Silakan sinkronisasi terlebih dahulu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($customers) && $customers->total() > 0)
            <div class="d-flex justify-content-between align-items-center">
                <div>Menampilkan {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} dari {{ $customers->total() }} data</div>
                {{ $customers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection