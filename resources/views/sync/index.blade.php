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
                        <button type="button" class="btn btn-primary btn-lg px-5 shadow-sm" id="btnStartSync">
                            <i class="bi bi-cloud-arrow-down me-2"></i>
                            Mulai Sinkronisasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Progress Sync -->
<div class="modal fade" id="syncProgressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="syncProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="spinner-border text-primary mb-3" role="status" id="syncSpinner" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div id="syncIconContainer"></div>
                <h5 class="modal-title mb-2 fw-bold" id="syncProgressTitle">Inisialisasi Sinkronisasi...</h5>
                <p class="text-muted small mb-4" id="syncProgressStatus">Menghubungi Google Sheets API...</p>
                
                <div class="progress mb-3" style="height: 20px; border-radius: 10px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="syncProgressBar">0%</div>
                </div>
                
                <div class="text-muted small fw-bold" id="syncDetails">
                    Baris diproses: <span id="processedRows">0</span> / <span id="totalRows">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnStartSync').addEventListener('click', function() {
    const syncModal = new bootstrap.Modal(document.getElementById('syncProgressModal'));
    syncModal.show();
    startSyncProcess();
});

async function startSyncProcess() {
    const progressBar = document.getElementById('syncProgressBar');
    const progressTitle = document.getElementById('syncProgressTitle');
    const progressStatus = document.getElementById('syncProgressStatus');
    const processedRowsText = document.getElementById('processedRows');
    const totalRowsText = document.getElementById('totalRows');
    const spinner = document.getElementById('syncSpinner');
    const iconContainer = document.getElementById('syncIconContainer');
    
    try {
        progressTitle.innerText = "Inisialisasi Sinkronisasi...";
        progressStatus.innerText = "Membaca header kolom & menghitung total baris...";
        
        const initResponse = await fetch("{{ route('sync.init') }}");
        const initData = await initResponse.json();
        
        if (!initData.success) {
            throw new Error(initData.message || "Gagal melakukan inisialisasi.");
        }
        
        const totalRows = initData.total_rows;
        const batchSize = initData.batch_size;
        totalRowsText.innerText = totalRows.toLocaleString();
        
        if (totalRows === 0) {
            progressTitle.innerText = "Sinkronisasi Selesai";
            progressStatus.innerText = "Tidak ada data untuk disinkronkan.";
            progressBar.style.width = "100%";
            progressBar.innerText = "100%";
            setTimeout(() => window.location.reload(), 1500);
            return;
        }
        
        const totalBatches = Math.ceil(totalRows / batchSize);
        let processedRows = 0;
        
        for (let batch = 1; batch <= totalBatches; batch++) {
            const start = ((batch - 1) * batchSize) + 2; // Data spreadsheet dimulai dari baris 2 (baris 1 header)
            const end = Math.min(start + batchSize - 1, totalRows + 1);
            
            progressTitle.innerText = `Menarik Data Batch ${batch} dari ${totalBatches}`;
            progressStatus.innerText = `Menyimpan data baris ${start.toLocaleString()} s/d ${end.toLocaleString()}...`;
            
            const response = await fetch("{{ route('sync.batch') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    batch: batch,
                    start: start,
                    end: end
                })
            });
            
            const batchResult = await response.json();
            
            if (!batchResult.success) {
                throw new Error(batchResult.message || `Gagal memproses batch ${batch}.`);
            }
            
            processedRows += (end - start + 1);
            processedRowsText.innerText = Math.min(processedRows, totalRows).toLocaleString();
            
            const percent = Math.round((batch / totalBatches) * 100);
            progressBar.style.width = `${percent}%`;
            progressBar.innerText = `${percent}%`;
            progressBar.setAttribute('aria-valuenow', percent);
        }
        
        // Sukses
        spinner.style.display = "none";
        iconContainer.innerHTML = '<i class="bi bi-check-circle-fill text-success mb-3 d-block" style="font-size: 3.5rem;"></i>';
        progressTitle.innerText = "Sinkronisasi Selesai!";
        progressStatus.className = "text-success fw-bold mb-4";
        progressStatus.innerText = "Seluruh data berhasil disimpan ke database!";
        
        setTimeout(() => {
            window.location.href = "{{ route('dashboard') }}?sync=success";
        }, 1500);
        
    } catch (error) {
        console.error(error);
        spinner.style.display = "none";
        iconContainer.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger mb-3 d-block" style="font-size: 3.5rem;"></i>';
        progressTitle.innerText = "Sinkronisasi Gagal";
        progressStatus.className = "text-danger fw-bold mb-4";
        progressStatus.innerText = error.message || "Terjadi kesalahan internal.";
        
        // Membuka akses tutup modal manual jika error
        const modalElement = document.getElementById('syncProgressModal');
        modalElement.setAttribute('data-bs-backdrop', 'true');
        modalElement.setAttribute('data-bs-keyboard', 'true');
        
        const closeBtn = document.createElement('button');
        closeBtn.type = "button";
        closeBtn.className = "btn btn-danger mt-3 px-4";
        closeBtn.innerText = "Tutup & Coba Lagi";
        closeBtn.onclick = () => window.location.reload();
        
        progressStatus.after(closeBtn);
    }
}
</script>
@endsection