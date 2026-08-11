<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * ============================================
     * DASHBOARD
     * ============================================
     */
    public function dashboard(Request $request)
    {
        $datel = $request->input('datel');
        $agency = $request->input('agency');
        $sales = $request->input('sales');

        $applyFilters = function ($query) use ($datel, $agency, $sales) {
            if ($datel) {
                $query->where('datel', $datel);
            }
            if ($agency) {
                $query->where('agency', $agency);
            }
            if ($sales) {
                $query->where('sales', $sales);
            }
            // HANYA menampilkan yang belum bayar di dashboard
            $query->where('status_bayar', '!=', 'Sdh Bayar');
            return $query;
        };

        // Data statistik
        $totalBelumLunas = $applyFilters(Customer::query())->count();
        $totalTagihan = $applyFilters(Customer::query())->sum('tag_total');
        $totalSaldo = $applyFilters(Customer::query())->sum('tag_total');
        $totalAgency = $applyFilters(Customer::query())->distinct('agency')->count('agency');
        $totalSales = $applyFilters(Customer::query())->distinct('sales')->count('sales');

        // Billing Summary 1-6
        $billingSummary = clone $applyFilters(Customer::query());
        $billingSummary = $billingSummary->select(
            'billing_ke',
            DB::raw('COUNT(*) as belum_lunas'),
            DB::raw('SUM(tag_total) as total_tagihan')
        )
            ->whereNotNull('billing_ke')
            ->whereBetween('billing_ke', [1, 6])
            ->groupBy('billing_ke')
            ->orderBy('billing_ke')
            ->get();

        // HOTD Data - Rekapan per billing per datel
        $hotdData = clone $applyFilters(Customer::query());
        $hotdData = $hotdData->select(
            'datel',
            'billing_ke',
            DB::raw('COUNT(*) as blm_bayar'),
            DB::raw('SUM(tag_total) as blm_bayar_rp')
        )
            ->whereNotNull('datel')
            ->whereBetween('billing_ke', [1, 6])
            ->groupBy('datel', 'billing_ke')
            ->orderBy('billing_ke')
            ->orderBy('datel')
            ->get();

        // Latest 10 Customers (Belum bayar)
        $latestCustomers = clone $applyFilters(Customer::query());
        $latestCustomers = $latestCustomers->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // List datels untuk dropdown filter
        $datelsList = Customer::distinct('datel')->whereNotNull('datel')->pluck('datel');

        return view('dashboard', compact(
            'totalBelumLunas',
            'totalTagihan',
            'totalSaldo',
            'totalAgency',
            'totalSales',
            'billingSummary',
            'hotdData',
            'latestCustomers',
            'datelsList'
        ));
    }

    /**
     * ============================================
     * HOTD DETAIL (AJAX)
     * ============================================
     */
    public function hotdDetail($billingKe, $datel)
    {
        $customers = Customer::where('billing_ke', $billingKe)
            ->where('datel', $datel)
            ->select(
                'status_bayar',
                'tag_total',
                'tag_inet',
                'tag_tlp',
                'snd',
                'snd_group',
                'ncli',
                'nama',
                'alamat',
                'sto',
                'datel',
                'produk',
                'eksepsi_desc',
                'desc_newbill',
                'usage_desc',
                'saldo',
                'umur_customer',
                'billing_ke',
                'paid_l11',
                'tgl_paid',
                'paid_rp',
                'coll_agent',
                'tgl_klaim',
                'amount_klaim',
                'user_klaim',
                'tgl_paid_n1',
                'agency_psb',
                'sales_agency',
                'ppp',
                'caring_mybrains'
            )
            ->orderBy('tag_total', 'DESC')
            ->get();

        return response()->json([
            'billing_ke' => $billingKe,
            'datel' => $datel,
            'total_customer' => $customers->count(),
            'total_tagihan' => $customers->sum('tag_total'),
            'total_saldo' => $customers->sum('tag_total'),
            'total_blm_bayar' => $customers->where('status_bayar', '!=', 'Sdh Bayar')->count(),
            'total_sdh_bayar' => $customers->where('status_bayar', 'Sdh Bayar')->count(),
            'customers' => $customers
        ]);
    }

    /**
     * ============================================
     * DATA CUSTOMER (INDEX)
     * ============================================
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Filter Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('snd', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter Status Bayar
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        // Filter Datel
        if ($request->filled('datel')) {
            $query->where('datel', $request->datel);
        }

        // Filter Agency
        if ($request->filled('agency')) {
            $query->where('agency', $request->agency);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(50);

        // Data untuk filter
        $filters = [
            'datel' => Customer::distinct('datel')->whereNotNull('datel')->pluck('datel'),
            'agency' => Customer::distinct('agency')->whereNotNull('agency')->pluck('agency'),
        ];

        return view('customers.index', compact('customers', 'filters'));
    }


    /**
     * ============================================
     * RIWAYAT REMINDER - CUSTOMER BELUM BAYAR
     * ============================================
     */
    public function riwayatReminder(Request $request)
    {
        $query = Reminder::query();

        // Filter Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                    ->orWhere('sales_agency', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Filter Rentang Tanggal (format: DD/MM/YYYY - DD/MM/YYYY atau DD/MM/YYYY to DD/MM/YYYY)
        if ($request->filled('daterange')) {
            $separator = str_contains($request->daterange, ' to ') ? ' to ' : ' - ';
            $dates = explode($separator, $request->daterange);
            
            try {
                if (count($dates) == 2) {
                    $dateFrom = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $dateTo = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                } elseif (count($dates) == 1 && !empty(trim($dates[0]))) {
                    $dateFrom = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $dateTo = \Carbon\Carbon::createFromFormat('d/m/Y', trim($dates[0]))->endOfDay();
                    $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                }
            } catch (\Exception $e) {
                // Fallback jika format d/m/Y gagal, coba parse otomatis menggunakan Carbon
                try {
                    if (count($dates) == 2) {
                        $dateFrom = \Carbon\Carbon::parse(trim($dates[0]))->startOfDay();
                        $dateTo = \Carbon\Carbon::parse(trim($dates[1]))->endOfDay();
                        $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                    } elseif (count($dates) == 1 && !empty(trim($dates[0]))) {
                        $dateFrom = \Carbon\Carbon::parse(trim($dates[0]))->startOfDay();
                        $dateTo = \Carbon\Carbon::parse(trim($dates[0]))->endOfDay();
                        $query->whereBetween('created_at', [$dateFrom, $dateTo]);
                    }
                } catch (\Exception $ex) {
                    \Log::error("Gagal parse daterange: " . $ex->getMessage());
                }
            }
        }

        $reminders = $query->orderBy('created_at', 'desc')->paginate(30);

        return view('reminders.index', compact('reminders'));
    }

    /**
     * ============================================
     * REKAP AGENCY BILLING 1-2 (LENGKAP)
     * ============================================
     */
    public function rekapAgency(Request $request)
    {
        // Rekap mengikuti logika Pivot Spreadsheet:
        // STATUS BAYAR = Blm Bayar
        // BILLING KE = 1 dan 2
        // GROUP BY AGENCY PSB + SALES AGENCY
        // NILAI = COUNT SND + SUM SALDO

        $rekap = Customer::select(
            'agency_psb',
            'sales_agency',

            // Billing 1
            DB::raw("SUM(CASE WHEN billing_ke = 1 AND snd IS NOT NULL AND snd != '' THEN 1 ELSE 0 END) as billing_1_ssl"),
            DB::raw('SUM(CASE WHEN billing_ke = 1 THEN saldo ELSE 0 END) as billing_1_saldo'),

            // Billing 2
            DB::raw("SUM(CASE WHEN billing_ke = 2 AND snd IS NOT NULL AND snd != '' THEN 1 ELSE 0 END) as billing_2_ssl"),
            DB::raw('SUM(CASE WHEN billing_ke = 2 THEN saldo ELSE 0 END) as billing_2_saldo'),

            // Total Billing 1 + 2
            DB::raw("COUNT(NULLIF(snd, '')) as total_ssl"),
            DB::raw('SUM(saldo) as total_saldo')
        )
            ->whereNotNull('agency_psb')

            // Sesuai filter Pivot: hanya Belum Bayar
            ->where('status_bayar', 'Blm Bayar')

            // Sesuai filter Pivot: Billing 1 dan 2
            ->whereBetween('billing_ke', [1, 2])

            // Filter Agency
            ->when($request->filled('agency_psb'), function ($q) use ($request) {
                return $q->where('agency_psb', $request->agency_psb);
            })

            // Filter Sales Agency
            ->when($request->filled('sales_agency'), function ($q) use ($request) {
                return $q->where('sales_agency', $request->sales_agency);
            })

            ->groupBy('agency_psb', 'sales_agency')
            ->orderBy('agency_psb', 'ASC')
            ->orderBy('sales_agency', 'ASC')
            ->paginate(25);


        // ==============================
        // SUMMARY
        // ==============================

        $summaryQuery = Customer::whereNotNull('agency_psb')
            ->where('status_bayar', 'Blm Bayar')
            ->whereBetween('billing_ke', [1, 2]);

        $summary = [
            'total_customer' => (clone $summaryQuery)->count(),

            'total_sudah_bayar' => (clone $summaryQuery)
                ->where('status_bayar', 'Sdh Bayar')
                ->count(),

            'total_belum_bayar' => (clone $summaryQuery)
                ->count(),

            'total_saldo' => (clone $summaryQuery)
                ->sum('saldo'),
        ];


        // ==============================
        // DATA FILTER
        // ==============================

        $filters = [
            'agency_psb' => Customer::whereNotNull('agency_psb')
                ->where('agency_psb', '!=', '')
                ->where('status_bayar', 'Blm Bayar')
                ->whereBetween('billing_ke', [1, 2])
                ->distinct()
                ->orderBy('agency_psb')
                ->pluck('agency_psb'),

            'sales_agency' => Customer::whereNotNull('sales_agency')
                ->where('sales_agency', '!=', '')
                ->where('status_bayar', 'Blm Bayar')
                ->whereBetween('billing_ke', [1, 2])
                ->when($request->filled('agency_psb'), function ($q) use ($request) {
                    $q->where('agency_psb', $request->agency_psb);
                })
                ->distinct()
                ->orderBy('sales_agency')
                ->pluck('sales_agency'),
        ];

        return view(
            'rekap-agency',
            compact('rekap', 'summary', 'filters')
        );
    }
    /**
     * ============================================
     * EXPORT EXCEL
     * ============================================
     */
    public function exportExcel(Request $request)
    {
        // Ambil data dengan filter yang sama
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('snd', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }
        if ($request->filled('datel')) {
            $query->where('datel', $request->datel);
        }
        if ($request->filled('agency')) {
            $query->where('agency', $request->agency);
        }

        $customers = $query->get();

        // Export ke Excel (gunakan library seperti Maatwebsite Excel)
        // Atau return view export
        return view('customers.export_excel', compact('customers'));
    }

    /**
     * ============================================
     * EXPORT PDF
     * ============================================
     */
    public function exportPdf(Request $request)
    {
        // Sama seperti export Excel tapi untuk PDF
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('snd', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }
        if ($request->filled('datel')) {
            $query->where('datel', $request->datel);
        }
        if ($request->filled('agency')) {
            $query->where('agency', $request->agency);
        }

        $customers = $query->get();

        return view('customers.export_pdf', compact('customers'));
    }

    /**
     * ============================================
     * DOWNLOAD SSL
     * ============================================
     */
    public function downloadSsl($id)
    {
        $customer = Customer::findOrFail($id);

        if (!$customer->ssl_file) {
            return back()->with('error', 'File SSL tidak ditemukan untuk customer ini.');
        }

        $filePath = storage_path('app/ssl/' . $customer->ssl_file);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File SSL tidak ditemukan di server.');
        }

        return response()->download($filePath, $customer->ssl_file);
    }
}
