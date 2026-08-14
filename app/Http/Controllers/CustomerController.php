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

        $invalidPlaceholders = ['#N/A ()', '#N/A', '0', 'UNKNOWN', 'null', 'NULL'];

        $applyFilters = function ($query) use ($datel, $agency, $sales, $invalidPlaceholders) {
            if ($datel) {
                $query->where('datel', $datel);
            }
            if ($agency) {
                $query->where('agency_psb', $agency);
            }
            if ($sales) {
                $query->where('sales_agency', $sales);
            }
            
            // Filter invalid placeholders agar tidak dihitung dalam rekap
            $query->whereNotIn('datel', $invalidPlaceholders)
                  ->whereNotIn('agency_psb', $invalidPlaceholders)
                  ->whereNotIn('sales_agency', $invalidPlaceholders);

            // Filter billing_ke 1-6 agar sesuai dengan rekap spreadsheet
            $query->whereBetween('billing_ke', [1, 6]);

            // HANYA menampilkan yang belum bayar di dashboard
            $query->where('status_bayar', '!=', 'Sdh Bayar');
            return $query;
        };

        // 1. Build base query with all active filters
        $baseQuery = Customer::query()
            ->whereBetween('billing_ke', [1, 6])
            ->whereNotIn('datel', $invalidPlaceholders)
            ->whereNotIn('agency_psb', $invalidPlaceholders)
            ->whereNotIn('sales_agency', $invalidPlaceholders);

        if ($datel) {
            $baseQuery->where('datel', $datel);
        }
        if ($agency) {
            $baseQuery->where('agency_psb', $agency);
        }
        if ($sales) {
            $baseQuery->where('sales_agency', $sales);
        }

        // 2. Compute statistics for Default/Datel/Agency views (Unpaid data only)
        $unpaidQuery = (clone $baseQuery)->where('status_bayar', '!=', 'Sdh Bayar');
        $totalBelumLunas = (clone $unpaidQuery)->count();
        $totalTagihan = (clone $unpaidQuery)->sum('tag_total');
        $totalSales = (clone $unpaidQuery)->distinct()->pluck('sales_agency')->filter()->count();
        $totalAgency = (clone $unpaidQuery)->distinct()->pluck('agency_psb')->filter()->count();

        // 3. Compute statistics for Sales view (All base vs Unpaid)
        $totalCustomer = (clone $baseQuery)->count();
        $totalBelumBayarSales = (clone $unpaidQuery)->count();
        $totalTagihanSales = (clone $baseQuery)->sum('tag_total');
        $totalSaldoSales = (clone $unpaidQuery)->sum('tag_total');

        // Default Case Variables
        $rekapBilling = null;
        $agencyCustomers = null;
        $salesCustomers = null;

        // 4. Load case-specific data
        if ($sales) {
            // Case 3: Sales Agency Terpilih (Menampilkan semua customer baik lunas maupun belum bayar)
            $salesCustomers = (clone $baseQuery)
                ->orderBy('status_bayar', 'desc') // Belum bayar first
                ->orderBy('tag_total', 'desc')
                ->paginate(30)
                ->withQueryString();

        } elseif ($agency) {
            // Case 2: Agency Terpilih (Hanya menampilkan yang belum bayar)
            $agencyCustomers = (clone $unpaidQuery)
                ->orderBy('tag_total', 'desc')
                ->paginate(30)
                ->withQueryString();

        } else {
            // Case 1: Hanya Datel Terpilih / Default View
            $rekapQuery = (clone $baseQuery);

            $rekapBilling = $rekapQuery
                ->select(
                    'billing_ke',
                    DB::raw('COUNT(*) as total_cust'),
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as unpaid_cust'),
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN tag_total ELSE 0 END) as unpaid_rp')
                )
                ->groupBy('billing_ke')
                ->orderBy('billing_ke')
                ->get();
        }

        // Tampilan Awal: 2D Grid Matrix Datel by Billing 1-6
        $dashboardGrid = [];
        if (!$datel && !$agency && !$sales) {
            $rawGrid = Customer::query()
                ->select(
                    'datel',
                    'billing_ke',
                    DB::raw('COUNT(*) as total_cust'),
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as unpaid_cust'),
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN tag_total ELSE 0 END) as unpaid_rp')
                )
                ->whereBetween('billing_ke', [1, 6])
                ->whereNotIn('datel', $invalidPlaceholders)
                ->whereNotIn('agency_psb', $invalidPlaceholders)
                ->whereNotIn('sales_agency', $invalidPlaceholders)
                ->groupBy('datel', 'billing_ke')
                ->get();

            $gridByDatel = $rawGrid->groupBy('datel');

            $standardDatels = [
                '91405 - Kuningan',
                '91407 - Inner - Priangan Timur',
                '91403 - Garut',
                '91406 - Majalengka',
                '91404 - Indramayu',
                '91408 - Singaparna',
                '91409 - Tasikmalaya',
                '91401 - Banjar'
            ];

            // Get any other datels that might exist in database
            $allDbDatels = $rawGrid->pluck('datel')->unique()->toArray();
            foreach ($allDbDatels as $dbd) {
                if (!in_array($dbd, $standardDatels)) {
                    $standardDatels[] = $dbd;
                }
            }

            foreach ($standardDatels as $datelName) {
                $row = [
                    'datel' => $datelName,
                    'billings' => [],
                    'total_ssl' => 0,
                    'total_rp' => 0,
                    'reward' => (str_contains(strtolower($datelName), 'majalengka') || str_contains($datelName, '91406')) ? '150.000' : ''
                ];

                $datelGroup = $gridByDatel->get($datelName, collect());

                foreach (range(1, 6) as $b) {
                    $item = $datelGroup->firstWhere('billing_ke', $b);
                    if ($item) {
                        $ssl = $item->unpaid_cust;
                        $rp = $item->unpaid_rp;
                        $tot = $item->total_cust;
                        $rate = $tot > 0 ? (($tot - $ssl) / $tot) * 100 : 100;

                        $row['billings'][$b] = [
                            'ssl' => $ssl,
                            'rp' => $rp,
                            'rate' => $rate
                        ];
                        $row['total_ssl'] += $ssl;
                        $row['total_rp'] += $rp;
                    } else {
                        $row['billings'][$b] = [
                            'ssl' => 0,
                            'rp' => 0,
                            'rate' => 100
                        ];
                    }
                }

                $dashboardGrid[] = $row;
            }
        }

        // Populate dashboard data when no filters are selected
        $latestCustomers = [];
        $hotdData = [];
        $billingSummary = [];

        if (!$datel && !$agency && !$sales) {
            $latestCustomers = Customer::query()
                ->whereNotIn('datel', $invalidPlaceholders)
                ->whereNotIn('agency_psb', $invalidPlaceholders)
                ->whereNotIn('sales_agency', $invalidPlaceholders)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $hotdData = Customer::query()
                ->where('status_bayar', '!=', 'Sdh Bayar')
                ->whereBetween('billing_ke', [1, 6])
                ->whereNotIn('datel', $invalidPlaceholders)
                ->whereNotIn('agency_psb', $invalidPlaceholders)
                ->whereNotIn('sales_agency', $invalidPlaceholders)
                ->select(
                    'datel',
                    'billing_ke',
                    DB::raw('COUNT(*) as blm_bayar'),
                    DB::raw('SUM(tag_total) as blm_bayar_rp')
                )
                ->groupBy('datel', 'billing_ke')
                ->get();

            $billingSummary = Customer::query()
                ->where('status_bayar', '!=', 'Sdh Bayar')
                ->whereBetween('billing_ke', [1, 6])
                ->whereNotIn('datel', $invalidPlaceholders)
                ->whereNotIn('agency_psb', $invalidPlaceholders)
                ->whereNotIn('sales_agency', $invalidPlaceholders)
                ->select(
                    'billing_ke',
                    DB::raw('COUNT(*) as belum_lunas'),
                    DB::raw('SUM(tag_total) as total_tagihan')
                )
                ->groupBy('billing_ke')
                ->orderBy('billing_ke')
                ->get();
        }

        // List datels untuk dropdown filter
        $datelsList = Customer::distinct('datel')
            ->whereNotNull('datel')
            ->where('datel', '!=', '')
            ->whereNotIn('datel', $invalidPlaceholders)
            ->orderBy('datel')
            ->pluck('datel');

        $agenciesQuery = Customer::query()
            ->whereNotNull('agency_psb')
            ->where('agency_psb', '!=', '')
            ->whereNotIn('agency_psb', $invalidPlaceholders);

        if ($datel) {
            $agenciesQuery->where('datel', $datel);
        }
        $agenciesList = $agenciesQuery
            ->select('agency_psb as agency_val')
            ->distinct()
            ->orderBy('agency_val')
            ->pluck('agency_val');

        $salesQuery = Customer::query()
            ->whereNotNull('sales_agency')
            ->where('sales_agency', '!=', '')
            ->whereNotIn('sales_agency', $invalidPlaceholders);

        if ($datel) {
            $salesQuery->where('datel', $datel);
        }
        if ($agency) {
            $salesQuery->where('agency_psb', $agency);
        }
        $salesList = $salesQuery
            ->select('sales_agency as sales_val')
            ->distinct()
            ->orderBy('sales_val')
            ->pluck('sales_val');

        return view('dashboard', compact(
            'totalBelumLunas',
            'totalTagihan',
            'totalSaldo',
            'totalCustomer',
            'totalBelumBayarSales',
            'totalTagihanSales',
            'totalSaldoSales',
            'totalAgency',
            'totalSales',
            'billingSummary',
            'hotdData',
            'latestCustomers',
            'datelsList',
            'agenciesList',
            'salesList',
            'rekapBilling',
            'agencyCustomers',
            'salesCustomers',
            'dashboardGrid'
        ));
    }

    /**
     * ============================================
     * AJAX FILTER
     * ============================================
     */
    public function getAgencies(Request $request)
    {
        $invalidPlaceholders = ['#N/A ()', '#N/A', '0', 'UNKNOWN', 'null', 'NULL'];

        $query = Customer::query()
            ->whereNotNull('agency_psb')
            ->where('agency_psb', '!=', '')
            ->whereNotIn('agency_psb', $invalidPlaceholders);

        if ($request->filled('datel')) {
            $query->where('datel', $request->datel);
        }

        $agencies = $query
            ->select('agency_psb as agency_val')
            ->distinct()
            ->orderBy('agency_val')
            ->pluck('agency_val');

        return response()->json($agencies);
    }

    public function getSales(Request $request)
    {
        $invalidPlaceholders = ['#N/A ()', '#N/A', '0', 'UNKNOWN', 'null', 'NULL'];

        $query = Customer::query()
            ->whereNotNull('sales_agency')
            ->where('sales_agency', '!=', '')
            ->whereNotIn('sales_agency', $invalidPlaceholders);

        if ($request->filled('datel')) {
            $query->where('datel', $request->datel);
        }

        if ($request->filled('agency')) {
            $query->where('agency_psb', $request->agency);
        }

        $sales = $query
            ->select('sales_agency as sales_val')
            ->distinct()
            ->orderBy('sales_val')
            ->pluck('sales_val');

        return response()->json($sales);
    }

    /**
     * ============================================
     * HOTD DETAIL (AJAX)
     * ============================================
     */
    public function hotdDetail($billingKe, $datel, ?Request $request = null)
    {
        $request = $request ?? request();
        $query = Customer::where('billing_ke', $billingKe);

        if ($datel && $datel !== 'Nasional' && $datel !== 'Semua Datel') {
            $query->where('datel', $datel);
        }

        if ($request->filled('agency')) {
            $agency = $request->agency;
            $query->where(function ($q) use ($agency) {
                $q->where('agency_psb', $agency)
                  ->orWhere('agency', $agency);
            });
        }

        if ($request->filled('sales')) {
            $sales = $request->sales;
            $query->where(function ($q) use ($sales) {
                $q->where('sales_agency', $sales)
                  ->orWhere('sales', $sales);
            });
        }

        $customers = $query->select(
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
            $agency = $request->agency;
            $query->where(function ($q) use ($agency) {
                $q->where('agency_psb', $agency)
                  ->orWhere('agency', $agency);
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(50);

        // Data untuk filter
        $filters = [
            'datel' => Customer::distinct('datel')->whereNotNull('datel')->where('datel', '!=', '')->orderBy('datel')->pluck('datel'),
            'agency' => Customer::where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('agency_psb')->where('agency_psb', '!=', '');
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('agency')->where('agency', '!=', '');
                });
            })
            ->select(DB::raw("COALESCE(NULLIF(agency_psb, ''), agency) as agency_val"))
            ->distinct()
            ->orderBy('agency_val')
            ->pluck('agency_val'),
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

    /**
     * ============================================
     * EXPORT EXCEL HOTD DETAIL (DASHBOARD)
     * ============================================
     */
    public function exportHotdExcel($billingKe, $datel, Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\HotdDetailExport($billingKe, $datel, $request->agency, $request->sales), 
            'hotd_billing_' . $billingKe . '_' . str_replace([' ', '-', '/'], '_', $datel) . '.xlsx'
        );
    }
}
