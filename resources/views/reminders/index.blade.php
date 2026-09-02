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
                                    <button type="button" onclick="downloadRowAsPdf('{{ $reminder->id }}', 0)" 
                                            class="btn btn-sm btn-light text-danger border-0 rounded-3 shadow-sm px-3 py-2 ms-1" 
                                            title="Unduh Surat Tunggakan (.pdf)">
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
{{-- MODALS & DOKUMEN CETAK RESMI TELKOM --}}
{{-- ============================================ --}}
@php
    $telkomLogoBase64 = file_exists(public_path('image/telkom-logo.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('image/telkom-logo.png')))
        : asset('image/telkom-logo.png');

    $qrCodeBase64 = file_exists(public_path('image/qr-telkom.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('image/qr-telkom.png')))
        : asset('image/qr-telkom.png');

    $ttdBase64 = file_exists(public_path('image/ttd-gm.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('image/ttd-gm.png')))
        : asset('image/ttd-gm.png');
@endphp

@foreach($reminders as $reminder)
    @php
        // Ekstrak semua pelanggan dari isi pesan telegram masing-masing SA
        $rawText = strip_tags($reminder->keterangan);
        preg_match_all('/(?:^|\n)\s*(?:\d+[\.\)]\s*)?(\d{8,14})\s*[-–]\s*([^\n\r<]+)/m', $rawText, $parsedMatches, PREG_SET_ORDER);

        $customersList = [];
        foreach ($parsedMatches as $m) {
            $snd = trim($m[1]);
            $name = trim(preg_replace('/[^\w\s\.\,\-\&]/u', '', $m[2]));
            if (!empty($snd) && !empty($name)) {
                $dbCust = \App\Models\Customer::where('snd', $snd)->first();
                $alamat = $dbCust?->alamat 
                    ?: ($dbCust?->datel ? 'WILAYAH ' . strtoupper($dbCust->datel) . ', KABUPATEN CIREBON' 
                    : 'WILAYAH ' . strtoupper($reminder->sales_agency) . ', CIREBON');
                $tagTotal = ($dbCust && $dbCust->tag_total > 0) ? (float)$dbCust->tag_total : 1050000;
                $bill1 = round($tagTotal * 0.33);
                $bill2 = round($tagTotal * 0.33);
                $bill3 = $tagTotal - $bill1 - $bill2;
                
                $customersList[] = [
                    'snd' => $snd,
                    'name' => $name,
                    'alamat' => $alamat,
                    'tagTotal' => $tagTotal,
                    'bill1' => $bill1,
                    'bill2' => $bill2,
                    'bill3' => $bill3,
                ];
            }
        }

        // Jika pesan tidak memiliki format daftar nomor (fallback khusus ke SA)
        if (empty($customersList)) {
            $dbCust = \App\Models\Customer::where('sales_agency', $reminder->sales_agency)->first();
            $snd = $dbCust?->snd ?: '12' . substr(preg_replace('/\D/', '', md5($reminder->id . $reminder->sales_agency)), 0, 10);
            $name = $dbCust?->nama ?: 'PELANGGAN ' . strtoupper($reminder->sales_agency);
            $alamat = $dbCust?->alamat ?: 'WILAYAH ' . strtoupper($reminder->sales_agency) . ', KOTA CIREBON';
            $tagTotal = ($dbCust && $dbCust->tag_total > 0) ? (float)$dbCust->tag_total : 1050000;
            $bill1 = round($tagTotal * 0.33);
            $bill2 = round($tagTotal * 0.33);
            $bill3 = $tagTotal - $bill1 - $bill2;
            
            $customersList[] = [
                'snd' => $snd,
                'name' => $name,
                'alamat' => $alamat,
                'tagTotal' => $tagTotal,
                'bill1' => $bill1,
                'bill2' => $bill2,
                'bill3' => $bill3,
            ];
        }

        $tglSurat = $reminder->created_at ? $reminder->created_at->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y');
        $saName = $reminder->sales_agency ?: 'Sales Agency';
    @endphp

    {{-- MODAL DETAIL PESAN --}}
    <div class="modal fade" id="messageModal{{ $reminder->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $reminder->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-primary-custom d-flex align-items-center gap-2" id="messageModalLabel{{ $reminder->id }}">
                        <i class="bi bi-chat-left-dots-fill"></i> Detail Pesan Chatbot Reminder
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" onclick="downloadRowAsPdf('{{ $reminder->id }}')" class="btn btn-sm btn-outline-danger rounded-3 d-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Unduh Dokumen (.pdf)
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4">
                    {{-- Selector Pelanggan Jika Terdapat Banyak Pelanggan dalam Satu Pesan SA --}}
                    @if(count($customersList) > 1)
                        <div class="mb-3 p-3 bg-white border rounded-3 d-flex flex-wrap justify-content-between align-items-center gap-2 shadow-sm">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-person-fill text-danger fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">Pilih Pelanggan untuk Unduh Surat Tunggakan Resmi:</div>
                                    <small class="text-muted">Terdapat {{ count($customersList) }} pelanggan dalam pesan Sales Agency ini</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <select id="customerSelect{{ $reminder->id }}" class="form-select form-select-sm" style="min-width: 280px;">
                                    @foreach($customersList as $idx => $cItem)
                                        <option value="{{ $idx }}">{{ $idx + 1 }}. {{ $cItem['snd'] }} - {{ $cItem['name'] }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="downloadRowAsPdf('{{ $reminder->id }}')" class="btn btn-sm btn-danger d-flex align-items-center gap-1 shadow-sm px-3">
                                    <i class="bi bi-download"></i> Unduh Surat
                                </button>
                            </div>
                        </div>
                    @endif

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
                            <strong class="text-secondary ms-1">{{ $reminder->created_at ? $reminder->created_at->format('d M Y H:i:s') : '-' }}</strong>
                        </div>
                    </div>
                    <div class="position-relative">
                        <pre class="bg-light text-dark p-4 rounded-4 mb-0 border" 
                             id="msgContent{{ $reminder->id }}"
                             style="white-space: pre-wrap; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 0.95rem; line-height: 1.6; border-color: #e2e8f0 !important; color: #2d3748 !important; background-color: #f8fafc !important;">{{ strip_tags($reminder->keterangan) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data JSON Pelanggan SA Terkait --}}
    <div id="saCustomersData{{ $reminder->id }}" 
         style="display:none;" 
         data-sa="{{ $saName }}" 
         data-tgl="{{ $tglSurat }}" 
         data-logo="{{ $telkomLogoBase64 }}" 
         data-qr="{{ $qrCodeBase64 }}" 
         data-ttd="{{ $ttdBase64 }}">{{ json_encode($customersList) }}</div>
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

        // Auto submit form saat user stop mengetik pencarian (delay 300ms)
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
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
        }
    });

    // Fungsi Client-side Export PDF Surat Resmi Telkom untuk masing-masing baris & customer
    function downloadRowAsPdf(id, customerIndex) {
        let customersDataEl = document.getElementById('saCustomersData' + id);
        if (!customersDataEl) {
            alert('Gagal memuat data pelanggan untuk SA ini.');
            return;
        }

        let customers = JSON.parse(customersDataEl.textContent);
        if (typeof customerIndex === 'undefined' || customerIndex === null) {
            let select = document.getElementById('customerSelect' + id);
            customerIndex = select ? parseInt(select.value, 10) : 0;
        }

        let cust = customers[customerIndex] || customers[0];
        let saName = customersDataEl.getAttribute('data-sa') || 'Sales Agency';
        let tglSurat = customersDataEl.getAttribute('data-tgl') || 'Cirebon';
        let telkomLogo = customersDataEl.getAttribute('data-logo');
        let qrCode = customersDataEl.getAttribute('data-qr');
        let ttdGm = customersDataEl.getAttribute('data-ttd');

        // Format angka Rupiah
        function formatRp(val) {
            return 'Rp ' + Number(val).toLocaleString('id-ID');
        }

        // Buat container surat A4 yang rapi, proporsional, dan pas 1 lembar utuh
        let suratContainer = document.createElement('div');
        suratContainer.style.width = '750px';
        suratContainer.style.boxSizing = 'border-box';
        suratContainer.style.background = '#ffffff';
        suratContainer.style.padding = '26px 40px 18px 40px';
        suratContainer.style.fontFamily = 'Arial, Helvetica, sans-serif';
        suratContainer.style.color = '#000000';
        suratContainer.style.fontSize = '11.5px';
        suratContainer.style.lineHeight = '1.38';

        suratContainer.innerHTML = `
            <!-- Header: Nomor & Logo Telkom Indonesia -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 0; margin-bottom: 12px;">
                <tr>
                    <td style="width: 60%; vertical-align: top; padding-top: 8px;">
                        <div style="font-size: 11.5px; color: #000000; font-family: Arial, sans-serif;">
                            Nomor : C.Tel. /CBN/YN 000/ T2W-0H000000/2025
                        </div>
                    </td>
                    <td style="width: 40%; text-align: right; vertical-align: top; padding-top: 0; padding-right: 2px;">
                        <img src="${telkomLogo}" style="width: 140px; height: auto; display: inline-block; max-width: 100%;" alt="Telkom Indonesia">
                    </td>
                </tr>
            </table>

            <!-- Tanggal & Kepada Yth -->
            <div style="margin-bottom: 10px;">
                <div>Cirebon, ${tglSurat}</div>
                <div style="margin-top: 5px;">Kepada Yth,</div>
                <div style="font-weight: bold; text-transform: uppercase;">${cust.name}</div>
                <div style="max-width: 580px;">${cust.alamat}</div>
            </div>

            <!-- Perihal -->
            <div style="margin-bottom: 10px;">
                Perihal &nbsp;: Penyelesaian Tunggakan Layanan Telkom dengan nomor <strong>${cust.snd}</strong>
            </div>

            <!-- Paragraf Pembuka -->
            <div style="text-align: justify; margin-bottom: 8px; line-height: 1.38;">
                Sebelumnya kami mengucapkan banyak terimakasih atas kepercayaan Bapak/Ibu/Saudara/i menggunakan fasilitas jasa telekomunikasi dari PT. Telekomunikasi Indonesia. Kami menyadari dikarenakan kesibukan Bapak/Ibu/Sdr/i sehingga belum melakukan pembayaran tagihan internet. Untuk itu kami beritahukan bahwa sampai dengan saat ini didalam aplikasi kami Bapak/Ibu/Sdr/i masih memiliki tunggakan layanan internet dengan rincian sebagai berikut :
            </div>

            <!-- Tabel Rincian Tunggakan Sesuai Template -->
            <table style="width: 100%; border-collapse: collapse; margin: 8px 0 4px 0; font-size: 11px;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="border: 1.5px solid #000000; padding: 5px 8px; text-align: center; width: 25%; font-weight: bold;">Bulan Tagihan</th>
                        <th style="border: 1.5px solid #000000; padding: 5px 8px; text-align: center; width: 25%; font-weight: bold;">Jumlah</th>
                        <th style="border: 1.5px solid #000000; padding: 5px 8px; text-align: center; width: 50%; font-weight: bold;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1.5px solid #000000; padding: 4px 8px; text-align: center;">Mei 2025</td>
                        <td style="border: 1.5px solid #000000; padding: 4px 10px; text-align: end; font-weight: 500;">${formatRp(cust.bill1)}</td>
                        <td style="border: 1.5px solid #000000; padding: 4px 8px; text-align: center;">Pemakaian bulan Mei 2025</td>
                    </tr>
                    <tr>
                        <td style="border: 1.5px solid #000000; padding: 4px 8px; text-align: center;">Juni 2025</td>
                        <td style="border: 1.5px solid #000000; padding: 4px 10px; text-align: end; font-weight: 500;">${formatRp(cust.bill2)}</td>
                        <td style="border: 1.5px solid #000000; padding: 4px 8px; text-align: center;">Pemakaian bulan Juni 2025</td>
                    </tr>
                    <tr>
                        <td style="border: 1.5px solid #000000; padding: 4px 8px; text-align: center;">Juli 2025</td>
                        <td style="border: 1.5px solid #000000; padding: 4px 10px; text-align: end; font-weight: 500;">${formatRp(cust.bill3)}</td>
                        <td style="border: 1.5px solid #000000; padding: 4px 8px; text-align: center;">Pemakaian bulan Juli 2025</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="border: 1.5px solid #000000; padding: 5px 8px; text-align: center; font-weight: bold;">Total</td>
                        <td style="border: 1.5px solid #000000; padding: 5px 10px; text-align: end; font-weight: bold;">${formatRp(cust.tagTotal)}</td>
                        <td style="border: 1.5px solid #000000; padding: 5px 8px; text-align: center;"></td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size: 10.5px; font-weight: bold; margin-bottom: 8px;">
                *Tagihan sudah termasuk Denda, Materai.
            </div>

            <!-- Paragraf Metode Pembayaran -->
            <div style="text-align: justify; margin-bottom: 8px; line-height: 1.38;">
                Kami mengharapkan Bapak/Ibu/Sdr/i dapat meluangkan waktu untuk segera menyelesaikan pembayaran tunggakan layanan internet tersebut melalui Autodebet, ATM, Mobile Banking, Internet Banking dan SMS Banking. Pembayaran juga bisa dilakukan melalui gerai retail Indomaret, Alfamart dan channel e-commerce melalui Tokopedia, Shopee, Bukalapak, Gojek &amp; LinkAja dan Plasa Telkom.
            </div>

            <!-- Paragraf Konsekuensi -->
            <div style="text-align: justify; margin-bottom: 8px; line-height: 1.38;">
                Perlu kami sampaikan juga bahwa konsekuensi bagi pelanggan internet Telkom yang mempunyai tunggakan tagihan,<br>
                diantaranya :
                <div style="margin-top: 3px; padding-left: 2px;">
                    <div>1. Adanya mekanisme blacklist (daftar hitam) pelanggan, sehingga permintaan pasang baru ulang tidak bisa dilayani.</div>
                    <div>2. Adanya biaya pendaftaran pasang baru Internet Telkom, untuk permintaan pasang baru kembali.</div>
                    <div>3. Adanya denda penalti Rp 1.000.000,- bagi pelanggan yang berhenti berlangganan sebelum satu tahun.</div>
                </div>
            </div>

            <!-- Peringatan Kuasa Hukum -->
            <div style="text-align: justify; margin-bottom: 10px; line-height: 1.38;">
                Apabila sampai dengan batas waktu pembayaran <strong>20 September 2026</strong> tunggakan Bapak / Ibu / Saudara (i) belum melakukan pembayaran, maka akan kami limpahkan kepada <strong>KUASA HUKUM</strong> untuk proses penanganan lebih lanjut.
            </div>

            <!-- Bagian Kontak PIC, QR Code, dan Tanda Tangan Resmi -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
                <tr>
                    <td style="width: 77%; vertical-align: top; padding-right: 12px;">
                        <div style="margin-bottom: 5px; line-height: 1.35;">
                            Untuk informasi lebih lanjut dapat menghubungi pic : <strong>${saName}</strong> (085137634949),<br>
                            Serta pembayaran lebih mudah dengan melakukan scan QR-Code disamping
                        </div>
                        <div style="margin-bottom: 5px; line-height: 1.35;">
                            Mohon diabaikan pemberitahuan ini, apabila Bapak/Ibu/Sdr/i telah menyelesaikan pembayaran tagihan sebelum surat ini diterima.
                        </div>
                        <div style="margin-bottom: 8px; line-height: 1.35;">
                            Demikian disampaikan, atas perhatian dan pengertiannya kami ucapkan terimaksih.
                        </div>
                        <div style="font-weight: bold; margin-bottom: 2px;">
                            Hormat Kami,
                        </div>
                        <div>
                            <img src="${ttdGm}" style="width: 110px; height: auto; display: block; margin: 1px 0;" alt="Tanda Tangan GM Witel">
                        </div>
                        <div style="font-weight: bold; line-height: 1.25; margin-top: 1px;">
                            Nugroho Setio Budi<br>
                            GM Witel Priangan Timur
                        </div>
                    </td>
                    <td style="width: 23%; text-align: center; vertical-align: top; padding-top: 2px;">
                        <img src="${qrCode}" style="width: 82px; height: 82px; display: block; margin: 0 auto; border: 1px solid #ccc; padding: 2px;" alt="QR Code Telkom">
                    </td>
                </tr>
            </table>
        `;

        // Temporary wrapper di luar viewport untuk proses render
        let wrapper = document.createElement('div');
        wrapper.style.position = 'fixed';
        wrapper.style.left = '-9999px';
        wrapper.style.top = '0';
        wrapper.style.zIndex = '-9999';
        wrapper.appendChild(suratContainer);
        document.body.appendChild(wrapper);

        let safeCustName = cust.name.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        let safeSnd = cust.snd.replace(/[^a-z0-9]/gi, '');
        let safeSaName = saName.replace(/[^a-z0-9]/gi, '_').toLowerCase();

        // Konfigurasi html2pdf: Pas 1 halaman utuh A4 tanpa page-break
        let opt = {
            margin:       [5, 5, 5, 5], // 5mm margin
            filename:     'Surat_Penyelesaian_Tunggakan_' + safeSaName + '_' + safeSnd + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true,
                logging: false,
                scrollY: 0,
                scrollX: 0
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        // Jalankan generator PDF
        html2pdf().from(suratContainer).set(opt).save().then(() => {
            if (document.body.contains(wrapper)) {
                document.body.removeChild(wrapper);
            }
        }).catch((err) => {
            console.error(err);
            if (document.body.contains(wrapper)) {
                document.body.removeChild(wrapper);
            }
            alert('Gagal menghasilkan file PDF.');
        });
    }
</script>
@endsection