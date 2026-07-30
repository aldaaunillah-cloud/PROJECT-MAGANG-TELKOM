<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - Telkom Customer Management</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    {{-- Font Awesome (untuk kompatibilitas) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Custom CSS --}}
    <style>
        :root {
            --primary-color: #E2001A;
            --secondary-color: #2F3A4A;
            --bg-color: #F8F9FC;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ============================================
           SIDEBAR - TELKOM THEME
           ============================================ */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, #b30014 100%);
            padding-top: 20px;
            position: sticky;
            top: 0;
            overflow-y: auto;
            width: 260px;
                        flex-shrink: 0;
            z-index: 100;
        }

        .sidebar .sidebar-brand {
            padding: 10px 20px 30px;
            text-align: center;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            margin-bottom: 20px;
        }

        .sidebar .sidebar-brand .brand-icon {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 5px;
        }

        .sidebar .sidebar-brand h4 {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
        }

        .sidebar .sidebar-brand small {
            opacity: 0.8;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 3px 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
            position: relative;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: #fff;
            border-radius: 0 4px 4px 0;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            width: 22px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar .nav-link .badge {
            float: right;
            margin-top: 2px;
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .sidebar hr {
            border-color: rgba(255,255,255,0.1);
            margin: 15px 20px;
        }

        .sidebar .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: auto;
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
        }

        .sidebar .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .sidebar .sidebar-footer .user-info .avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .sidebar .sidebar-footer .user-info .user-name {
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .main-content {
            padding: 30px;
            min-height: 100vh;
            flex: 1;
        }

        /* ============================================
           CARDS - Modern
           ============================================ */
        .card {
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            padding: 16px 20px;
        }

        .card-body {
            padding: 20px;
        }

        /* ============================================
           TABLES
           ============================================ */
        .table thead th {
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 12px 12px;
            background-color: #f8f9fa;
        }

        .table tbody td {
            padding: 12px 12px;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(226, 0, 26, 0.04);
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,0.02);
        }

        /* ============================================
           BADGES
           ============================================ */
        .badge {
            font-weight: 500;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }

        .badge-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .badge-secondary {
            background-color: var(--secondary-color);
            color: #fff;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 8px 22px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #b30014;
            border-color: #b30014;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(226, 0, 26, 0.3);
        }

        .btn-sm {
            border-radius: 8px;
            padding: 5px 14px;
            font-size: 0.8rem;
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #fff;
        }

        /* ============================================
           PROGRESS
           ============================================ */
        .progress {
            border-radius: 10px;
            background-color: #f0f0f0;
            height: 6px;
        }

        .progress-bar {
            border-radius: 10px;
            background-color: var(--primary-color);
        }

        .progress-bar-success {
            background-color: #28a745;
        }

        /* ============================================
           FORM
           ============================================ */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e8e8e8;
            padding: 8px 16px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(226, 0, 26, 0.15);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }

        /* ============================================
           LOADING SPINNER
           ============================================ */
        .spinner-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .spinner-overlay.show {
            display: flex;
        }

        .spinner-overlay .spinner-border {
            width: 50px;
            height: 50px;
            color: var(--primary-color);
        }

        .spinner-overlay .spinner-text {
            margin-top: 15px;
            color: var(--secondary-color);
            font-weight: 600;
        }

        /* ============================================
           LOGIN PAGE
           ============================================ */
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color) 0%, #b30014 100%);
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            background: #fff;
        }
        .login-card .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card .login-logo h3 {
            color: var(--primary-color);
            font-weight: 700;
        }
        .login-card .login-logo p {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* ============================================
           EMPTY STATE
           ============================================ */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state .empty-icon {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 20px;
        }
        .empty-state h5 {
            color: var(--secondary-color);
            font-weight: 600;
        }
        .empty-state p {
            color: #9ca3af;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                position: relative;
                width: 100%;
                padding-bottom: 10px;
            }
            .main-content {
                padding: 15px;
            }
            .card-body {
                padding: 15px;
            }
            .login-card {
                padding: 25px;
                margin: 15px;
            }
        }

        @media (max-width: 576px) {
            .sidebar .sidebar-brand h4 {
                font-size: 1.1rem;
            }
            .sidebar .nav-link {
                font-size: 0.8rem;
                padding: 10px 15px;
                margin: 2px 8px;
            }
            .main-content {
                padding: 10px;
            }
        }

        /* ============================================
           SCROLLBAR
           ============================================ */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* ============================================
           CHART CONTAINER
           ============================================ */
        .chart-container {
            position: relative;
            width: 100%;
        }

        /* ============================================
           PRINT
           ============================================ */
        @media print {
            .sidebar {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 20px !important;
            }
            .btn, .no-print {
                display: none !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            .table {
                font-size: 10px !important;
            }
        }

        /* ============================================
           UTILITY CLASSES
           ============================================ */
        .text-primary-custom {
            color: var(--primary-color) !important;
        }
        .bg-primary-custom {
            background-color: var(--primary-color) !important;
        }
        .bg-primary-custom-light {
            background-color: rgba(226, 0, 26, 0.08) !important;
        }
        .border-primary-custom {
            border-color: var(--primary-color) !important;
        }
        .shadow-hover {
            transition: box-shadow 0.3s ease;
        }
        .shadow-hover:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
        }
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>

    @stack('styles')
