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
            --primary-color: #000361;
            --secondary-color: #2F3A4A;
            --bg-color: #F8F9FC;
            --telkom-red: #E2001A;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        .main-content {
            padding: 30px;
            min-height: 100vh;
            margin-left: 260px;
            width: calc(100% - 260px);
            flex: none;
            background-color: var(--bg-color);
        }

        /* ============================================
           CARDS - Premium & Modern
           ============================================ */
        .card {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.01);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02) !important;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
        }

        .card-body {
            padding: 20px;
        }

        /* ============================================
           TABLES
           ============================================ */
        .table thead th {
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            color: #475569;
            padding: 10px 12px;
            background-color: #f8fafc;
        }

        .table tbody td {
            padding: 10px 12px;
            vertical-align: middle;
            color: #334155;
            border-color: #f1f5f9;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(226, 0, 26, 0.03);
            transition: background-color 0.2s ease;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fafafb;
        }

        /* ============================================
           BADGES
           ============================================ */
        .badge {
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.72rem;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .badge-warning {
            background-color: #fef9c3;
            color: #a16207;
        }

        .badge-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge-primary {
            background-color: #e0e7ff;
            color: #4338ca;
        }

        .badge-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 18px;
            transition: all 0.2s ease;
            font-size: 0.85rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #00024a;
            border-color: #00024a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 3, 97, 0.2);
        }

        .btn-sm {
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 0.78rem;
        }

        /* ============================================
           SIDEBAR LINKS & HOVER
           ============================================ */
        .hover-bg-light {
            transition: all 0.2s ease;
        }
        
        .hover-bg-light:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #fff !important;
            transform: translateX(3px);
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
        SIDEBAR FIXED
        ============================================ */
        .sidebar {
            width: 260px;
            height: 100vh;
            max-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow: hidden;
            flex-shrink: 0;
            background-color: rgb(4, 4, 63);
            z-index: 1000;
        }

        .sidebar nav {
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
        }

        .sidebar nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 10px;
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 768px) {
        .sidebar {
            height: auto;
            max-height: none;
            min-height: auto;
            position: relative;
            width: 100%;
            padding-bottom: 10px;
        }

        .sidebar nav {
            overflow-y: visible;
        }

        .main-content {
            margin-left: 0;
            width: 100%;
            flex: 1;
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
            {{-- SIDEBAR --}}
            {{-- ============================================ --}}
            <div class="sidebar d-flex flex-column flex-shrink-0 text-white shadow">
                <div class="p-3 text-center border-bottom border-light border-opacity-25 mb-3">

            {{-- Logo --}}
                    <img src="{{ asset('image/logo.png') }}"
                        alt="Logo Telkom"
                        style="width:250px; height:auto;"
                        class="mb-2">
                </div>

                <nav class="nav flex-column px-2 overflow-y-auto" style="flex: 1;">
                    {{-- DASHBOARD --}}
                    <a href="{{ route('dashboard') }}" class="nav-link text-white rounded mb-1 px-3 py-2 d-flex align-items-center {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : '' }} hover-bg-light">
                        <i class="bi bi-grid-1x2-fill me-2 fs-5"></i>
                        Dashboard
                    </a>

                    {{-- REKAP AGENCY BILLING 1-2 --}}
                    <a href="{{ route('rekap.agency') }}" class="nav-link text-white rounded mb-1 px-3 py-2 d-flex align-items-center {{ request()->routeIs('rekap.agency') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : '' }} hover-bg-light">
                        <i class="bi bi-building-fill me-2 fs-5"></i>
                        Rekap Agency Billing 1-2
                    </a>

                    {{-- RIWAYAT REMINDER --}}
                    <a href="{{ route('reminders.index') }}" class="nav-link text-white rounded mb-1 px-3 py-2 d-flex align-items-center {{ request()->routeIs('reminders.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : '' }} hover-bg-light">
                        <i class="bi bi-clock-history me-2 fs-5"></i>
                        Riwayat Reminder
                    </a>

                    <hr class="border-light border-opacity-25 my-3 mx-2">

                    {{-- SINKRONISASI --}}
                    <a href="{{ route('sync.index') }}" class="nav-link text-white rounded mb-1 px-3 py-2 d-flex align-items-center {{ request()->routeIs('sync.*') ? 'bg-white bg-opacity-25 fw-bold shadow-sm' : '' }} hover-bg-light">
                        <i class="bi bi-arrow-repeat me-2 fs-5"></i>
                        Sinkronisasi
                    </a>
                </nav>

                {{-- FOOTER SIDEBAR --}}
                <div class="p-3 border-top border-light border-opacity-25 mt-auto">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 1.2rem;">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'Admin' }}</div>
                            <small class="text-white-50" style="font-size: 0.7rem;">
                                <i class="bi bi-shield-check me-1"></i>
                                {{ Auth::user()->role ?? 'Supervisor' }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-white-50 mb-2" style="font-size: 0.75rem;">
                        <small>
                            <i class="bi bi-database me-1"></i>
                            {{ number_format($totalCustomer ?? 0) }} Cust
                        </small>
                        <small>
                            <i class="bi bi-clock me-1"></i>
                            {{ now()->format('d/m H:i') }}
                        </small>
                    </div>
                    <hr class="border-light border-opacity-25 my-2">
                    <a href="#" class="text-white-50 text-decoration-none d-flex align-items-center" style="font-size: 0.8rem;" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
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
                        <h1 class="mb-0 fw-bold text-primary-custom">
                            @yield('title', 'Dashboard')
                        </h1>
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