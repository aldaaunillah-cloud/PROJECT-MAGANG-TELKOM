@extends('layouts.app')

@section('title', 'Status Sinkronisasi')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-clock-history me-2"></i>
                        Status Sinkronisasi
                    </h5>
                </div>
                <div class="card-body">
                    
                    {{-- ============================================ --}}
                    {{-- HASIL SYNC (dari session) --}}
                    {{-- ============================================ --}}
                    @if(session('sync_result'))
                        @php
                            $result = session('sync_result');
                            $isSuccess = isset($result['success']) && $result['success'] === true;
                        @endphp
                        
                        <div class="alert alert-{{ $isSuccess ? 'success' : 'danger' }} alert-dismissible fade show">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-{{ $isSuccess ? 'check-circle-fill' : 'x-circle-fill' }} fs-3 me-3 text-{{ $isSuccess ? 'success' : 'danger' }}"></i>
                                <div>
                                    <h5 class="alert-heading">{{ $result['message'] ?? 'Proses selesai' }}</h5>
                                    
                                    @if($isSuccess && isset($result['data']))
                                        <div class="row mt-3">
                                            <div class="col-md-3 col-6">
                                                <div class="bg-white p-2 rounded text-center">
                                                    <small class="text-muted d-block">Google Sheets</small>
                                                    <strong>{{ number_format($result['data']['google_rows'] ?? 0) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="bg-white p-2 rounded text-center">
                                                    <small class="text-muted d-block text-success">Insert</small>
                                                    <strong class="text-success">{{ number_format($result['data']['insert'] ?? 0) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="bg-white p-2 rounded text-center">
                                                    <small class="text-muted d-block text-warning">Update</small>
                                                    <strong class="text-warning">{{ number_format($result['data']['update'] ?? 0) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="bg-white p-2 rounded text-center">
                                                    <small class="text-muted d-block text-secondary">Skip</small>
                                                    <strong class="text-secondary">{{ number_format($result['data']['skip'] ?? 0) }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <hr>
                                                <div class="d-flex justify-content-between">
                                                    <span>
                                                        <small class="text-muted">Durasi:</small>
                                                        <strong>{{ $result['data']['duration'] ?? 'N/A' }}</strong>
                                                    </span>
                                                    <span>
                                                        <small class="text-muted">Total Customer:</small>
                                                        <strong class="text-primary">{{ number_format($result['data']['total_customer'] ?? 0) }}</strong>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ============================================ --}}
                    {{-- STATUS TERAKHIR --}}
                    {{-- ============================================ --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted mb-1">Total Customer</h6>
                                    <h3 class="mb-0 text-primary">{{ number_format($status['total_customer'] ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted mb-1">Last Update</h6>
                                    <h6 class="mb-0">{{ $status['last_update_human'] ?? '-' }}</h6>
                                    <small class="text-muted">{{ $status['last_update'] ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted mb-1">Last Insert</h6>
                                    <h6 class="mb-0">{{ $status['last_insert_human'] ?? '-' }}</h6>
                                    <small class="text-muted">{{ $status['last_insert'] ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted mb-1">Status</h6>
                                    @if(($status['total_customer'] ?? 0) > 0)
                                        <h5 class="mb-0 text-success">
                                            <i class="bi bi-check-circle-fill"></i> Aktif
                                        </h5>
                                    @else
                                        <h5 class="mb-0 text-warning">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Kosong
                                        </h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- TOMBOL AKSI --}}
                    {{-- ============================================ --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-grid me-2"></i> Dashboard
                        </a>
                        <a href="{{ route('sync.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-repeat me-2"></i> Sync Lagi
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-people me-2"></i> Lihat Data
                        </a>
                    </div>

                    {{-- ============================================ --}}
                    {{-- INFO --}}
                    {{-- ============================================ --}}
                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Data disinkronisasi dari Google Sheets menggunakan metode 
                            <strong>upsert</strong> (update jika ada, insert jika baru).
                            Proses menggunakan <strong>chunk 500</strong> data.
                        </small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection