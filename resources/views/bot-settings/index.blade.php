@extends('layouts.app')

@section('title', 'Pengaturan Chat Bot')

@section('content')
<style>
    /* Custom Toggle Switch Styling agar Kontras dan Jelas */
    .custom-switch {
        width: 3rem !important;
        height: 1.6rem !important;
        transition: all 0.3s ease !important;
    }
    .custom-switch:checked {
        background-color: #198754 !important;
        border-color: #198754 !important;
        opacity: 1 !important;
    }
    .custom-switch:not(:checked) {
        background-color: #cbd5e1 !important;
        border-color: #94a3b8 !important;
        opacity: 1 !important;
    }
    .custom-switch:disabled {
        opacity: 0.9 !important;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid px-0">

    {{-- Top Badges Status Bar --}}
    <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
        <span id="modeBadge" class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill fw-bold">
            <i class="bi bi-lock-fill me-1" id="modeBadgeIcon"></i>
            <span id="modeBadgeText">Mode Terkunci</span>
        </span>

        <span class="badge {{ $setting->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} px-3 py-2 rounded-pill fw-bold border" id="topBotStatusBadge">
            <i class="bi {{ $setting->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1" id="topBotStatusIcon"></i>
            <span id="topBotStatusText">Bot: {{ $setting->is_active ? 'AKTIF' : 'NONAKTIF' }}</span>
        </span>
    </div>

    {{-- Notifikasi Error Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2 mt-0.5"></i>
                <div>
                    <div class="fw-bold mb-1">Terjadi kesalahan input:</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Alert Feedback Uji Coba Telegram via AJAX --}}
    <div id="testAlertContainer" style="display: none;">
        <div class="alert alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" id="testAlert" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi fs-5 me-2" id="testAlertIcon"></i>
                <div id="testAlertMsg" class="fw-semibold small"></div>
            </div>
            <button type="button" class="btn-close" onclick="document.getElementById('testAlertContainer').style.display='none'"></button>
        </div>
    </div>

    <form action="{{ route('bot-settings.update') }}" method="POST" id="botSettingForm">
        @csrf
        <div class="row g-4">
            {{-- KOLOM KIRI: KONFIGURASI TELEGRAM & HOTD --}}
            <div class="col-12 col-xl-7">
                <div class="card border-0 rounded-4 shadow-sm h-100 bg-white overflow-hidden">
                    <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center text-danger" style="width: 36px; height: 36px;">
                                <i class="bi bi-telegram fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Target Pengiriman Telegram & HOTD</h6>
                                <small class="text-muted">Atur grup Telegram bersama dan daftar PIC HOTD yang akan di-mention</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        {{-- ID Grup Telegram HOTD (Dengan Fitur Sembunyikan/Lihat) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">
                                ID Grup Telegram HOTD <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-people-fill text-muted"></i></span>
                                <input type="password" name="telegram_group_id" id="telegram_group_id" class="form-control bg-light border-0 py-2 font-monospace bot-field" 
                                       placeholder="Contoh: -5430258004" value="{{ old('telegram_group_id', $setting->telegram_group_id) }}" disabled required>
                                <button class="btn btn-light border-0" type="button" onclick="toggleMask('telegram_group_id', 'eyeIconGroupId')" title="Lihat / Sembunyikan ID Grup">
                                    <i class="bi bi-eye text-muted" id="eyeIconGroupId"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-info-circle me-1"></i> ID Grup Telegram selalu diawali dengan tanda minus (<code>-</code>). Pesan rekap billing akan dikirim ke grup ini.
                            </small>
                        </div>

                        {{-- Username Tag HOTD (Input Dinamis Satu per Satu) --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold text-dark small mb-0">
                                    Daftar Tag / Mention PIC HOTD di Grup
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 d-none align-items-center gap-1" id="btnAddHotd" style="font-size: 0.75rem;">
                                    <i class="bi bi-plus-circle-fill"></i>
                                    <span>Tambah HOTD</span>
                                </button>
                            </div>

                            <div id="hotdListContainer" class="d-flex flex-column gap-2">
                                @foreach($hotdList as $index => $hotdItem)
                                    <div class="input-group hotd-row">
                                        <span class="input-group-text bg-light border-0 text-muted fw-bold">@</span>
                                        <input type="text" name="hotd_mentions[]" class="form-control bg-light border-0 py-2 hotd-input bot-field" 
                                               placeholder="Username HOTD (contoh: hotdAinun)" value="{{ ltrim($hotdItem, '@') }}" disabled>
                                        <button type="button" class="btn btn-light border-0 text-danger btn-remove-hotd d-none" title="Hapus HOTD">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i> Masukkan username Telegram masing-masing HOTD tanpa tanda @ (otomatis ditambahkan bot).
                            </small>
                        </div>

                        {{-- Token Bot Telegram --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">
                                Token Bot Telegram <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-key-fill text-muted"></i></span>
                                <input type="password" name="bot_token" id="bot_token" class="form-control bg-light border-0 py-2 font-monospace bot-field" 
                                       placeholder="Contoh: 8887064573:AAH1ah..." value="{{ old('bot_token', $setting->bot_token) }}" disabled required>
                                <button class="btn btn-light border-0" type="button" onclick="toggleMask('bot_token', 'eyeIconBotToken')" title="Lihat / Sembunyikan Token">
                                    <i class="bi bi-eye text-muted" id="eyeIconBotToken"></i>
                                </button>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Diperoleh dari <strong>@BotFather</strong> di Telegram.
                            </small>
                        </div>

                        {{-- Jeda Waktu Pengiriman Pesan --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small">Jeda Antar Pengiriman Pesan (ms) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-stopwatch text-muted"></i></span>
                                <input type="number" name="delay_ms" class="form-control bg-light border-0 py-2 bot-field" 
                                       min="500" max="10000" step="100" value="{{ old('delay_ms', $setting->delay_ms) }}" disabled required>
                                <span class="input-group-text bg-light border-0 small text-muted">milidetik</span>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                Rekomendasi: 1500 ms (1.5 detik) untuk mencegah bot terkena rate limit dari Telegram.
                            </small>
                        </div>

                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: INTEGRASI WEB & API --}}
            <div class="col-12 col-xl-5">
                <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom border-light p-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary" style="width: 36px; height: 36px;">
                                <i class="bi bi-globe fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Integrasi Web URL & API</h6>
                                <small class="text-muted">Koneksi antara Google Apps Script dan Laravel</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        {{-- URL Aplikasi Web --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">
                                URL Aplikasi Web (Base URL) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg text-muted"></i></span>
                                <input type="url" name="app_url" class="form-control bg-light border-0 py-2 bot-field" 
                                       placeholder="https://reminder.crafts.web.id" value="{{ old('app_url', $setting->app_url) }}" disabled required>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Pastikan menggunakan <code>https://</code> untuk server produksi.
                            </small>
                        </div>

                        {{-- Secret API Token (Dengan Fitur Masking dan Generate Acak) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">
                                API Secret Token <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-shield-lock-fill text-muted"></i></span>
                                <input type="password" name="api_token" id="api_token" class="form-control bg-light border-0 py-2 font-monospace bot-field" 
                                       value="{{ old('api_token', $setting->api_token) }}" disabled required>
                                <button class="btn btn-light border-0" type="button" onclick="toggleMask('api_token', 'eyeIconApiToken')" title="Lihat / Sembunyikan Token">
                                    <i class="bi bi-eye text-muted" id="eyeIconApiToken"></i>
                                </button>
                                <button class="btn btn-light border-0 text-success d-none" type="button" id="btnGenerateToken" title="Generate Token Acak Kuat">
                                    <i class="bi bi-shuffle me-1"></i> Generate
                                </button>
                            </div>

                            {{-- Rekomendasi Rotasi Keamanan 3 Bulan --}}
                            <div class="p-2.5 rounded-3 bg-light border mt-2" style="font-size: 0.75rem;">
                                <div class="d-flex align-items-start gap-1.5 text-secondary">
                                    <div>
                                        <strong>Rekomendasi Keamanan:</strong> Disarankan memperbarui/merotasi API Secret Token secara berkala setiap <strong>3 bulan sekali</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Switch Status Bot (Dengan Kotak Highlight Jelas) --}}
                        <div class="p-3 rounded-4 mt-3 border d-flex align-items-center justify-content-between {{ $setting->is_active ? 'bg-success bg-opacity-10 border-success border-opacity-25' : 'bg-danger bg-opacity-10 border-danger border-opacity-25' }}" id="statusSwitchBox">
                            <div>
                                <label class="form-check-label fw-bold text-dark small mb-0 d-block" for="is_active">
                                    Status Chat Bot
                                </label>
                                <small class="fw-semibold {{ $setting->is_active ? 'text-success' : 'text-danger' }}" id="statusSwitchText">
                                    {{ $setting->is_active ? '● Bot Aktif (Reminder Berjalan)' : '○ Bot Nonaktif (Reminder Ditahan)' }}
                                </small>
                            </div>
                            <div class="form-check form-switch m-0 ps-0">
                                <input class="form-check-input custom-switch bot-field ms-0" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $setting->is_active) ? 'checked' : '' }} disabled>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PANEL AKSI & UJI COBA --}}
                <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            {{-- Tombol Edit Pengaturan (Aktif saat locked) --}}
                            <button type="button" id="btnEditMode" class="btn btn-primary py-2.5 rounded-3 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit Pengaturan</span>
                            </button>

                            {{-- Tombol Simpan Pengaturan (Hanya muncul saat mode edit) --}}
                            <button type="submit" id="btnSaveConfig" class="btn btn-danger py-2.5 rounded-3 fw-semibold shadow-sm d-none align-items-center justify-content-center gap-2">
                                <i class="bi bi-floppy-fill"></i>
                                <span>Simpan Pengaturan</span>
                            </button>

                            {{-- Tombol Batal Edit (Hanya muncul saat mode edit) --}}
                            <button type="button" id="btnCancelEdit" class="btn btn-light border py-2 rounded-3 d-none align-items-center justify-content-center gap-2 text-secondary">
                                <i class="bi bi-x-circle"></i>
                                <span>Batal</span>
                            </button>

                            <hr class="my-1 border-light">

                            {{-- Tombol Tes Koneksi Telegram (Bisa dijalankan kapan saja) --}}
                            <button type="button" id="btnTestTelegram" class="btn btn-outline-secondary py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                                <i class="bi bi-send-fill" id="btnTestIcon"></i>
                                <span id="btnTestText">Tes Kirim Pesan ke Grup Telegram</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    {{-- CARD PANDUAN INTEGRASI --}}
    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden mt-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                <i class="bi bi-lightbulb-fill text-warning"></i> Cara Kerja Sinkronisasi Bot & Web
            </h6>
            <p class="text-muted small mb-3">
                Dengan sistem ini, Anda tidak perlu lagi mengubah file kode Google Apps Script secara manual saat ada perubahan HOTD atau ID Grup:
            </p>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="fw-bold text-danger small mb-1"><i class="bi bi-1-circle-fill me-1"></i> 1. Simpan di Web</div>
                        <small class="text-muted">Klik <strong>Edit Pengaturan</strong>, ubah ID Grup atau PIC HOTD, lalu klik <strong>Simpan Pengaturan</strong>.</small>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="fw-bold text-primary small mb-1"><i class="bi bi-2-circle-fill me-1"></i> 2. Ditarik via API</div>
                        <small class="text-muted">Saat reminder dijalankan, bot otomatis memanggil endpoint <code>/api/bot-config</code> untuk mengambil konfigurasi terbaru.</small>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="fw-bold text-success small mb-1"><i class="bi bi-3-circle-fill me-1"></i> 3. Terkirim Sesuai Target</div>
                        <small class="text-muted">Pesan pribadi masuk ke masing-masing Sales Agency aktif, dan pesan grup masuk ke grup HOTD beserta tag mention yang terdaftar.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT UNTUK MODE EDIT / LOCK, TOGGLE MASK, MULTI-INPUT HOTD, GENERATE TOKEN, & TES TELEGRAM AJAX --}}
<script>
// Fungsi Global Toggle Password Masking (Lihat / Sembunyikan)
function toggleMask(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('botSettingForm');
    const fields = document.querySelectorAll('.bot-field');
    const btnEditMode = document.getElementById('btnEditMode');
    const btnSaveConfig = document.getElementById('btnSaveConfig');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const btnAddHotd = document.getElementById('btnAddHotd');
    const btnGenerateToken = document.getElementById('btnGenerateToken');
    const apiTokenInput = document.getElementById('api_token');
    const container = document.getElementById('hotdListContainer');
    const modeBadge = document.getElementById('modeBadge');
    const modeBadgeIcon = document.getElementById('modeBadgeIcon');
    const modeBadgeText = document.getElementById('modeBadgeText');
    const isActiveCheckbox = document.getElementById('is_active');
    const statusSwitchBox = document.getElementById('statusSwitchBox');
    const statusSwitchText = document.getElementById('statusSwitchText');

    let isEditing = false;

    // Generator Token Acak Kuat (32 karakter: huruf besar, kecil, angka)
    function generateSecureRandomToken(length = 32) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = 'telkom_';
        const randomValues = new Uint32Array(length - 7);
        window.crypto.getRandomValues(randomValues);
        for (let i = 0; i < randomValues.length; i++) {
            result += chars[randomValues[i] % chars.length];
        }
        return result;
    }

    // Tombol Generate Token Acak
    if (btnGenerateToken && apiTokenInput) {
        btnGenerateToken.addEventListener('click', function () {
            if (!isEditing) return;
            const newToken = generateSecureRandomToken(32);
            apiTokenInput.value = newToken;
            apiTokenInput.type = 'text';
            const eyeIcon = document.getElementById('eyeIconApiToken');
            if (eyeIcon) {
                eyeIcon.className = 'bi bi-eye-slash text-muted';
            }
            showAlert('success', 'bi-shield-lock-fill', 'Token acak baru berhasil di-generate! Klik "Simpan Pengaturan" untuk menerapkannya.');
        });
    }

    // Listener interaktif saat switch dinyalakan / dimatikan
    if (isActiveCheckbox) {
        isActiveCheckbox.addEventListener('change', function () {
            if (this.checked) {
                statusSwitchBox.className = 'p-3 rounded-4 mt-3 border d-flex align-items-center justify-content-between bg-success bg-opacity-10 border-success border-opacity-25';
                statusSwitchText.className = 'small fw-semibold text-success';
                statusSwitchText.innerText = '● Bot Aktif (Reminder Berjalan)';
            } else {
                statusSwitchBox.className = 'p-3 rounded-4 mt-3 border d-flex align-items-center justify-content-between bg-danger bg-opacity-10 border-danger border-opacity-25';
                statusSwitchText.className = 'small fw-semibold text-danger';
                statusSwitchText.innerText = '○ Bot Nonaktif (Reminder Ditahan)';
            }
        });
    }

    function setEditMode(edit) {
        isEditing = edit;
        fields.forEach(field => {
            field.disabled = !edit;
            if (field.type !== 'checkbox') {
                if (edit) {
                    field.classList.remove('bg-light');
                    field.classList.add('bg-white');
                } else {
                    field.classList.remove('bg-white');
                    field.classList.add('bg-light');
                }
            }
        });

        const removeBtns = document.querySelectorAll('.btn-remove-hotd');
        removeBtns.forEach(btn => {
            if (edit) {
                btn.classList.remove('d-none');
            } else {
                btn.classList.add('d-none');
            }
        });

        if (edit) {
            btnEditMode.classList.add('d-none');
            btnSaveConfig.classList.remove('d-none');
            btnSaveConfig.classList.add('d-flex');
            btnCancelEdit.classList.remove('d-none');
            btnCancelEdit.classList.add('d-flex');
            btnAddHotd.classList.remove('d-none');
            btnAddHotd.classList.add('d-flex');
            if (btnGenerateToken) {
                btnGenerateToken.classList.remove('d-none');
            }

            modeBadge.className = 'badge bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-bold';
            modeBadgeIcon.className = 'bi bi-pencil-fill me-1';
            modeBadgeText.innerText = 'Mode Edit Aktif';
        } else {
            btnEditMode.classList.remove('d-none');
            btnSaveConfig.classList.add('d-none');
            btnSaveConfig.classList.remove('d-flex');
            btnCancelEdit.classList.add('d-none');
            btnCancelEdit.classList.remove('d-flex');
            btnAddHotd.classList.add('d-none');
            btnAddHotd.classList.remove('d-flex');
            if (btnGenerateToken) {
                btnGenerateToken.classList.add('d-none');
            }

            modeBadge.className = 'badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill fw-bold';
            modeBadgeIcon.className = 'bi bi-lock-fill me-1';
            modeBadgeText.innerText = 'Mode Terkunci';
        }
    }

    // Klik Edit Pengaturan
    if (btnEditMode) {
        btnEditMode.addEventListener('click', function () {
            setEditMode(true);
            document.getElementById('telegram_group_id').focus();
        });
    }

    // Klik Batal
    if (btnCancelEdit) {
        btnCancelEdit.addEventListener('click', function () {
            form.reset();
            setEditMode(false);
        });
    }

    // Sebelum Submit: Pastikan fields di-enable agar terkirim dalam POST request
    form.addEventListener('submit', function () {
        fields.forEach(field => field.disabled = false);
    });

    // Event Delegation untuk Hapus Baris HOTD
    container.addEventListener('click', function (e) {
        const btnRemove = e.target.closest('.btn-remove-hotd');
        if (!btnRemove || !isEditing) return;

        const rows = container.querySelectorAll('.hotd-row');
        if (rows.length > 1) {
            btnRemove.closest('.hotd-row').remove();
        } else {
            rows[0].querySelector('.hotd-input').value = '';
        }
    });

    // Tambah Baris HOTD Baru
    if (btnAddHotd) {
        btnAddHotd.addEventListener('click', function () {
            if (!isEditing) return;

            const newRow = document.createElement('div');
            newRow.className = 'input-group hotd-row';
            newRow.innerHTML = `
                <span class="input-group-text bg-white border-0 text-muted fw-bold">@</span>
                <input type="text" name="hotd_mentions[]" class="form-control bg-white border-0 py-2 hotd-input bot-field" 
                       placeholder="Username HOTD (contoh: hotdDhea)" value="">
                <button type="button" class="btn btn-light border-0 text-danger btn-remove-hotd" title="Hapus HOTD">
                    <i class="bi bi-trash-fill"></i>
                </button>
            `;
            container.appendChild(newRow);
            newRow.querySelector('.hotd-input').focus();
        });
    }

    // Ajax Test Telegram
    const btnTest = document.getElementById('btnTestTelegram');
    const btnIcon = document.getElementById('btnTestIcon');
    const btnText = document.getElementById('btnTestText');
    const alertContainer = document.getElementById('testAlertContainer');
    const alertBox = document.getElementById('testAlert');
    const alertIcon = document.getElementById('testAlertIcon');
    const alertMsg = document.getElementById('testAlertMsg');

    if (btnTest) {
        btnTest.addEventListener('click', function () {
            const token = document.getElementById('bot_token').value;
            const groupId = document.getElementById('telegram_group_id').value;

            // Kumpulkan seluruh input HOTD
            const hotdInputs = document.querySelectorAll('.hotd-input');
            const hotdList = [];
            hotdInputs.forEach(input => {
                const val = input.value.trim().replace(/^@+/, '');
                if (val) hotdList.push('@' + val);
            });
            const hotdString = hotdList.join(' ');

            if (!token || !groupId) {
                showAlert('danger', 'bi-exclamation-circle-fill', 'Token Bot dan ID Grup Telegram tidak boleh kosong.');
                return;
            }

            // Set loading state
            btnTest.disabled = true;
            btnIcon.className = 'spinner-border spinner-border-sm';
            btnText.innerText = 'Mengirim pesan uji coba...';

            fetch("{{ route('bot-settings.test-telegram') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    bot_token: token,
                    telegram_group_id: groupId,
                    hotd_mentions: hotdString
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'bi-check-circle-fill', data.message);
                } else {
                    showAlert('danger', 'bi-x-circle-fill', data.message || 'Gagal mengirim pesan test.');
                }
            })
            .catch(err => {
                showAlert('danger', 'bi-exclamation-triangle-fill', 'Terjadi kesalahan jaringan: ' + err.message);
            })
            .finally(() => {
                btnTest.disabled = false;
                btnIcon.className = 'bi bi-send-fill';
                btnText.innerText = 'Tes Kirim Pesan ke Grup Telegram';
            });
        });
    }

    function showAlert(type, iconClass, message) {
        alertContainer.style.display = 'block';
        alertBox.className = 'alert alert-' + type + ' alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4';
        alertIcon.className = 'bi ' + iconClass + ' fs-5 me-2';
        alertMsg.innerText = message;
        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});
</script>
@endsection
