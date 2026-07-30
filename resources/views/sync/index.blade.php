@extends('layouts.app')

@section('title', 'Sinkronisasi Data')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Sinkronisasi Data Customer
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Status --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Customer</h6>
                                    <h2 class="mb-0 text-primary">{{ number_format($status['total_customer'] ?? 0) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Last Sync</h6>
                                    <h5 class="mb-0">{{ $status['last_update_human'] ?? 'Never' }}</h5>
                                    <small class="text-muted">{{ $status['last_update'] ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Sinkronisasi akan mengambil data dari Google Sheets</li>
                            <li>Proses menggunakan <strong>chunk 1000</strong> data untuk performa</li>
                            <li>Data akan di-<strong>upsert</strong> (update jika sudah ada, insert jika baru)</li>
                            <li>Total data sekitar <strong>26.000+</strong> customer</li>
                            <li>Proses hanya mengupdate data yang <strong>berubah</strong> (lebih cepat!)</li>
                            <li>Angka tagihan otomatis di-normalisasi (4.260.652 → 4260652)</li>
                        </ul>
                    </div>

                    {{-- Tombol Sync --}}
                    <div class="text-center">
                        <form action="{{ route('sync.google-sheets') }}" method="GET">
                            <button type="submit" class="btn btn-primary btn-lg px-5" 
                                    onclick="return confirmSync()">
                                <i class="bi bi-cloud-arrow-down me-2"></i>
                                Mulai Sinkronisasi
                            </button>
                        </form>
                        <p class="text-muted mt-3 small">
                            <i class="bi bi-clock me-1"></i>
                            Jangan refresh halaman selama proses berjalan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmSync() {
    return confirm('Apakah Anda yakin ingin memulai sinkronisasi data?\n\nProses ini akan mengambil data dari Google Sheets dan memperbarui database.\nMohon tunggu hingga proses selesai.');
}
</script>
@endsection