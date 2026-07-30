@extends('layouts.app')

@section('title', 'Rekap HOTD')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rekap HOTD (Datel)</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('customers.rekap-hotd') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <select name="datel" class="form-control">
                            <option value="">Semua Datel</option>
                            @foreach($datelList ?? [] as $datel)
                                <option value="{{ $datel }}" {{ request('datel') == $datel ? 'selected' : '' }}>{{ $datel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="billing_ke" class="form-control">
                            <option value="">Semua Billing</option>
                            @foreach($billingKeList ?? [] as $billing)
                                <option value="{{ $billing }}" {{ request('billing_ke') == $billing ? 'selected' : '' }}>{{ $billing }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            @if(isset($grandTotal) && $grandTotal)
            <div class="alert alert-info mb-3">
                <div class="row">
                    <div class="col-md-3"><strong>Total Customer:</strong> {{ number_format($grandTotal->total_customer ?? 0) }}</div>
                    <div class="col-md-3"><strong>Total Tagihan:</strong> Rp {{ number_format($grandTotal->total_tagihan ?? 0, 0, ',', '.') }}</div>
                    <div class="col-md-3"><strong>Belum Bayar:</strong> {{ number_format($grandTotal->blm_bayar ?? 0) }}</div>
                    <div class="col-md-3"><strong>Sudah Bayar:</strong> {{ number_format($grandTotal->sdh_bayar ?? 0) }}</div>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Datel</th>
                            <th>Billing Ke</th>
                            <th>Total Customer</th>
                            <th>Total Tagihan</th>
                            <th>Blm Bayar</th>
                            <th>Sdh Bayar</th>
                            <th>Blm Bayar (Rp)</th>
                            <th>Sdh Bayar (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekap ?? [] as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->datel }}</td>
                            <td>{{ $item->billing_ke }}</td>
                            <td>{{ number_format($item->total_customer) }}</td>
                            <td>Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                            <td>{{ number_format($item->blm_bayar) }}</td>
                            <td>{{ number_format($item->sdh_bayar) }}</td>
                            <td>Rp {{ number_format($item->total_blm_bayar, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->total_sdh_bayar, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data. Silakan sinkronisasi terlebih dahulu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection