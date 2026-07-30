@extends('layouts.app')

@section('title', 'Data All Customer')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data All Customer</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('customers.data-all') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="datel" class="form-control">
                            <option value="">Semua Datel</option>
                            @foreach($datelList ?? [] as $datel)
                                <option value="{{ $datel }}" {{ request('datel') == $datel ? 'selected' : '' }}>{{ $datel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="agency" class="form-control">
                            <option value="">Semua Agency</option>
                            @foreach($agencyList ?? [] as $agency)
                                <option value="{{ $agency }}" {{ request('agency') == $agency ? 'selected' : '' }}>{{ $agency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="sales" class="form-control">
                            <option value="">Semua Sales</option>
                            @foreach($salesList ?? [] as $sales)
                                <option value="{{ $sales }}" {{ request('sales') == $sales ? 'selected' : '' }}>{{ $sales }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <select name="billing_ke" class="form-control">
                            <option value="">Semua Billing</option>
                            @foreach($billingKeList ?? [] as $billing)
                                <option value="{{ $billing }}" {{ request('billing_ke') == $billing ? 'selected' : '' }}>{{ $billing }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
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
                            <th>Agency</th>
                            <th>Sales</th>
                            <th>Tagihan</th>
                            <th>Billing</th>
                            <th>Saldo</th>
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
                            <td>{{ $customer->agency_psb }}</td>
                            <td>{{ $customer->sales_agency }}</td>
                            <td>Rp {{ number_format($customer->tag_total, 0, ',', '.') }}</td>
                            <td>{{ $customer->billing_ke }}</td>
                            <td>Rp {{ number_format($customer->saldo, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada data. Silakan sinkronisasi terlebih dahulu.</td>
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