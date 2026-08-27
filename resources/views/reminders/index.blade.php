@extends('layouts.app')

@section('title', 'RIWAYAT CHATBOT REMINDER')

@section('content')
<div class="container-fluid px-0">
    <p class="text-primary mb-4" style="margin-top: -15px; font-size: 0.95rem;">
        Daftar pengiriman reminder chatbot ke Sales Agency
    </p>

    {{-- ============================================ --}}
    {{-- FILTER BOX (TANGGAL & PENCARIAN) --}}
    {{-- ============================================ --}}
    <div class="card border border-2 rounded-4 mb-4 no-hover" style="border-color: #dbe0eb !important; box-shadow: none;">
        <div class="card-body p-4">
            <form method="GET" id="filterForm" class="row align-items-end g-3">
                {{-- Tanggal Mulai --}}
                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold mb-2" style="font-size: 0.85rem;">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control rounded-3 py-2" 
                           value="{{ request('start_date') }}" style="border-color: #ced4da !important;">
                </div>

                {{-- Tanggal Akhir --}}
                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold mb-2" style="font-size: 0.85rem;">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control rounded-3 py-2" 
                           value="{{ request('end_date') }}" style="border-color: #ced4da !important;">
                </div>

                {{-- Search Bar --}}
                <div class="col-md-4 ms-auto">
                    <div class="position-relative">
                        <input type="text" name="search" class="form-control rounded-3 py-2 pe-5" 
                               placeholder="Cari Customer / pesan...." value="{{ request('search') }}"
                               style="border-color: #ced4da !important;">
                        <span class="position-absolute end-0 top-50 translate-middle-y me-3">
                            <i class="bi bi-search text-secondary fs-5"></i>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABLE DATA --}}
    {{-- ============================================ --}}
    <div class="card border-0 rounded-4 overflow-hidden mb-3 no-hover" style="box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reminderTable">
                    <thead>
                        <tr class="text-secondary" style="background-color: #f3f6f9; border-bottom: 2px solid #ebedf3;">
                            <th class="py-3 px-4 text-center fw-bold" style="width: 60px; font-size: 0.85rem;">No</th>
                            <th class="py-3 px-3 fw-bold" style="width: 180px; font-size: 0.85rem;">Tanggal</th>
                            <th class="py-3 px-3 fw-bold" style="width: 220px; font-size: 0.85rem;">Sales Agency</th>
                            <th class="py-3 px-3 fw-bold text-center" style="width: 150px; font-size: 0.85rem;">Total SSL</th>
                            <th class="py-3 px-4 fw-bold text-center" style="width: 180px; font-size: 0.85rem;">Status</th>
                            <th class="py-3 px-3 fw-bold text-center" style="width: 150px; font-size: 0.85rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reminders as $index => $reminder)
                            <tr style="border-bottom: 1px solid #ebedf3;">
                                <td class="py-3 px-4 text-center text-secondary fw-semibold">{{ $reminders->firstItem() + $index }}</td>
                                <td class="py-3 px-3 text-secondary" style="font-size: 0.9rem;">
                                    {{ $reminder->created_at ? $reminder->created_at->format('d M Y H:i:s') : '-' }}
                                </td>
                                <td class="py-3 px-3 text-dark fw-bold text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.2px;">
                                    {{ $reminder->sales_agency ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-center fw-semibold text-secondary" style="font-size: 0.9rem;">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5" style="border-radius: 6px; font-size: 0.85rem;">
                                        {{ $reminder->total_ssl ?? 0 }} SSL
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if(in_array(strtolower($reminder->status), ['selesai', 'terkirim', 'lunas']))
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-3 py-2 fw-bold" style="font-size: 0.75rem; border-radius: 6px;">
                                            Terkirim
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 px-3 py-2 fw-bold" style="font-size: 0.75rem; border-radius: 6px;">
                                            Belum Terkirim
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center">
                                    {{-- Eye Icon (Detail) --}}
                                    <button type="button" class="btn btn-sm btn-light text-primary border-0 rounded-3 shadow-sm px-3 py-2" 
                                            data-bs-toggle="modal" data-bs-target="#messageModal{{ $reminder->id }}" 
                                            title="Lihat Detail Pesan">
                                        <i class="bi bi-eye-fill fs-5"></i>
                                    </button>
                                    {{-- Download Icon (PDF) --}}
                                    <button type="button" onclick="downloadRowAsPdf('{{ $reminder->id }}', '{{ $reminder->created_at ? $reminder->created_at->format('d M Y H:i:s') : '-' }}')" 
                                            class="btn btn-sm btn-light text-danger border-0 rounded-3 shadow-sm px-3 py-2 ms-1" 
                                            title="Unduh Laporan (.pdf)">
                                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-chat-left-text fs-1 d-block text-secondary mb-3"></i>
                                    Tidak ada data riwayat reminder ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            {{ $reminders->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODALS SECTION (Ditaruh di luar tabel agar stabil) --}}
{{-- ============================================ --}}
@foreach($reminders as $reminder)
    <div class="modal fade" id="messageModal{{ $reminder->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $reminder->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary-custom d-flex align-items-center gap-2" id="messageModalLabel{{ $reminder->id }}">
                        <i class="bi bi-chat-left-dots-fill"></i> Detail Pesan Chatbot Reminder
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center bg-light p-3 rounded-3 gap-2" style="font-size: 0.85rem;">
                        <div>
                            <span class="text-muted">Sales Agency:</span> 
                            <strong class="text-primary-custom text-uppercase ms-1" id="saName{{ $reminder->id }}">{{ $reminder->sales_agency }}</strong>
                        </div>
                        <div>
                            <span class="text-muted">Total SSL:</span> 
                            <strong class="text-dark ms-1">{{ $reminder->total_ssl }} SSL</strong>
                        </div>
                        <div>
                            <span class="text-muted">Tanggal:</span> 
                            <strong class="text-secondary ms-1">{{ $reminder->created_at->format('d M Y H:i:s') }}</strong>
                        </div>
                    </div>
                    <div class="position-relative">
                        <pre class="bg-dark text-light p-4 rounded-3 mb-0" 
                             id="msgContent{{ $reminder->id }}"
                             style="white-space: pre-wrap; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 0.95rem; line-height: 1.6; border: none;">{{ $reminder->keterangan }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Load Flatpickr CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Styling khusus tabel */
    #reminderTable tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    .badge {
        letter-spacing: 0.5px;
    }
    /* Mencegah card tabel melayang saat dihover */
    .no-hover:hover {
        transform: none !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02) !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
{{-- Library html2pdf.js untuk mengunduh PDF di sisi browser --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto submit form saat tanggal diubah
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        startDateInput.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        endDateInput.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        // Auto submit form saat user stop mengetik pencarian (delay dipercepat jadi 300ms agar responsif)
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 300);
        });
        
        // Posisikan cursor di akhir teks pencarian saat focus
        const val = searchInput.value;
        if (val) {
            searchInput.value = '';
            searchInput.focus();
            searchInput.value = val;
        }
    });

    // Fungsi Client-side Export PDF untuk masing-masing baris
    function downloadRowAsPdf(id, sentDate) {
        let contentElement = document.getElementById('msgContent' + id);
        let saNameElement = document.getElementById('saName' + id);
        
        if (!contentElement || !saNameElement) {
            alert('Gagal mengambil data untuk diunduh.');
            return;
        }

        let content = contentElement.textContent;
        let saName = saNameElement.textContent.trim();
        
        // Buat container tersembunyi di HTML body untuk me-render dokumen PDF
        let pdfContainer = document.createElement('div');
        pdfContainer.style.padding = '30px';
        pdfContainer.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        pdfContainer.style.color = '#333333';
        pdfContainer.style.backgroundColor = '#ffffff';

        // Template Desain Dokumen A4 Telkom Laporan Resmi
        pdfContainer.innerHTML = `
            <!-- Header -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="vertical-align: middle;">
                        <span style="font-size: 24px; font-weight: 800; color: #e2001a; letter-spacing: -0.5px;">Telkom</span>
                        <span style="font-size: 24px; font-weight: 400; color: #212529; letter-spacing: -0.5px;"> Indonesia</span>
                        <div style="font-size: 9px; color: #6c757d; margin-top: 2px; text-transform: uppercase; letter-spacing: 1px;">Collection Management System</div>
                    </td>
                    <td style="text-align: right; vertical-align: middle;">
                        <div style="font-size: 14px; font-weight: 700; color: #000361;">LAPORAN REMINDER BILLING</div>
                        <div style="font-size: 10px; color: #6c757d;">Dokumen Resmi Hasil Pengiriman Bot</div>
                    </td>
                </tr>
            </table>

            <div style="height: 3px; background: linear-gradient(90deg, #e2001a 0%, #ff5252 100%); margin-bottom: 25px; border-radius: 2px;"></div>

            <!-- Metadata Info -->
            <table style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 8px; margin-bottom: 25px;">
                <tr>
                    <td style="padding: 15px; border-bottom: 1px solid #dee2e6; width: 50%;">
                        <span style="font-size: 10px; color: #6c757d; display: block; text-transform: uppercase; letter-spacing: 0.5px;">Sales Agency (SA)</span>
                        <strong style="font-size: 14px; color: #000361; text-transform: uppercase;">${saName}</strong>
                    </td>
                    <td style="padding: 15px; border-bottom: 1px solid #dee2e6; width: 50%;">
                        <span style="font-size: 10px; color: #6c757d; display: block; text-transform: uppercase; letter-spacing: 0.5px;">Tanggal Kirim</span>
                        <strong style="font-size: 14px; color: #212529;">${sentDate}</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 15px; width: 50%;">
                        <span style="font-size: 10px; color: #6c757d; display: block; text-transform: uppercase; letter-spacing: 0.5px;">Metode Pengiriman</span>
                        <strong style="font-size: 14px; color: #212529;">Telegram Chatbot</strong>
                    </td>
                    <td style="padding: 15px; width: 50%;">
                        <span style="font-size: 10px; color: #6c757d; display: block; text-transform: uppercase; letter-spacing: 0.5px;">Status Laporan</span>
                        <strong style="font-size: 13px; color: #198754; background-color: rgba(25, 135, 84, 0.1); padding: 2px 8px; border-radius: 4px;">Sukses Terkirim</strong>
                    </td>
                </tr>
            </table>

            <!-- Content -->
            <div style="margin-bottom: 30px;">
                <div style="font-size: 11px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #dee2e6; padding-bottom: 6px; margin-bottom: 12px;">Isi Pesan Reminder Telegram</div>
                <div style="white-space: pre-wrap; font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; line-height: 1.6; color: #212529; padding: 5px 0;">${content}</div>
            </div>

            <!-- Footer -->
            <div style="border-top: 1px dashed #dee2e6; padding-top: 15px; margin-top: 40px; text-align: center; font-size: 9px; color: #868e96; line-height: 1.4;">
                Laporan ini diunduh secara resmi melalui Telkom Customer Management System.<br>
                © ${new Date().getFullYear()} Telkom Indonesia. All Rights Reserved.
            </div>
        `;

        // Append to body temporarily
        document.body.appendChild(pdfContainer);

        let safeSaName = saName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        if (!safeSaName) safeSaName = 'reminder';

        // Konfigurasi html2pdf
        let opt = {
            margin:       15,
            filename:     'Laporan_Reminder_' + safeSaName + '_' + new Date().toISOString().slice(0,10) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Jalankan generator PDF
        html2pdf().from(pdfContainer).set(opt).save().then(() => {
            document.body.removeChild(pdfContainer);
        }).catch((err) => {
            console.error(err);
            document.body.removeChild(pdfContainer);
            alert('Gagal menghasilkan file PDF.');
        });
    }
</script>
@endsection