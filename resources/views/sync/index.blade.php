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

                    {{-- Tombol Sync Google Sheets --}}
                    <div class="text-center mb-5 pb-4 border-bottom">
                        <form action="{{ route('sync.google-sheets') }}" method="GET">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" 
                                    onclick="return confirmSync()">
                                <i class="bi bi-arrow-repeat me-2"></i>
                                Ambil Data dari Google Sheets
                            </button>
                        </form>
                        <p class="text-muted mt-3 small mb-0">
                            <i class="bi bi-clock me-1"></i>
                            Gunakan ini jika perubahan data dilakukan di Google Sheets tim Anda.
                        </p>
                    </div>

                    {{-- Upload Excel Mentor --}}
                    <div>
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-file-earmark-excel me-2"></i>
                            Sinkronisasi Menggunakan File Excel Mentor
                        </h6>
                        <p class="text-muted small">
                            Jika mentor Anda tetap menggunakan Excel lokal dan melakukan perubahan di dalamnya, silakan unggah file Excel mentor tersebut (.xlsx) ke sini. Sistem akan menyelaraskan database lokal agar 100% sama dengan Excel mentor.
                        </p>

                        <form action="{{ route('sync.excel') }}" method="POST" enctype="multipart/form-data" class="mt-4" id="excelSyncForm" onsubmit="showExcelLoading()">
                            @csrf
                            
                            <!-- Opsi 1: Masukkan Path Lokal (OneDrive) -->
                            <div class="row g-2 align-items-center justify-content-center mb-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-dark small mb-1">Metode A: Sinkronisasi Otomatis Via Jalur Folder OneDrive/SharePoint Lokal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted" style="font-size: 0.85rem;"><i class="bi bi-folder-fill"></i></span>
                                        <input type="text" name="excel_path" class="form-control" 
                                               placeholder="Contoh: C:\Users\intern\OneDrive - Telkom Indonesia\Documents\BILL 1-6 HOTD & AGENCY 2026.xlsx" style="font-size: 0.85rem;">
                                        <button class="btn btn-primary px-4" type="submit">
                                            <i class="bi bi-play-fill me-1"></i> Sinkron Path
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-2">Sangat direkomendasikan! Cukup sync folder OneDrive mentor Anda ke Windows Explorer laptop Anda, masukkan path filenya ke sini, lalu klik tombol ini untuk sinkronisasi otomatis kapan saja tanpa perlu mengunduh file berkali-kali.</small>
                                </div>
                            </div>
                            
                            <div class="row align-items-center justify-content-center mb-4">
                                <div class="col-md-8 text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <hr class="w-25 my-0">
                                        <span class="px-3 text-muted small fw-bold">ATAU</span>
                                        <hr class="w-25 my-0">
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi 2: Upload File -->
                            <div class="row g-2 align-items-center justify-content-center">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-dark small mb-1">Metode B: Unggah Salinan File Excel Secara Manual</label>
                                    <div class="input-group">
                                        <input type="file" name="excel_file" class="form-control">
                                        <button class="btn btn-dark px-4" type="submit">
                                            <i class="bi bi-cloud-arrow-up me-1"></i> Unggah & Sinkron
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-2">Dukungan format: .xlsx, .xls, .csv (Maks. 10MB)</small>
                                </div>
                            </div>
                        </form>

                        <div id="excelLoading" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 small">Membaca file Excel dan memperbarui database. Mohon tunggu, jangan tutup halaman ini...</p>
                        </div>
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

function showExcelLoading() {
    document.getElementById('excelSyncForm').classList.add('d-none');
    document.getElementById('excelLoading').classList.remove('d-none');
}
</script>
@endsection