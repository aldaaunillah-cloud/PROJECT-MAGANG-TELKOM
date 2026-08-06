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
    <div class="card border border-2 rounded-4 mb-4" style="border-color: #dbe0eb !important; box-shadow: none;">
        <div class="card-body p-4">
            <form method="GET" id="filterForm" class="row align-items-end g-3">
                {{-- Date Range Picker --}}
                <div class="col-md-5">
                    <label class="form-label text-secondary fw-semibold mb-2" style="font-size: 0.85rem;">Tanggal</label>
                    <div class="input-group border rounded-3 overflow-hidden" style="border-color: #ced4da !important;">
                        <input type="text" id="daterange" name="daterange" class="form-control border-0 py-2 bg-white" 
                               placeholder="Pilih tanggal..." value="{{ request('daterange') }}" readonly>
                        <span class="input-group-text bg-white border-0 pe-3" id="calendarIcon" style="cursor: pointer;">
                            <i class="bi bi-calendar3 text-primary fs-5"></i>
                        </span>
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="col-md-5 ms-auto">
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
    <div class="card border-0 rounded-4 overflow-hidden mb-3" style="box-shadow: 0 4px 20px rgba(0,0,0,0.02);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reminderTable">
                    <thead>
                        <tr class="text-secondary" style="background-color: #f3f6f9; border-bottom: 2px solid #ebedf3;">
                            <th class="py-3 px-4 text-center fw-bold" style="width: 60px; font-size: 0.85rem;">No</th>
                            <th class="py-3 px-3 fw-bold" style="width: 180px; font-size: 0.85rem;">Tanggal</th>
                            <th class="py-3 px-3 fw-bold" style="width: 180px; font-size: 0.85rem;">Sales Agency</th>
                            <th class="py-3 px-3 fw-bold" style="width: 220px; font-size: 0.85rem;">Customer</th>
                            <th class="py-3 px-3 fw-bold" style="font-size: 0.85rem;">Isi Pesan</th>
                            <th class="py-3 px-4 fw-bold text-center" style="width: 150px; font-size: 0.85rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reminders as $index => $reminder)
                            <tr style="border-bottom: 1px solid #ebedf3;">
                                <td class="py-3 px-4 text-center text-secondary fw-semibold">{{ $reminders->firstItem() + $index }}</td>
                                <td class="py-3 px-3 text-secondary" style="font-size: 0.9rem;">
                                    {{ $reminder->created_at ? $reminder->created_at->format('d M Y H:i:s') : '-' }}
                                </td>
                                <td class="py-3 px-3 text-secondary fw-semibold" style="font-size: 0.9rem;">
                                    {{ $reminder->customer->sales_agency ?? $reminder->customer->sales ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-dark fw-bold text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.2px;">
                                    {{ $reminder->customer->nama ?? '-' }}
                                </td>
                                <td class="py-3 px-3 text-secondary" style="font-size: 0.875rem; line-height: 1.5; max-width: 450px;">
                                    {{ $reminder->keterangan ?? '-' }}
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

    {{-- Pagination & Export button --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">
        {{-- Pagination --}}
        <div>
            {{ $reminders->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>

        {{-- Export Excel Button --}}
        <div>
            <button onclick="exportToExcel()" class="btn btn-outline-primary d-flex align-items-center gap-2 px-4 py-2 fw-semibold" 
                    style="border-color: #0d6efd; color: #0d6efd; border-radius: 8px; font-size: 0.9rem;">
                <span>Klik Unduh</span>
                <i class="bi bi-download"></i>
            </button>
        </div>
    </div>
</div>

{{-- Load Flatpickr CSS & JS --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Styling khusus tabel */
    #reminderTable tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    /* Ganti warna hover bawaan theme */
    .table-hover tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    /* Badge styling */
    .badge {
        letter-spacing: 0.5px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Flatpickr Range Mode
        const dateInput = flatpickr("#daterange", {
            mode: "range",
            dateFormat: "d/m/Y",
            locale: "id",
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2 || dateStr === "") {
                    document.getElementById('filterForm').submit();
                }
            }
        });

        // Trigger flatpickr open ketika klik icon kalender
        document.getElementById('calendarIcon').addEventListener('click', function() {
            dateInput.open();
        });

        // Auto submit form saat user stop mengetik pencarian (debounce)
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 600);
        });
        
        // Posisikan cursor di akhir teks pencarian saat focus
        const val = searchInput.value;
        searchInput.value = '';
        searchInput.focus();
        searchInput.value = val;
    });

    // Fungsi Client-side Export Excel
    function exportToExcel() {
        let table = document.getElementById('reminderTable');
        
        // Buat salinan tabel agar tidak merusak tampilan asli saat export
        let tableClone = table.cloneNode(true);
        
        // Hapus elemen HTML atau tag yang tidak perlu diexport
        let html = tableClone.outerHTML;
        
        // Format download Excel
        let uri = 'data:application/vnd.ms-excel;base64,';
        let template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:office" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml>' +
                       '<' + 'x:ExcelWorkbook>' +
                       '<' + 'x:ExcelWorksheets>' +
                       '<' + 'x:ExcelWorksheet>' +
                       '<' + 'x:Name>{worksheet}</' + 'x:Name>' +
                       '<' + 'x:WorksheetOptions>' +
                       '<' + 'x:DisplayGridlines/>' +
                       '</' + 'x:WorksheetOptions>' +
                       '</' + 'x:ExcelWorksheet>' +
                       '</' + 'x:ExcelWorksheets>' +
                       '</' + 'x:ExcelWorkbook>' +
                       '</xml><![endif]--><meta charset="UTF-8"></head><body><table>{table}</table></body></html>';
        
        let base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)));
        };
        
        let format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; });
        };
        
        let ctx = {
            worksheet: 'Riwayat Reminder',
            table: html
        };
        
        let link = document.createElement("a");
        link.download = "Riwayat_Chatbot_Reminder_" + new Date().toISOString().slice(0,10) + ".xls";
        link.href = uri + base64(format(template, ctx));
        link.click();
    }
</script>
@endpush
@endsection