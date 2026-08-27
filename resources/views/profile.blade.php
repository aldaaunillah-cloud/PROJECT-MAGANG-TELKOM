@extends('layouts.app')

@section('title', 'PROFIL ADMIN')

@section('content')
<style>
    .shadow-sm-hover {
        transition: all 0.2s ease-in-out;
    }
    .shadow-sm-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,3,97,0.06) !important;
    }
    .custom-input-group {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
    }
    .custom-input-group:focus-within {
        border-color: #000361 !important;
        box-shadow: 0 0 0 3px rgba(0,3,97,0.15) !important;
    }
    .btn-link:focus, .btn-link:active {
        outline: none !important;
        box-shadow: none !important;
    }
    @media (min-width: 768px) {
        .profile-divider {
            border-right: 1px solid #f1f5f9 !important;
        }
    }
</style>

<div class="container-fluid p-0">
    <!-- Header Page Description -->
    <div class="mb-4" style="margin-top: -15px;">
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Informasi profil dan pengaturan akun admin</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4 p-3 mx-auto" role="alert" style="background-color: #e6f4ea; color: #137333; max-width: 1000px;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4 p-3 mx-auto" role="alert" style="background-color: #fce8e6; color: #c5221f; max-width: 1000px;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card border-0 shadow-sm overflow-hidden mx-auto" style="border-radius: 20px; border: 1px solid #e2e8f0 !important; max-width: 1000px; background-color: #ffffff;">
        <div class="card-body p-4 p-md-5">
            <div class="row g-4 g-lg-5">
                <!-- Left Column (Avatar & Quick Info) -->
                <div class="col-md-4 text-center profile-divider pe-md-4">
                    <div class="d-flex flex-column align-items-center py-2">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm overflow-hidden border border-4 border-white" 
                                 style="width: 160px; height: 160px; background-color: #e6efff; box-shadow: 0 8px 24px rgba(0,3,97,0.08) !important;">
                                @if($user->profile_photo_path && file_exists(public_path($user->profile_photo_path)))
                                    <img src="{{ asset($user->profile_photo_path) }}?v={{ time() }}" class="w-100 h-100" style="object-fit: cover;" alt="Foto Profil">
                                @else
                                    <i class="bi bi-person-fill" style="font-size: 85px; color: #000361;"></i>
                                @endif
                            </div>
                            <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" title="Online" style="width: 18px; height: 18px; margin-bottom: 8px; margin-right: 8px;"></span>
                        </div>
                        <h4 class="fw-bold mb-1" style="color: #000361; font-size: 1.35rem;">{{ $user->name }}</h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 fw-semibold mb-3" style="font-size: 0.8rem; border-radius: 20px;">
                            <i class="bi bi-shield-check me-1"></i> Administrator
                        </span>
                        <div class="w-100 text-start bg-light p-3 rounded-4 mt-2" style="font-size: 0.8rem; border: 1px dashed #cbd5e1;">
                            <div class="text-secondary fw-semibold mb-1">Catatan Akun:</div>
                            <div class="text-muted">Gunakan hak akses administrator ini dengan bijak untuk mengelola reminder tagihan pelanggan.</div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Details & Forms) -->
                <div class="col-md-8 ps-md-4">
                    <!-- Section 1: Informasi Akun -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-light-subtle">
                            <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: #000361; letter-spacing: 0.5px;">
                                <i class="bi bi-person-badge-fill"></i> Informasi Akun
                            </h5>
                            <button type="button" class="btn btn-sm border-0 rounded-circle p-2 d-flex align-items-center justify-content-center" 
                                    id="btnEditProfile" onclick="toggleEditMode(true)" title="Edit Profil" style="width: 36px; height: 36px; background-color: #f1f5f9; color: #000361;">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>

                        <!-- VIEW MODE -->
                        <div id="profileView" class="ps-1">
                            <div class="row g-3">
                                {{-- Nama Lengkap --}}
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 border border-light-subtle bg-light h-100 shadow-sm-hover" style="background-color: #f8fafc !important;">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                            <i class="bi bi-person text-primary"></i> Nama Lengkap
                                        </div>
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $user->name }}</div>
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 border border-light-subtle bg-light h-100 shadow-sm-hover" style="background-color: #f8fafc !important;">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                            <i class="bi bi-envelope text-primary"></i> Email Address
                                        </div>
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $user->email }}</div>
                                    </div>
                                </div>

                                {{-- Divisi --}}
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 border border-light-subtle bg-light h-100 shadow-sm-hover" style="background-color: #f8fafc !important;">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                            <i class="bi bi-briefcase text-primary"></i> Divisi
                                        </div>
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $user->divisi ?? 'Business Service' }}</div>
                                    </div>
                                </div>

                                {{-- Witel --}}
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 border border-light-subtle bg-light h-100 shadow-sm-hover" style="background-color: #f8fafc !important;">
                                        <div class="d-flex align-items-center gap-2 text-muted mb-2" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                                            <i class="bi bi-geo-alt text-primary"></i> Witel
                                        </div>
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $user->witel ?? 'Telkom Cirebon' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EDIT MODE (FORM) -->
                        <form action="{{ route('profile.update') }}" method="POST" id="formEditProfile" 
                              class="@if($errors->has('name') || $errors->has('email') || $errors->has('profile_photo')) d-block @else d-none @endif ps-1" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">Foto Profil</label>
                                    <input type="file" name="profile_photo" class="form-control rounded-3 @error('profile_photo') is-invalid @enderror" accept="image/*" style="border-color: #cbd5e1; font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format: JPG, JPEG, PNG, GIF (Maks. 2MB)</small>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" required style="border-color: #cbd5e1; font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">Email Address</label>
                                    <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" required style="border-color: #cbd5e1; font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">Divisi</label>
                                    <input type="text" name="divisi" class="form-control rounded-3 @error('divisi') is-invalid @enderror" 
                                           value="{{ old('divisi', $user->divisi ?? 'Business Service') }}" style="border-color: #cbd5e1; font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                    @error('divisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">Witel</label>
                                    <input type="text" name="witel" class="form-control rounded-3 @error('witel') is-invalid @enderror" 
                                           value="{{ old('witel', $user->witel ?? 'Telkom Cirebon') }}" style="border-color: #cbd5e1; font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                    @error('witel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn text-white btn-sm px-4 py-2" style="background-color: #000361; border-radius: 8px;">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-4 py-2" style="border-radius: 8px;" onclick="toggleEditMode(false)">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Section 2: Ubah Password -->
                    <div>
                        <div class="mb-4 pb-2 border-bottom border-light-subtle">
                            <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: #000361; letter-spacing: 0.5px;">
                                <i class="bi bi-shield-lock-fill"></i> Ubah Password
                            </h5>
                        </div>

                        <form action="{{ route('profile.password') }}" method="POST" class="ps-1">
                            @csrf
                            
                            <div class="row g-3">
                                <!-- Password Saat Ini -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.85rem;">Password Saat Ini <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group @error('current_password') border-danger @enderror">
                                        <span class="input-group-text bg-transparent border-0 px-3" style="color: #64748b;">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" name="current_password" id="current_password" class="form-control bg-transparent border-0 py-2.5" 
                                               placeholder="Masukkan password saat ini" style="font-size: 0.9rem;" required>
                                        <button class="btn btn-link text-secondary border-0 pe-3" type="button" onclick="togglePasswordVisibility('current_password')">
                                            <i class="bi bi-eye-slash-fill" id="current_password_icon"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <small class="text-danger d-block mt-1 ms-1" style="font-size: 0.75rem;">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Keterangan Password Saat Ini -->
                                <div class="col-sm-6 d-flex align-items-end pb-2">
                                    <small class="text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                                        <i class="bi bi-info-circle-fill text-primary me-1"></i> Masukkan password Anda saat ini untuk memverifikasi identitas Anda sebelum mengubahnya.
                                    </small>
                                </div>

                                <!-- Password Baru -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.85rem;">Password Baru <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group @error('password') border-danger @enderror">
                                        <span class="input-group-text bg-transparent border-0 px-3" style="color: #64748b;">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" name="password" id="password" class="form-control bg-transparent border-0 py-2.5" 
                                               placeholder="Masukkan password baru" style="font-size: 0.9rem;" required>
                                        <button class="btn btn-link text-secondary border-0 pe-3" type="button" onclick="togglePasswordVisibility('password')">
                                            <i class="bi bi-eye-slash-fill" id="password_icon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <small class="text-danger d-block mt-1 ms-1" style="font-size: 0.75rem;">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Konfirmasi Password Baru -->
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 0.85rem;">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group @error('password_confirmation') border-danger @enderror">
                                        <span class="input-group-text bg-transparent border-0 px-3" style="color: #64748b;">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control bg-transparent border-0 py-2.5" 
                                               placeholder="Konfirmasi password baru" style="font-size: 0.9rem;" required>
                                        <button class="btn btn-link text-secondary border-0 pe-3" type="button" onclick="togglePasswordVisibility('password_confirmation')">
                                            <i class="bi bi-eye-slash-fill" id="password_confirmation_icon"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <small class="text-danger d-block mt-1 ms-1" style="font-size: 0.75rem;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="mt-4">
                                <button type="submit" class="btn text-white px-4 py-2.5 d-flex align-items-center gap-2 shadow-sm" 
                                        style="background-color: #000361; border-radius: 8px; transition: all 0.25s;"
                                        onmouseover="this.style.backgroundColor='#00024a'"
                                        onmouseout="this.style.backgroundColor='#000361'">
                                    <i class="bi bi-shield-lock-fill"></i> Simpan Password Baru
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleEditMode(edit) {
        const viewDiv = document.getElementById('profileView');
        const editForm = document.getElementById('formEditProfile');
        const btnEdit = document.getElementById('btnEditProfile');

        if (edit) {
            viewDiv.classList.add('d-none');
            editForm.classList.remove('d-none');
            btnEdit.classList.add('d-none');
        } else {
            viewDiv.classList.remove('d-none');
            editForm.classList.add('d-none');
            btnEdit.classList.remove('d-none');
        }
    }

    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        }
    }
</script>
@endpush