</head>
<body>
    {{-- Loading Spinner Global --}}
    <div class="spinner-overlay" id="globalLoader">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-text">Memuat data...</div>
    </div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            {{-- ============================================ --}}
            {{-- SIDEBAR --}}
            {{-- ============================================ --}}
            <div class="sidebar d-flex flex-column">
                <div class="sidebar-brand">
                    <span class="brand-icon">
                        <i class="bi bi-building"></i>
                    </span>
                    <h4>Telkom</h4>
                    <small>Customer Management</small>
                </div>

                <nav class="nav flex-column">
                    {{-- DASHBOARD --}}
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        Dashboard
                    </a>

                    {{-- REKAP AGENCY BILLING 1-2 --}}
                    <a href="{{ route('rekap.agency') }}" class="nav-link {{ request()->routeIs('rekap.agency') ? 'active' : '' }}">
                        <i class="bi bi-building-fill"></i>
                        Rekap Agency Billing 1-2
                    </a>

                    {{-- RIWAYAT REMINDER --}}
                    <a href="{{ route('reminders.index') }}" class="nav-link {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        Riwayat Reminder
                    </a>

                    <hr>

                    <hr>

                    {{-- HAK AKSES (HANYA ADMIN) - SEMENTARA DINONAKTIFKAN --}}
                    {{-- 
                    @if(auth()->user() && auth()->user()->role === 'admin')
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock-fill"></i>
                        Hak Akses
                    </a>
                    @endif
                    --}}

                    {{-- SINKRONISASI --}}
                    <a href="{{ route('sync.index') }}" class="nav-link {{ request()->routeIs('sync.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-repeat"></i>
                        Sinkronisasi
                    </a>
                </nav>

                {{-- FOOTER SIDEBAR --}}
                <div class="sidebar-footer mt-auto">
                    <div class="user-info">
                        <div class="avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <small style="opacity: 0.7; font-size: 0.7rem;">
                                <i class="bi bi-shield-check me-1"></i>
                                {{ Auth::user()->role ?? 'Supervisor' }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small>
                            <i class="bi bi-database me-1"></i>
                            {{ number_format($totalCustomer ?? 0) }} Customer
                        </small>
                        <small>
                            <i class="bi bi-clock me-1"></i>
                            {{ now()->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <hr style="border-color: rgba(255,255,255,0.1); margin: 10px 0;">
                    <a href="#" class="text-white text-decoration-none" style="opacity: 0.8; font-size: 0.8rem;" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- MAIN CONTENT --}}
            {{-- ============================================ --}}
            <div class="main-content">
                {{-- Top Bar --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary-custom">
                            @yield('title', 'Dashboard')
                        </h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    @yield('title', 'Dashboard')
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->name ?? 'Admin' }}
                        </span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.location.reload()" title="Refresh Halaman">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('sync_result'))
                    @php $result = session('sync_result'); @endphp
                    <div class="alert alert-{{ $result['success'] ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                        <i class="bi bi-{{ $result['success'] ? 'check-circle-fill' : 'exclamation-triangle-fill' }} me-2"></i>
                        {{ $result['message'] }}
                        @if($result['success'] && isset($result['data']))
                            <br>
                            <small>
                                Google Sheets: {{ $result['data']['google_rows'] }} | 
                                Insert: {{ $result['data']['insert'] }} | 
                                Update: {{ $result['data']['update'] }} | 
                                Skip: {{ $result['data']['skip'] }} | 
                                Durasi: {{ $result['data']['duration'] }}
                            </small>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Content --}}
                @yield('content')
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================ --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = bootstrap.Alert.getInstance(alert);
                    if (bsAlert) {
                        bsAlert.close();
                    } else {
                        // Fallback
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            alert.remove();
                        }, 500);
                    }
                }, 5000);
            });

            // Tooltip activation
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Popover activation
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Auto submit form on select change
            document.querySelectorAll('select[data-auto-submit="true"]').forEach(function(select) {
                select.addEventListener('change', function() {
                    this.closest('form').submit();
                });
            });
        });

        // Show global loader
        function showLoader(message = 'Memuat data...') {
            const loader = document.getElementById('globalLoader');
            const text = loader.querySelector('.spinner-text');
            if (text) text.textContent = message;
            loader.classList.add('show');
        }

        // Hide global loader
        function hideLoader() {
            document.getElementById('globalLoader').classList.remove('show');
        }

        // Confirm delete
        function confirmDelete(message) {
            return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
        }

        // Export functions
        function exportExcel(url, params = {}) {
            showLoader('Menyiapkan file Excel...');
            const queryString = new URLSearchParams(params).toString();
            const fullUrl = url + (queryString ? '?' + queryString : '');
            window.location.href = fullUrl;
            setTimeout(hideLoader, 3000);
        }

        function exportPDF(url, params = {}) {
            showLoader('Menyiapkan file PDF...');
            const queryString = new URLSearchParams(params).toString();
            const fullUrl = url + (queryString ? '?' + queryString : '');
            window.location.href = fullUrl;
            setTimeout(hideLoader, 3000);
        }

        // Sync function
        function syncData(url) {
            if (!confirm('Apakah Anda yakin ingin melakukan sinkronisasi data dari Google Sheets?')) {
                return;
            }
            showLoader('Sinkronisasi data sedang berjalan...');
            window.location.href = url;
        }

        // Number format
        function numberFormat(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Currency format
        function currencyFormat(num) {
            return 'Rp ' + numberFormat(num);
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                // Create container if not exists
                const container = document.createElement('div');
                container.id = 'toast-container';
                container.style.position = 'fixed';
                container.style.bottom = '20px';
                container.style.right = '20px';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
            toast.role = 'alert';
            toast.ariaLive = 'assertive';
            toast.ariaAtomic = 'true';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(function() {
                toast.classList.remove('show');
                setTimeout(function() {
                    toast.remove();
                }, 500);
            }, 4000);
        }
    </script>

    @stack('scripts')
</body>
</html>