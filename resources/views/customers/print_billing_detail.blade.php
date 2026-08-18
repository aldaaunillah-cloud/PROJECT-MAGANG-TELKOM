<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Detail Billing {{ $billing_ke }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000361;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #000361;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #666;
            font-size: 10px;
        }
        .confidential {
            text-align: right;
        }
        .confidential h2 {
            font-size: 14px;
            margin: 0;
            color: #dc3545;
            letter-spacing: 0.5px;
        }
        .confidential p {
            margin: 3px 0 0 0;
            color: #888;
            font-size: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #000361;
        }
        .text-end {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            color: #fff;
        }
        .bg-success {
            background-color: #198754;
        }
        .bg-danger {
            background-color: #dc3545;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="header">
        <div>
            <h1>LAPORAN DETAIL CUSTOMER - BILLING {{ $billing_ke }}</h1>
            <p>Dihasilkan secara dinamis dari Telkom Customer Management System</p>
        </div>
        <div class="confidential">
            <h2>CONFIDENTIAL</h2>
            <p>Printed: {{ now()->format('d-m-Y H:i:s') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">SND</th>
                <th style="width: 30%;">NAMA CUSTOMER</th>
                <th style="width: 15%;">DATEL</th>
                <th style="width: 15%;">AGENCY PSB</th>
                <th style="width: 10%;">STATUS</th>
                <th style="width: 10%; text-align: right;">TAGIHAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $index => $customer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><code>{{ $customer->snd }}</code></td>
                    <td>{{ $customer->nama }}</td>
                    <td>{{ $customer->datel }}</td>
                    <td>{{ $customer->agency_psb ?: '-' }}</td>
                    <td>
                        <span class="badge {{ $customer->status_bayar == 'Sdh Bayar' ? 'bg-success' : 'bg-danger' }}">
                            {{ $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                        </span>
                    </td>
                    <td class="text-end">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
