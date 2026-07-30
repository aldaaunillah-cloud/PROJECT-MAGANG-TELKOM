<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #4e73df;
            padding-bottom: 15px;
        }
        .header h2 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header .subtitle {
            color: #6c757d;
            font-size: 12px;
        }
        .header .date {
            color: #6c757d;
            font-size: 10px;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table thead {
            background: #4e73df;
            color: white;
        }
        table thead th {
            padding: 8px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #4e73df;
        }
        table tbody td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 8px;
            vertical-align: middle;
        }
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            color: white;
        }
        .badge-success { background: #1cc88a; }
        .badge-danger { background: #e74a3b; }
        .badge-warning { background: #f6c23e; color: #2c3e50; }
        .badge-info { background: #36b9cc; }
        .badge-secondary { background: #858796; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #adb5bd;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-size: 12px;
        }
        .filter-info {
            font-size: 9px;
            color: #6c757d;
            margin-bottom: 10px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .filter-info strong { color: #2c3e50; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <div class="subtitle">Billing Telkom - Data Reminder</div>
        <div class="date">Dicetak: {{ $date }}</div>
    </div>

    @if(isset($customers) && count($customers) > 0)
        <div class="filter-info">
            <strong>Total Data:</strong> {{ count($customers) }} customer
            @if(request('search'))
                | <strong>Pencarian:</strong> "{{ request('search') }}"
            @endif
            @if(request('status'))
                | <strong>Status:</strong> {{ request('status') }}
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">SND</th>
                    <th style="width: 12%;">Nama</th>
                    <th style="width: 10%;">Alamat</th>
                    <th style="width: 6%;">Datel</th>
                    <th style="width: 8%;">Agency</th>
                    <th style="width: 8%;">SA</th>
                    <th style="width: 5%;">Billing</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 8%;">Tagihan</th>
                    <th style="width: 6%;">Klaim</th>
                    <th style="width: 6%;">Paid</th>
                    <th style="width: 8%;">Status Kirim</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $index => $customer)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $customer->snd ?? '-' }}</strong></td>
                    <td>{{ Str::limit($customer->nama ?? '-', 20) }}</td>
                    <td>{{ Str::limit($customer->alamat ?? '-', 20) }}</td>
                    <td>{{ $customer->datel ?? '-' }}</td>
                    <td>{{ $customer->agency ?? '-' }}</td>
                    <td>{{ $customer->sales ?? '-' }}</td>
                    <td class="text-center">
                        @if($customer->billing_ke)
                            <span class="badge badge-{{ $customer->billing_ke == 1 ? 'success' : 'warning' }}">
                                {{ $customer->billing_ke }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($customer->status_bayar == 'Sdh Bayar')
                            <span class="badge badge-success">Lunas</span>
                        @elseif($customer->status_bayar == 'Blm Bayar')
                            <span class="badge badge-danger">Belum</span>
                        @else
                            <span class="badge badge-secondary">{{ $customer->status_bayar ?? '-' }}</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($customer->tag_total ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $customer->tgl_klaim ? \Carbon\Carbon::parse($customer->tgl_klaim)->format('d/m') : '-' }}</td>
                    <td class="text-center">{{ $customer->tgl_paid ? \Carbon\Carbon::parse($customer->tgl_paid)->format('d/m') : '-' }}</td>
                    <td>
                        @if($customer->tgl_paid)
                            <span class="badge badge-success">Terkirim</span>
                        @else
                            <span class="badge badge-warning">Belum</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px; display: flex; justify-content: space-between; font-size: 9px;">
            <div><strong>Total:</strong> {{ count($customers) }} customer</div>
            <div><strong>Total Tagihan:</strong> Rp {{ number_format($customers->sum('tag_total'), 0, ',', '.') }}</div>
            <div><strong>Belum Bayar:</strong> {{ $customers->where('status_bayar', 'Blm Bayar')->count() }}</div>
            <div><strong>Sudah Bayar:</strong> {{ $customers->where('status_bayar', 'Sdh Bayar')->count() }}</div>
        </div>
    @else
        <div class="no-data">Tidak ada data reminder yang ditemukan</div>
    @endif

    <div class="footer">
        <span>Dicetak dari Aplikasi Billing Telkom &bull; {{ now()->format('d/m/Y H:i:s') }}</span>
    </div>
</body>
</html>