@extends('layouts.app')

@section('title', 'PROFIL ADMIN')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Page Description (Title is handled by main layout topbar) -->
    <div class="mb-4" style="margin-top: -15px;">
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Informasi profil dan pengaturan akun admin</p>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px; border: 1px solid #e2e8f0 !important; max-width: 1000px;">
        <div class="card-body p-5">
            <div class="row g-5">
                <!-- Left Column (Avatar) -->
                <div class="col-md-4 text-center border-end-md" style="border-color: #f1f5f9;">
                    <div class="d-flex flex-column align-items-center">
                        <!-- Silhouette Avatar Circle or User Uploaded Photo -->
                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm overflow-hidden" 
                             style="width: 150px; height: 150px; background-color: #dbeafe;">
                            @if($user->profile_photo_path && file_exists(public_path($user->profile_photo_path)))
                                <img src="{{ asset($user->profile_photo_path) }}?v={{ time() }}" class="w-100 h-100" style="object-fit: cover;" alt="Foto Profil">
                            @else
                                <i class="bi bi-person-fill" style="font-size: 90px; color: #3b82f6;"></i>
                            @endif
                        </div>
                        <h4 class="fw-bold" style="color: #000361;">Administrator</h4>
                    </div>
                </div>

                <!-- Right Column (Details & Form) -->
                <div class="col-md-8">
                    <!-- Informasi Akun -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4">
                            <h5 class="fw-bold m-0" style="color: #000361; letter-spacing: 0.5px;">Informasi Akun</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 border-0 px-2 py-1" id="btnEditProfile" onclick="toggleEditMode(true)" title="Edit Profil">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </button>
                        </div>
                        
                        <!-- VIEW MODE -->
                        <div id="profileView" class="ps-2">
                            <div class="row mb-3 align-items-center">
                                <div class="col-sm-4 text-muted" style="font-size: 0.95rem;">Nama Lengkap</div>
                                <div class="col-sm-8 fw-semibold" style="color: #1e293b; font-size: 0.95rem;">
                                    : {{ $user->name }}
                                </div>
                            </div>
                            
                            <div class="row mb-3 align-items-center">
                                <div class="col-sm-4 text-muted" style="font-size: 0.95rem;">Divisi</div>
                                <div class="col-sm-8 fw-semibold" style="color: #1e293b; font-size: 0.95rem;">
                                    : {{ $user->divisi ?? 'Business Service' }}
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <div class="col-sm-4 text-muted" style="font-size: 0.95rem;">Witel</div>
                                <div class="col-sm-8 fw-semibold" style="color: #1e293b; font-size: 0.95rem;">
                                    : {{ $user->witel ?? 'Telkom Cirebon' }}
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <div class="col-sm-4 text-muted" style="font-size: 0.95rem;">Email</div>
                                <div class="col-sm-8 fw-semibold" style="color: #1e293b; font-size: 0.95rem;">
                                    : {{ $user->email }}
                                </div>
                            </div>
                        </div>

                        <!-- EDIT MODE (FORM) -->
                        <form action="{{ route('profile.update') }}" method="POST" id="formEditProfile" class="d-none ps-2" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">Foto Profil</label>
                                <input type="file" name="profile_photo" class="form-control" accept="image/*">
                                <small class="text-muted d-block mt-1">Format: JPG, JPEG, PNG, GIF (Maks. 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">Divisi</label>
                                <input type="text" name="divisi" class="form-control" value="{{ $user->divisi ?? 'Business Service' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">Witel</label>
                                <input type="text" name="witel" class="form-control" value="{{ $user->witel ?? 'Telkom Cirebon' }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted mb-1" style="font-size: 0.85rem;">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn text-white btn-sm px-4 py-2" style="background-color: #000361; border-radius: 8px;">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-4 py-2 ms-2" style="border-radius: 8px;" onclick="toggleEditMode(false)">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Form Ubah Password -->
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        
                        <h5 class="fw-bold mb-3" style="color: #000361; letter-spacing: 0.5px;">Ubah Password</h5>
                        
                        <!-- Current Password -->
                        <div class="mb-4">
                            <div class="input-group" style="border-radius: 10px; overflow: hidden; background-color: #e2e8f0; border: 1px solid #cbd5e1;">
                                <span class="input-group-text bg-transparent border-0 px-3" style="color: #475569;">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" name="current_password" class="form-control bg-transparent border-0 py-2.5" 
                                       placeholder="Masukkan password saat ini" style="font-size: 0.9rem;" required>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2" style="color: #000361; font-size: 0.9rem;">Password Baru</label>
                            <div class="input-group" style="border-radius: 10px; overflow: hidden; background-color: #e2e8f0; border: 1px solid #cbd5e1;">
                                <span class="input-group-text bg-transparent border-0 px-3" style="color: #475569;">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 py-2.5" 
                                       placeholder="Masukkan password baru" style="font-size: 0.9rem;" required>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2" style="color: #000361; font-size: 0.9rem;">Konfirmasi Password Baru</label>
                            <div class="input-group" style="border-radius: 10px; overflow: hidden; background-color: #e2e8f0; border: 1px solid #cbd5e1;">
                                <span class="input-group-text bg-transparent border-0 px-3" style="color: #475569;">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" name="password_confirmation" class="form-control bg-transparent border-0 py-2.5" 
                                       placeholder="Konfirmasi password baru" style="font-size: 0.9rem;" required>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="mt-4 clearfix">
                            <button type="submit" class="btn text-white px-4 py-2.5 d-flex align-items-center gap-2 float-end shadow-sm" 
                                    style="background-color: #000361; border-radius: 8px; transition: all 0.25s;"
                                    onmouseover="this.style.backgroundColor='#00024a'"
                                    onmouseout="this.style.backgroundColor='#000361'">
                                <i class="bi bi-lock-fill"></i> Simpan Password
                            </button>
                        </div>
                    </form>
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
</script>
@endpush
