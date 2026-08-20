<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Detail Billing {{ $billing_ke }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm;
        }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #334155;
            margin: 0;
            line-height: 1.4;
            background-color: #ffffff;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #e2001a; /* Telkom Red */
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .brand-section h1 {
            font-size: 18px;
            margin: 0;
            color: #000361; /* Telkom Deep Blue */
            font-weight: 800;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand-section p {
            margin: 4px 0 0 0;
            color: #64748b;
            font-size: 10px;
            font-weight: 500;
        }
        .confidential-section {
            text-align: right;
        }
        .confidential-badge {
            background-color: #fee2e2;
            color: #ef4444;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 9px;
            display: inline-block;
            letter-spacing: 1px;
            margin-bottom: 6px;
            border: 1px solid #fecaca;
        }
        .confidential-section p {
            margin: 0;
            color: #94a3b8;
            font-size: 9px;
        }
        
        /* Summary Box Cards */
        .summary-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-card {
            flex: 1;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 15px;
        }
        .summary-card .title {
            font-size: 8px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .summary-card .value {
            font-size: 16px;
            font-weight: 700;
            color: #000361;
        }
        .summary-card.red-border {
            border-left: 4px solid #e2001a;
        }
        .summary-card.blue-border {
            border-left: 4px solid #000361;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #000361;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #000361;
            text-align: left;
        }
        td {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            font-size: 9.5px;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        code {
            font-family: 'Consolas', 'Courier New', Courier, monospace;
            font-weight: 600;
            color: #0f172a;
            font-size: 9px;
            background-color: #f1f5f9;
            padding: 2px 4px;
            border-radius: 4px;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8px;
            font-weight: 700;
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .bg-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .bg-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header-container">
        <div class="brand-section">
            <h1>LAPORAN DETAIL CUSTOMER - BILLING {{ $billing_ke }}</h1>
            <p>Telkom Daerah Cirebon - Customer Management System</p>
        </div>
        <div class="confidential-section">
            <span class="confidential-badge">CONFIDENTIAL</span>
            <p>Printed: {{ now()->translatedFormat('d-m-Y H:i:s') }} WIB</p>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="summary-container">
        <div class="summary-card blue-border">
            <div class="title">Total Customer Belum Bayar</div>
            <div class="value">{{ number_format(count($customers)) }}</div>
        </div>
        <div class="summary-card red-border">
            <div class="title">Total Tunggakan Tagihan</div>
            <div class="value">Rp {{ number_format($customers->sum('tag_total') ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">NO</th>
                <th style="width: 15%;">SND</th>
                <th style="width: 35%;">NAMA CUSTOMER</th>
                <th style="width: 15%;">DATEL</th>
                <th style="width: 15%;">AGENCY PSB</th>
                <th style="width: 10%; text-align: center;">STATUS</th>
                <th style="width: 10%; text-align: right;">TAGIHAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><code>{{ $customer->snd }}</code></td>
                    <td style="font-weight: 600; color: #0f172a;">{{ $customer->nama }}</td>
                    <td>{{ $customer->datel }}</td>
                    <td>{{ $customer->agency_psb ?: '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $customer->status_bayar == 'Sdh Bayar' ? 'bg-success' : 'bg-danger' }}">
                            {{ $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                        </span>
                    </td>
                    <td class="text-end" style="font-weight: 700; color: #000361;">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">Tidak ada data customer.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
