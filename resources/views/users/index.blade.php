@extends('layouts.app')

@section('title', 'Manajemen Anggota & Agensi')

@section('content')
<div class="container-fluid px-0">

    {{-- Alert Error Validasi (Jika Ada) --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-1"></i> Terdapat kesalahan pada input Anda:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- STATS CARDS RINGKASAN --}}
    {{-- ============================================ --}}
    <div class="row g-3 mb-4">
        {{-- Total Anggota --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Pengguna</div>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalUsers) }}</h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people-fill text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total PIKOL (Admin) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Role PIKOL (Admin)</div>
                        <h3 class="fw-bold text-danger mb-0 mt-1">{{ number_format($totalPikol) }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-lock-fill text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total HOTD (Member) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Role HOTD (Member)</div>
                        <h3 class="fw-bold text-info mb-0 mt-1">{{ number_format($totalHotd) }}</h3>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-badge-fill text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Aktif & Nonaktif --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Status Akun</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $totalAktif }} Aktif</span>
                            @if($totalNonaktif > 0)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold">{{ $totalNonaktif }} Nonaktif</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABEL ANGGOTA & FILTER --}}
    {{-- ============================================ --}}
    <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom border-light p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Daftar Anggota & Agensi Sistem</h5>
                    <p class="text-muted small mb-0">Kelola akun, kode agensi, integrasi Telegram ID, serta status keaktifan anggota</p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    {{-- Tombol Tambah Anggota --}}
                    <button type="button" class="btn btn-danger rounded-3 shadow-sm d-flex align-items-center gap-2 px-3 py-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Tambah Anggota Baru</span>
                    </button>
                </div>
            </div>

            {{-- Filter & Search Form --}}
            <form method="GET" action="{{ route('users.index') }}" class="mt-3 pt-3 border-top border-light">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari nama, kode, email, username, atau telegram id..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="role" class="form-select bg-light border-0" onchange="this.form.submit()">
                            <option value="all">Semua Role</option>
                            <option value="pikol" {{ request('role') == 'pikol' ? 'selected' : '' }}>PIKOL (Admin)</option>
                            <option value="hotd" {{ request('role') == 'hotd' ? 'selected' : '' }}>HOTD (Member)</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="status" class="form-select bg-light border-0" onchange="this.form.submit()">
                            <option value="all">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-1">
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('users.index') }}" class="btn btn-light border w-100" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 5%;">NO</th>
                            <th style="width: 12%;">KODE</th>
                            <th style="width: 25%;">NAMA & USERNAME</th>
                            <th style="width: 26%;">EMAIL & TELEGRAM ID</th>
                            <th style="width: 12%; text-align: center;">ROLE</th>
                            <th style="width: 10%; text-align: center;">STATUS</th>
                            <th class="pe-4 text-center" style="width: 10%;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="ps-4 fw-bold text-muted" style="font-size: 0.85rem;">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td>
                                    @if($user->kode)
                                        <span class="badge bg-light text-dark border font-monospace fw-bold px-2 py-1" style="font-size: 0.78rem;">
                                            {{ $user->kode }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                            {{ $user->name }}
                                            @if($user->id === Auth::id())
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 0.62rem;">Anda</span>
                                            @endif
                                        </div>
                                        <small class="text-muted font-monospace" style="font-size: 0.75rem;">{{ $user->username ?: strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $user->email)[0])) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold text-truncate" style="font-size: 0.84rem;">{{ $user->email }}</div>
                                    <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-telegram text-primary"></i>
                                        <span>{{ $user->telegram_id ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($user->isPikol())
                                        <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 shadow-sm" style="font-size: 0.72rem;">
                                            <i class="bi bi-shield-check me-1"></i> PIKOL
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                            <i class="bi bi-person me-1"></i> HOTD
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($user->isActive())
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            <i class="bi bi-dash-circle-fill me-1"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        {{-- Tombol Toggle Status Aktif / Tidak Aktif --}}
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('users.toggle-status', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-light {{ $user->isActive() ? 'text-warning' : 'text-success' }} border-0 rounded-3 px-2 py-1 shadow-sm"
                                                        title="{{ $user->isActive() ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                    <i class="bi {{ $user->isActive() ? 'bi-toggle-on fs-5' : 'bi-toggle-off fs-5' }}"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Tombol Edit --}}
                                        <button type="button" class="btn btn-sm btn-light text-primary border-0 rounded-3 px-2 py-1 shadow-sm" 
                                                data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Edit Data Anggota">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        @if($user->id !== Auth::id())
                                            <button type="button" class="btn btn-sm btn-light text-danger border-0 rounded-3 px-2 py-1 shadow-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}" title="Hapus Anggota">
                                                <i class="bi bi-trash-fill fs-6"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-people fs-1 d-block text-secondary mb-3"></i>
                                    Tidak ada data anggota ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-white border-top border-light p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $users->firstItem() }} s/d {{ $users->lastItem() }} dari total {{ $users->total() }} anggota
                </small>
                <div>
                    {{ $users->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- ======================================================== --}}
{{-- SEMUA MODALS DILETAKKAN DI LUAR TABEL (MENCEGAH KEDAP-KEDIP) --}}
{{-- ======================================================== --}}

{{-- MODAL TAMBAH ANGGOTA BARU --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark" id="addUserModalLabel">
                    <i class="bi bi-person-plus-fill text-danger me-1"></i> Tambah Anggota / Agensi Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Masukkan nama lengkap anggota" value="{{ old('name') }}" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Kode Agensi</label>
                            <input type="text" name="kode" class="form-control rounded-3" placeholder="Contoh: MN02817 / MB00454" value="{{ old('kode') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Username</label>
                            <input type="text" name="username" class="form-control rounded-3" placeholder="Contoh: agnes.prawesti" value="{{ old('username') }}">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" placeholder="contoh: nama@gmail.com" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Telegram ID</label>
                            <input type="text" name="telegram_id" class="form-control rounded-3" placeholder="Contoh: 114341071" value="{{ old('telegram_id') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Role Pengguna <span class="text-danger">*</span></label>
                            <select name="role" class="form-select rounded-3" required>
                                <option value="hotd" {{ old('role') == 'hotd' ? 'selected' : '' }}>HOTD (Member)</option>
                                <option value="pikol" {{ old('role') == 'pikol' ? 'selected' : '' }}>PIKOL (Admin / Sinkronisasi)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Status Awal <span class="text-danger">*</span></label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Divisi (Opsional)</label>
                            <input type="text" name="divisi" class="form-control rounded-3" placeholder="Misal: HOTD / Collection" value="{{ old('divisi') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Witel (Opsional)</label>
                            <input type="text" name="witel" class="form-control rounded-3" placeholder="Misal: Priangan Timur" value="{{ old('witel', 'Witel Priangan Timur') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">Daftarkan Anggota</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT & DELETE ANGGOTA (DI-LOOP DI LUAR TABEL) --}}
@foreach($users as $user)
    {{-- MODAL EDIT ANGGOTA --}}
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-pencil-square text-primary me-1"></i> Edit Data Anggota
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Kode Agensi</label>
                                <input type="text" name="kode" class="form-control rounded-3" value="{{ old('kode', $user->kode) }}" placeholder="Contoh: MN02817">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Username</label>
                                <input type="text" name="username" class="form-control rounded-3" value="{{ old('username', $user->username) }}" placeholder="Contoh: agnes.prawesti">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control rounded-3" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Telegram ID</label>
                                <input type="text" name="telegram_id" class="form-control rounded-3" value="{{ old('telegram_id', $user->telegram_id) }}" placeholder="Contoh: 114341071">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Role Pengguna <span class="text-danger">*</span></label>
                                <select name="role" class="form-select rounded-3" required>
                                    <option value="pikol" {{ old('role', $user->role) == 'pikol' || $user->isPikol() ? 'selected' : '' }}>PIKOL (Admin)</option>
                                    <option value="hotd" {{ old('role', $user->role) == 'hotd' && !$user->isPikol() ? 'selected' : '' }}>HOTD (Member)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Status Akun <span class="text-danger">*</span></label>
                                <select name="status" class="form-select rounded-3" required>
                                    <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="tidak_aktif" {{ old('status', $user->status) == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Divisi</label>
                                <input type="text" name="divisi" class="form-control rounded-3" value="{{ old('divisi', $user->divisi) }}" placeholder="Contoh: PIKOL / HOTD">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-muted">Witel</label>
                                <input type="text" name="witel" class="form-control rounded-3" value="{{ old('witel', $user->witel) }}" placeholder="Contoh: Priangan Timur">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    @if($user->id !== Auth::id())
        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-trash-fill text-danger fs-2"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Hapus Anggota Ini?</h6>
                        <p class="text-muted small mb-4">Akun <strong>{{ $user->name }}</strong> ({{ $user->email }}) akan dihapus secara permanen dari sistem.</p>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light w-50 rounded-3" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="w-50">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100 rounded-3">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
