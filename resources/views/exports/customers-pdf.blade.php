<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Laporan Customer' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            padding: 20px;
            color: #333;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #2B5797;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            color: #2B5797;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 12px;
            color: #666;
        }

        .header .info {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }

        /* Logo Placeholder */
        .logo-placeholder {
            width: 60px;
            height: 60px;
            background: #f0f2f5;
            border: 2px dashed #ccc;
            border-radius: 10px;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #999;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead th {
            background: #2B5797;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #2B5797;
        }

        table tbody td {
            padding: 5px 8px;
            border: 1px solid #ddd;
            font-size: 8px;
            vertical-align: middle;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody tr:hover {
            background: #f0f4ff;
        }

        /* Status Badge */
        .badge-success {
            background: #28a745;
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 7px;
            display: inline-block;
        }

        .badge-danger {
            background: #dc3545;
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 7px;
            display: inline-block;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 7px;
            display: inline-block;
        }

        .badge-info {
            background: #17a2b8;
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 7px;
            display: inline-block;
        }

        /* Footer */
        .footer {
            border-top: 2px solid #2B5797;
            padding-top: 10px;
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
        }

        .footer .page-number {
            float: right;
        }

        /* Summary */
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 9px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-item .label {
            color: #666;
            font-size: 8px;
        }

        .summary-item .value {
            font-weight: 700;
            font-size: 11px;
            color: #2B5797;
        }

        /* Text alignment */
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }

        /* Currency */
        .currency {
            font-weight: 600;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            bottom: 50px;
            right: 50px;
            opacity: 0.05;
            font-size: 60px;
            font-weight: 700;
            color: #2B5797;
            transform: rotate(-20deg);
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
    {{-- Watermark --}}
    <div class="watermark">TELKOM</div>

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="header">
        {{-- Logo Placeholder --}}
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height: 50px; margin-bottom: 10px;">
        @else
            <div class="logo-placeholder">
                <i class="fas fa-building"></i>
            </div>
        @endif

        <h1>{{ $title ?? 'Laporan Data Customer' }}</h1>
        <div class="subtitle">{{ $company ?? 'Telkom Indonesia' }}</div>
        <div class="info">
            Tanggal Export: {{ $export_date ?? now()->format('d F Y H:i') }} |
            Total Data: {{ number_format($total ?? 0) }} Customer
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SUMMARY --}}
    {{-- ============================================ --}}
    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Customer</div>
            <div class="value">{{ number_format($total ?? 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Tagihan</div>
            <div class="value">Rp {{ number_format($customers->sum('tag_total') ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Lunas</div>
            <div class="value" style="color: #28a745;">
                {{ number_format($customers->where('status_bayar', 'Sdh Bayar')->count()) }}
            </div>
        </div>
        <div class="summary-item">
            <div class="label">Belum Lunas</div>
            <div class="value" style="color: #dc3545;">
                {{ number_format($customers->where('status_bayar', '!=', 'Sdh Bayar')->count()) }}
            </div>
        </div>
        <div class="summary-item">
            <div class="label">Total Agency</div>
            <div class="value">{{ number_format($customers->unique('agency')->count()) }}</div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABLE --}}
    {{-- ============================================ --}}
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">SND</th>
                <th style="width: 15%;">Nama</th>
                <th style="width: 10%;">Agency</th>
                <th style="width: 10%;">Sales</th>
                <th style="width: 8%;">Billing</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 12%;">Tagihan</th>
                <th style="width: 10%;">Saldo</th>
                <th style="width: 11%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $customer->snd ?? '-' }}</strong></td>
                    <td>{{ Str::limit($customer->nama ?? '-', 25) }}</td>
                    <td>{{ Str::limit($customer->agency ?? '-', 15) }}</td>
                    <td>{{ Str::limit($customer->sales ?? '-', 15) }}</td>
                    <td class="text-center">
                        <span class="badge-info">B{{ $customer->billing_ke }}</span>
                    </td>
                    <td>
                        @if(($customer->status_bayar ?? '') == 'Sdh Bayar')
                            <span class="badge-success">Sudah Bayar</span>
                        @else
                            <span class="badge-danger">Belum Bayar</span>
                        @endif
                    </td>
                    <td class="text-right currency">
                        Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-right currency">
                        Rp {{ number_format($customer->saldo ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        {{ $customer->created_at ? $customer->created_at->format('d/m/Y') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 30px; color: #999;">
                        <i class="fas fa-inbox" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                        Tidak ada data customer
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ============================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================ --}}
    <div class="footer">
        <span>
            <i class="fas fa-print"></i>
            Dicetak pada: {{ now()->format('d F Y H:i:s') }}
        </span>
        <span class="page-number">
            Halaman <span class="pagenum"></span>
        </span>
        <br>
        <small>
            Laporan ini dibuat secara otomatis oleh sistem Telkom Customer Management
        </small>
    </div>

    {{-- ============================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================ --}}
    <script type="text/php">
        if (isset($pdf)) {
            $x = 530;
            $y = 810;
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $font = $fontMetrics->get_font("Arial, sans-serif", "normal");
            $size = 8;
            $color = array(0,0,0);
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>