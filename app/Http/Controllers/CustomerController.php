<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $search = $request->input('search');

        $invalidPlaceholders = [
            '#N/A ()',
            '#N/A',
            '0',
            'UNKNOWN',
            'null',
            'NULL'
        ];

        $applyFilters = function ($query) use (
            $datel,
            $agency,
            $sales,
            $search,
            $invalidPlaceholders
        ) {
            if ($datel) {
                $query->where('datel', $datel);
            }

            if ($agency) {
                $query->where('agency_psb', $agency);
            }

            if ($sales) {
                $query->where('sales_agency', $sales);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('snd', 'like', "%{$search}%")
                        ->orWhere('ncli', 'like', "%{$search}%")
                        ->orWhere('agency_psb', 'like', "%{$search}%")
                        ->orWhere('sales_agency', 'like', "%{$search}%")
                        ->orWhere('datel', 'like', "%{$search}%");
                });
            }

            $query->whereNotIn('datel', $invalidPlaceholders)
                ->whereNotIn('agency_psb', $invalidPlaceholders)
                ->whereNotIn('sales_agency', $invalidPlaceholders);

            $query->whereBetween('billing_ke', [1, 6]);

            $query->where('status_bayar', '!=', 'Sdh Bayar');

            return $query;
        };

        // ============================================================
        // BASE QUERY
        // ============================================================

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

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('snd', 'like', "%{$search}%")
                    ->orWhere('ncli', 'like', "%{$search}%")
                    ->orWhere('agency_psb', 'like', "%{$search}%")
                    ->orWhere('sales_agency', 'like', "%{$search}%")
                    ->orWhere('datel', 'like', "%{$search}%");
            });
        }

        // ============================================================
        // STATISTIK
        // ============================================================

        $unpaidQuery = (clone $baseQuery)
            ->where('status_bayar', '!=', 'Sdh Bayar');

        // 1 NCLI = 1 customer.
        // Jika NCLI kosong, fallback ke SND.
        $customerGroupSql = "CASE
            WHEN ncli IS NOT NULL AND TRIM(ncli) != ''
                THEN CONCAT('NCLI_', TRIM(ncli))
            ELSE CONCAT('SND_', snd)
        END";

        $totalBelumLunas = (clone $unpaidQuery)
            ->selectRaw("COUNT(DISTINCT {$customerGroupSql}) as total")
            ->value('total') ?? 0;

        // Tagihan tetap dijumlahkan berdasarkan layanan/SND.
        $totalTagihan = (clone $unpaidQuery)->sum('tag_total');

        // Total Sales Agency
        $salesCountQuery = (clone $unpaidQuery)
            ->whereNotNull('sales_agency')
            ->where('sales_agency', '!=', '')
            ->whereNotIn('sales_agency', $invalidPlaceholders);

        $totalSales = $salesCountQuery
            ->distinct()
            ->pluck('sales_agency')
            ->filter()
            ->count();

        // Total Agency
        $agencyCountQuery = (clone $unpaidQuery)
            ->whereNotNull('agency_psb')
            ->where('agency_psb', '!=', '')
            ->whereNotIn('agency_psb', $invalidPlaceholders);

        $totalAgency = $agencyCountQuery
            ->distinct()
            ->pluck('agency_psb')
            ->filter()
            ->count();

        // ============================================================
        // SALES
        // ============================================================

        $totalCustomer = $totalBelumLunas;
        $totalBelumBayarSales = $totalBelumLunas;
        $totalTagihanSales = $totalTagihan;
        $totalSaldoSales = $totalTagihan;

        // ============================================================
        // DEFAULT VARIABLE
        // ============================================================

        $rekapBilling = null;
        $agencyCustomers = null;
        $salesCustomers = null;
        $searchCustomers = null;

        // ============================================================
        // CASE VIEW
        // ============================================================

        if ($sales) {

            $salesCustomers = (clone $unpaidQuery)
                ->orderBy('tag_total', 'desc')
                ->paginate(30)
                ->withQueryString();

        } elseif ($agency) {

            $agencyCustomers = (clone $unpaidQuery)
                ->orderBy('tag_total', 'desc')
                ->paginate(30)
                ->withQueryString();

        } elseif ($search) {

            $searchCustomers = (clone $unpaidQuery)
                ->orderBy('tag_total', 'desc')
                ->paginate(30)
                ->withQueryString();

        } else {

            $rekapQuery = (clone $baseQuery);

            $rekapBilling = $rekapQuery
                ->select(
                    'billing_ke',
                    DB::raw("COUNT(DISTINCT {$customerGroupSql}) as total_cust"),
                    DB::raw("COUNT(DISTINCT CASE
                        WHEN status_bayar != 'Sdh Bayar'
                        THEN {$customerGroupSql}
                        ELSE NULL
                    END) as unpaid_cust"),
                    DB::raw('SUM(CASE
                        WHEN status_bayar != "Sdh Bayar"
                        THEN tag_total
                        ELSE 0
                    END) as unpaid_rp')
                )
                ->groupBy('billing_ke')
                ->orderBy('billing_ke')
                ->get();
        }

        // ============================================================
        // DASHBOARD GRID
        // ============================================================

        $dashboardGrid = [];

        if (!$datel && !$agency && !$sales) {

            $rawGrid = Customer::query()
                ->select(
                    'datel',
                    'billing_ke',
                    DB::raw("COUNT(DISTINCT {$customerGroupSql}) as total_cust"),
                    DB::raw("COUNT(DISTINCT CASE
                        WHEN status_bayar != 'Sdh Bayar'
                        THEN {$customerGroupSql}
                        ELSE NULL
                    END) as unpaid_cust"),
                    DB::raw('SUM(CASE
                        WHEN status_bayar != "Sdh Bayar"
                        THEN tag_total
                        ELSE 0
                    END) as unpaid_rp')
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

            $allDbDatels = $rawGrid
                ->pluck('datel')
                ->unique()
                ->toArray();

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
                    'reward' => (
                        str_contains(strtolower($datelName), 'majalengka') ||
                        str_contains($datelName, '91406')
                    )
                        ? '150.000'
                        : ''
                ];

                $datelGroup = $gridByDatel->get(
                    $datelName,
                    collect()
                );

                foreach (range(1, 6) as $b) {

                    $item = $datelGroup->firstWhere(
                        'billing_ke',
                        $b
                    );

                    if ($item) {

                        $ssl = $item->unpaid_cust;
                        $rp = $item->unpaid_rp;
                        $tot = $item->total_cust;

                        $rate = $tot > 0
                            ? (($tot - $ssl) / $tot) * 100
                            : 100;

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

        // ============================================================
        // DATA DASHBOARD
        // ============================================================

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
                    DB::raw("COUNT(DISTINCT {$customerGroupSql}) as blm_bayar"),
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
                    DB::raw("COUNT(DISTINCT {$customerGroupSql}) as belum_lunas"),
                    DB::raw('SUM(tag_total) as total_tagihan')
                )
                ->groupBy('billing_ke')
                ->orderBy('billing_ke')
                ->get();
        }

        // ============================================================
        // DATEL LIST
        // ============================================================

        $datelsList = Customer::distinct('datel')
            ->whereBetween('billing_ke', [1, 6])
            ->where('status_bayar', '!=', 'Sdh Bayar')
            ->whereNotNull('datel')
            ->where('datel', '!=', '')
            ->whereNotIn('datel', $invalidPlaceholders)
            ->orderBy('datel')
            ->pluck('datel');

        // ============================================================
        // AGENCY LIST
        // ============================================================

        $agenciesQuery = Customer::query()
            ->whereBetween('billing_ke', [1, 6])
            ->where('status_bayar', '!=', 'Sdh Bayar')
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

        // ============================================================
        // SALES LIST
        // ============================================================

        $salesQuery = Customer::query()
            ->whereBetween('billing_ke', [1, 6])
            ->where('status_bayar', '!=', 'Sdh Bayar')
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

        return view(
            'dashboard',
            compact(
                'totalBelumLunas',
                'totalTagihan',
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
                'searchCustomers',
                'dashboardGrid'
            )
        );
    }


    /**
     * ============================================
     * AJAX FILTER AGENCY
     * ============================================
     */
    public function getAgencies(Request $request)
    {
        $invalidPlaceholders = [
            '#N/A ()',
            '#N/A',
            '0',
            'UNKNOWN',
            'null',
            'NULL'
        ];

        $query = Customer::query()
            ->whereBetween('billing_ke', [1, 6])
            ->where('status_bayar', '!=', 'Sdh Bayar')
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


    /**
     * ============================================
     * AJAX FILTER SALES
     * ============================================
     */
    public function getSales(Request $request)
    {
        $invalidPlaceholders = [
            '#N/A ()',
            '#N/A',
            '0',
            'UNKNOWN',
            'null',
            'NULL'
        ];

        $query = Customer::query()
            ->whereBetween('billing_ke', [1, 6])
            ->where('status_bayar', '!=', 'Sdh Bayar')
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
     * HOTD DETAIL
     * ============================================
     */
    public function hotdDetail(
        $billingKe,
        $datel,
        ?Request $request = null
    ) {
        $request = $request ?? request();

        $invalidPlaceholders = [
            '#N/A ()',
            '#N/A',
            '0',
            'UNKNOWN',
            'null',
            'NULL'
        ];

        // =========================================================
        // 1. Ambil seluruh layanan/SND sesuai filter
        // =========================================================

        $query = Customer::query()
            ->where('status_bayar', '!=', 'Sdh Bayar')
            ->whereBetween('billing_ke', [1, 6])
            ->whereNotIn('datel', $invalidPlaceholders)
            ->whereNotIn('agency_psb', $invalidPlaceholders)
            ->whereNotIn('sales_agency', $invalidPlaceholders);

        // FILTER BILLING
        if (
            $billingKe &&
            $billingKe !== 'All' &&
            $billingKe !== 'Semua Billing' &&
            $billingKe !== 'all' &&
            $billingKe !== 'TOTAL' &&
            $billingKe != 0
        ) {
            $query->where('billing_ke', $billingKe);
        }

        // FILTER DATEL
        if (
            $datel &&
            $datel !== 'Nasional' &&
            $datel !== 'Semua Datel' &&
            $datel !== 'Semua' &&
            $datel !== 'TOTAL'
        ) {
            $query->where('datel', $datel);
        }

        // FILTER AGENCY
        if ($request->filled('agency')) {
            $query->where(
                'agency_psb',
                $request->agency
            );
        }

        // FILTER SALES
        if ($request->filled('sales')) {
            $query->where(
                'sales_agency',
                $request->sales
            );
        }

        $rawCustomers = $query->select(
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

        // =========================================================
        // 2. Grouping tampilan:
        //    - NCLI sama = 1 customer
        //    - NCLI kosong = fallback per SND
        //    - SND yang sama di dalam grup hanya dihitung sekali
        // =========================================================

        $customers = $rawCustomers
            ->groupBy(function ($customer) {
                $ncli = trim((string) ($customer->ncli ?? ''));

                return $ncli !== ''
                    ? 'NCLI_' . $ncli
                    : 'SND_' . trim((string) ($customer->snd ?? ''));
            })
            ->map(function ($group) {

                // Proteksi jika sumber mengandung row SND yang sama berulang.
                $uniqueServices = $group
                    ->unique(function ($item) {
                        $snd = trim((string) ($item->snd ?? ''));

                        // Bila SND kosong, jangan gabungkan semua row kosong.
                        return $snd !== ''
                            ? 'SND_' . $snd
                            : 'ROW_' . spl_object_id($item);
                    })
                    ->values();

                // Untuk baris utama, prioritaskan produk Internet.
                $representative = $uniqueServices->first(function ($item) {
                    return str_contains(
                        strtolower((string) ($item->produk ?? '')),
                        'internet'
                    );
                }) ?? $uniqueServices->first();

                $customer = clone $representative;

                // SND Group pada baris utama mengikuti row representative asli.
                $customer->snd_group = trim(
                    (string) ($representative->snd_group ?? '')
                ) !== ''
                    ? (string) $representative->snd_group
                    : null;

                // Nilai finansial digabung dari seluruh SND unik dalam NCLI.
                $customer->tag_total = (float) $uniqueServices->sum('tag_total');
                $customer->tag_inet = (float) $uniqueServices->sum('tag_inet');
                $customer->tag_tlp = (float) $uniqueServices->sum('tag_tlp');
                $customer->saldo = (float) $uniqueServices->sum('saldo');

                // Data tambahan untuk badge/detail di dashboard.
                $customer->jumlah_snd = $uniqueServices->count();

                $customer->daftar_snd = $uniqueServices
                    ->pluck('snd')
                    ->filter(fn ($snd) => trim((string) $snd) !== '')
                    ->map(fn ($snd) => (string) $snd)
                    ->values()
                    ->all();

                $customer->detail_snd = $uniqueServices
                    ->map(function ($item) {
                        return [
                            'snd' => (string) ($item->snd ?? ''),
                            'snd_group' => (string) ($item->snd_group ?? ''),
                            'produk' => (string) ($item->produk ?? ''),
                            'tag_total' => (float) ($item->tag_total ?? 0),
                        ];
                    })
                    ->values()
                    ->all();

                return $customer;
            })
            ->sortByDesc('tag_total')
            ->values();

        $totalCustomer = $customers->count();

        $totalTagihan = (float) $customers->sum('tag_total');
        $totalSaldo = (float) $customers->sum('saldo');

        // =========================================================
        // 3. Response popup HOTD
        // =========================================================

        return response()->json([
            'billing_ke' => $billingKe,
            'datel' => $datel,
            'total_customer' => $totalCustomer,
            'total_tagihan' => $totalTagihan,
            'total_saldo' => $totalSaldo,
            'total_blm_bayar' => $totalCustomer,
            'total_sdh_bayar' => 0,
            'customers' => $customers,
        ]);
    }


    /**
     * ============================================
     * DATA CUSTOMER
     * ============================================
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // SEARCH
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'nama',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'snd',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'alamat',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        // STATUS
        if ($request->filled('status_bayar')) {
            $query->where(
                'status_bayar',
                $request->status_bayar
            );
        }

        // DATEL
        if ($request->filled('datel')) {
            $query->where(
                'datel',
                $request->datel
            );
        }

        // AGENCY
        if ($request->filled('agency')) {

            $agency = $request->agency;

            $query->where(function ($q) use ($agency) {

                $q->where(
                    'agency_psb',
                    $agency
                )
                    ->orWhere(
                        'agency',
                        $agency
                    );
            });
        }

        $customers = $query
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // FILTER DATA
        $filters = [

            'datel' => Customer::distinct('datel')
                ->whereNotNull('datel')
                ->where('datel', '!=', '')
                ->orderBy('datel')
                ->pluck('datel'),

            'agency' => Customer::where(function ($q) {

                $q->where(function ($sub) {

                    $sub->whereNotNull('agency_psb')
                        ->where('agency_psb', '!=', '');

                })->orWhere(function ($sub) {

                    $sub->whereNotNull('agency')
                        ->where('agency', '!=', '');

                });

            })
                ->select(
                    DB::raw(
                        "COALESCE(NULLIF(agency_psb, ''), agency) as agency_val"
                    )
                )
                ->distinct()
                ->orderBy('agency_val')
                ->pluck('agency_val'),
        ];

        return view(
            'customers.index',
            compact(
                'customers',
                'filters'
            )
        );
    }


    /**
     * ============================================
     * RIWAYAT REMINDER
     * ============================================
     */
    public function riwayatReminder(Request $request)
    {
        $query = Reminder::with('customer');

        // SEARCH
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'keterangan',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'sales_agency',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'status',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        // START DATE
        if ($request->filled('start_date')) {

            try {

                $dateFrom =
                    \Carbon\Carbon::parse(
                        $request->start_date
                    )->startOfDay();

                $dateTo =
                    $request->filled('end_date')
                        ? \Carbon\Carbon::parse(
                            $request->end_date
                        )->endOfDay()
                        : \Carbon\Carbon::parse(
                            $request->start_date
                        )->endOfDay();

                $query->whereBetween(
                    'created_at',
                    [$dateFrom, $dateTo]
                );

            } catch (\Exception $e) {

                Log::error(
                    "Gagal parse start_date/end_date: " .
                    $e->getMessage()
                );
            }

        } elseif ($request->filled('end_date')) {

            try {

                $dateTo =
                    \Carbon\Carbon::parse(
                        $request->end_date
                    )->endOfDay();

                $query->where(
                    'created_at',
                    '<=',
                    $dateTo
                );

            } catch (\Exception $e) {

                Log::error(
                    "Gagal parse end_date: " .
                    $e->getMessage()
                );
            }
        }

        $reminders = $query
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view(
            'reminders.index',
            compact('reminders')
        );
    }


    /**
     * ============================================
     * REKAP AGENCY BILLING 1-2
     * ============================================
     */
    public function rekapAgency(Request $request)
    {
        $search = trim(
            $request->input('search', '')
        );

        // ============================================================
        // 1. SEARCH CUSTOMER DETAIL
        // ============================================================

        $searchCustomers = null;

        if ($search !== '') {

            $searchCustomers = Customer::query()

                ->where('status_bayar', 'Blm Bayar')

                ->whereBetween(
                    'billing_ke',
                    [1, 2]
                )

                ->where(function ($q) use ($search) {

                    $q->where(
                        'nama',
                        'LIKE',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'snd',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'ncli',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'agency_psb',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'sales_agency',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'datel',
                            'LIKE',
                            "%{$search}%"
                        );
                })

                ->orderBy(
                    'tag_total',
                    'DESC'
                )

                ->paginate(
                    30,
                    [
                        'snd',
                        'snd_group',
                        'ncli',
                        'nama',
                        'alamat',
                        'sto',
                        'datel',
                        'produk',
                        'billing_ke',
                        'tag_total',
                        'tag_inet',
                        'tag_tlp',
                        'saldo',
                        'status_bayar',
                        'agency_psb',
                        'sales_agency',
                        'eksepsi_desc',
                        'desc_newbill',
                        'usage_desc',
                        'umur_customer',
                        'paid_l11',
                        'tgl_paid',
                        'paid_rp',
                        'coll_agent',
                        'tgl_klaim',
                        'amount_klaim',
                        'user_klaim',
                        'tgl_paid_n1',
                        'ppp',
                        'caring_mybrains'
                    ],
                    'customer_page'
                )

                ->withQueryString();
        }

        // ============================================================
        // 2. REKAP AGENCY BILLING 1-2
        // ============================================================

        $rekap = Customer::select(

            'agency_psb',
            'sales_agency',

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 1
                        AND snd IS NOT NULL
                        AND snd != ''
                        THEN 1
                        ELSE 0
                    END
                ) as billing_1_ssl
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 1
                        THEN saldo
                        ELSE 0
                    END
                ) as billing_1_saldo
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 2
                        AND snd IS NOT NULL
                        AND snd != ''
                        THEN 1
                        ELSE 0
                    END
                ) as billing_2_ssl
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 2
                        THEN saldo
                        ELSE 0
                    END
                ) as billing_2_saldo
            "),

            DB::raw("
                COUNT(NULLIF(snd, '')) as total_ssl
            "),

            DB::raw("
                SUM(saldo) as total_saldo
            ")
        )

            ->whereNotNull('agency_psb')

            ->where(
                'status_bayar',
                'Blm Bayar'
            )

            ->whereBetween(
                'billing_ke',
                [1, 2]
            )

            ->when(
                $search !== '',
                function ($q) use ($search) {

                    return $q->where(
                        function ($sub) use ($search) {

                            $sub->where(
                                'nama',
                                'LIKE',
                                "%{$search}%"
                            )
                                ->orWhere(
                                    'snd',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'ncli',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'agency_psb',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'sales_agency',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'datel',
                                    'LIKE',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('agency_psb'),
                function ($q) use ($request) {

                    return $q->where(
                        'agency_psb',
                        $request->agency_psb
                    );
                }
            )

            ->when(
                $request->filled('sales_agency'),
                function ($q) use ($request) {

                    return $q->where(
                        'sales_agency',
                        $request->sales_agency
                    );
                }
            )

            ->groupBy(
                'agency_psb',
                'sales_agency'
            )

            ->orderBy(
                'agency_psb',
                'ASC'
            )

            ->orderBy(
                'sales_agency',
                'ASC'
            )

            ->paginate(25)

            ->withQueryString();

        // ============================================================
        // 3. SUMMARY
        // ============================================================

        $summaryQuery = Customer::whereNotNull(
            'agency_psb'
        )
            ->where(
                'status_bayar',
                'Blm Bayar'
            )
            ->whereBetween(
                'billing_ke',
                [1, 2]
            )

            ->when(
                $search !== '',
                function ($q) use ($search) {

                    return $q->where(
                        function ($sub) use ($search) {

                            $sub->where(
                                'nama',
                                'LIKE',
                                "%{$search}%"
                            )
                                ->orWhere(
                                    'snd',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'ncli',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'agency_psb',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'sales_agency',
                                    'LIKE',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'datel',
                                    'LIKE',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $request->filled('agency_psb'),
                function ($q) use ($request) {

                    $q->where(
                        'agency_psb',
                        $request->agency_psb
                    );
                }
            )

            ->when(
                $request->filled('sales_agency'),
                function ($q) use ($request) {

                    $q->where(
                        'sales_agency',
                        $request->sales_agency
                    );
                }
            );

        $summary = [

            'total_customer' =>
                (clone $summaryQuery)->count(),

            'total_sudah_bayar' =>
                (clone $summaryQuery)
                    ->where(
                        'status_bayar',
                        'Sdh Bayar'
                    )
                    ->count(),

            'total_belum_bayar' =>
                (clone $summaryQuery)->count(),

            'total_saldo' =>
                (clone $summaryQuery)->sum(
                    'saldo'
                ),
        ];

        // ============================================================
        // 4. FILTER AGENCY & SALES
        // ============================================================

        $filters = [

            'agency_psb' => Customer::whereNotNull(
                'agency_psb'
            )
                ->where(
                    'agency_psb',
                    '!=',
                    ''
                )
                ->where(
                    'status_bayar',
                    'Blm Bayar'
                )
                ->whereBetween(
                    'billing_ke',
                    [1, 2]
                )
                ->distinct()
                ->orderBy('agency_psb')
                ->pluck('agency_psb'),

            'sales_agency' => Customer::whereNotNull(
                'sales_agency'
            )
                ->where(
                    'sales_agency',
                    '!=',
                    ''
                )
                ->where(
                    'status_bayar',
                    'Blm Bayar'
                )
                ->whereBetween(
                    'billing_ke',
                    [1, 2]
                )
                ->when(
                    $request->filled('agency_psb'),
                    function ($q) use ($request) {

                        $q->where(
                            'agency_psb',
                            $request->agency_psb
                        );
                    }
                )
                ->distinct()
                ->orderBy('sales_agency')
                ->pluck('sales_agency'),
        ];

        // ============================================================
        // 5. BILLING 1 WITEL
        // ============================================================

        $billing1WitelRaw = Customer::select(

            DB::raw("
                CASE
                    WHEN LOWER(TRIM(agency_psb)) = 'm others'
                    THEN 'M Others'
                    ELSE TRIM(agency_psb)
                END as agency_psb
            "),

            'datel',

            DB::raw(
                'COUNT(*) as total'
            )
        )

            ->where(
                'billing_ke',
                1
            )

            ->where(
                'status_bayar',
                'Blm Bayar'
            )

            ->whereNotNull(
                'agency_psb'
            )

            ->where(
                'agency_psb',
                '!=',
                ''
            )

            ->whereNotNull(
                'datel'
            )

            ->where(
                'datel',
                '!=',
                ''
            )

            ->when(
                $request->filled('agency_psb'),
                function ($q) use ($request) {

                    return $q->where(
                        'agency_psb',
                        $request->agency_psb
                    );
                }
            )

            ->when(
                $request->filled('sales_agency'),
                function ($q) use ($request) {

                    return $q->where(
                        'sales_agency',
                        $request->sales_agency
                    );
                }
            )

            ->groupBy(
                'agency_psb',
                'datel'
            )

            ->get();

        $witelAgencies = $billing1WitelRaw
            ->pluck('agency_psb')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $witelDatels = $billing1WitelRaw
            ->pluck('datel')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $witelData = [];

        foreach ($billing1WitelRaw as $item) {

            $witelData[
                $item->agency_psb
            ][
                $item->datel
            ] = $item->total;
        }

        // ============================================================
        // 6. BILLING 2 WITEL
        // ============================================================

        $billing2WitelRaw = Customer::select(

            'agency_psb',
            'datel',

            DB::raw(
                'COUNT(*) as total'
            )
        )

            ->where(
                'billing_ke',
                2
            )

            ->where(
                'status_bayar',
                'Blm Bayar'
            )

            ->whereNotNull(
                'agency_psb'
            )

            ->where(
                'agency_psb',
                '!=',
                ''
            )

            ->whereNotNull(
                'datel'
            )

            ->where(
                'datel',
                '!=',
                ''
            )

            ->when(
                $request->filled('agency_psb'),
                function ($q) use ($request) {

                    return $q->where(
                        'agency_psb',
                        $request->agency_psb
                    );
                }
            )

            ->when(
                $request->filled('sales_agency'),
                function ($q) use ($request) {

                    return $q->where(
                        'sales_agency',
                        $request->sales_agency
                    );
                }
            )

            ->groupBy(
                'agency_psb',
                'datel'
            )

            ->get();

        $witelAgencies2 = $billing2WitelRaw
            ->pluck('agency_psb')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $witelDatels2 = $billing2WitelRaw
            ->pluck('datel')
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $witelData2 = [];

        foreach ($billing2WitelRaw as $item) {

            $witelData2[
                $item->agency_psb
            ][
                $item->datel
            ] = $item->total;
        }

        // ============================================================
        // 7. KIRIM SEMUA DATA KE BLADE
        // ============================================================

        return view(
            'rekap-agency',
            compact(
                'rekap',
                'summary',
                'filters',
                'searchCustomers',

                'witelAgencies',
                'witelDatels',
                'witelData',

                'witelAgencies2',
                'witelDatels2',
                'witelData2'
            )
        );
    }

/**
 * ============================================
 * REKAP AGENCY BILLING 3-6 HOTD
 * ============================================
 */
public function rekapAgencyBilling36(
    Request $request
) {
    $billing = $request->input(
        'billing',
        'all'
    );

    $search = trim($request->input('search', ''));

        // ============================================================
        // TABEL UTAMA BILLING 3-6
        // ============================================================

        $rekap = Customer::select(

            'agency_psb',
            'sales_agency',

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 3
                        AND snd IS NOT NULL
                        AND snd != ''
                        THEN 1
                        ELSE 0
                    END
                ) as billing_3_ssl
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 3
                        THEN saldo
                        ELSE 0
                    END
                ) as billing_3_saldo
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 4
                        AND snd IS NOT NULL
                        AND snd != ''
                        THEN 1
                        ELSE 0
                    END
                ) as billing_4_ssl
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 4
                        THEN saldo
                        ELSE 0
                    END
                ) as billing_4_saldo
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 5
                        AND snd IS NOT NULL
                        AND snd != ''
                        THEN 1
                        ELSE 0
                    END
                ) as billing_5_ssl
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 5
                        THEN saldo
                        ELSE 0
                    END
                ) as billing_5_saldo
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 6
                        AND snd IS NOT NULL
                        AND snd != ''
                        THEN 1
                        ELSE 0
                    END
                ) as billing_6_ssl
            "),

            DB::raw("
                SUM(
                    CASE
                        WHEN billing_ke = 6
                        THEN saldo
                        ELSE 0
                    END
                ) as billing_6_saldo
            "),

            DB::raw("
                COUNT(NULLIF(snd, '')) as total_ssl
            "),

            DB::raw("
                SUM(saldo) as total_saldo
            ")
        )

            ->whereNotNull(
                'agency_psb'
            )

            ->where(
                'status_bayar',
                'Blm Bayar'
            )

            ->whereBetween(
                'billing_ke',
                [3, 6]
            )

            // FILTER AGENCY
            ->when(
                $request->filled('agency_psb'),
                function ($q) use ($request) {

                    $q->where(
                        'agency_psb',
                        $request->agency_psb
                    );
                }
            )

            // FILTER SALES
            ->when(
                $request->filled('sales_agency'),
                function ($q) use ($request) {

                    $q->where(
                        'sales_agency',
                        $request->sales_agency
                    );
                }
            )

            

            ->groupBy(
                'agency_psb',
                'sales_agency'
            )

->orderBy(
    'agency_psb',
    'ASC'
)

->orderBy(
    'sales_agency',
    'ASC'
)

->paginate(25)
->withQueryString();

        
        // ============================================================
        // SEARCH DETAIL CUSTOMER
        // ============================================================

        $searchCustomers = collect();

        if ($search !== '') {

            $searchCustomers = Customer::where('status_bayar', 'Blm Bayar')
                ->whereBetween('billing_ke', [3, 6])

                // FILTER AGENCY
                ->when($request->filled('agency_psb'), function ($q) use ($request) {
                    $q->where('agency_psb', $request->agency_psb);
                })

                // FILTER SALES
                ->when($request->filled('sales_agency'), function ($q) use ($request) {
                    $q->where('sales_agency', $request->sales_agency);
                })

                // SEARCH SEMUA KOLOM DETAIL
                ->where(function ($query) use ($search) {

                    $query->where('status_bayar', 'like', "%{$search}%")
                        ->orWhere('tag_inet', 'like', "%{$search}%")
                        ->orWhere('tag_tlp', 'like', "%{$search}%")
                        ->orWhere('tag_total', 'like', "%{$search}%")
                        ->orWhere('snd', 'like', "%{$search}%")
                        ->orWhere('snd_group', 'like', "%{$search}%")
                        ->orWhere('ncli', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%")
                        ->orWhere('sto', 'like', "%{$search}%")
                        ->orWhere('datel', 'like', "%{$search}%")
                        ->orWhere('produk', 'like', "%{$search}%")
                        ->orWhere('eksepsi_desc', 'like', "%{$search}%")
                        ->orWhere('desc_newbill', 'like', "%{$search}%")
                        ->orWhere('usage_desc', 'like', "%{$search}%")
                        ->orWhere('saldo', 'like', "%{$search}%")
                        ->orWhere('umur_customer', 'like', "%{$search}%")
                        ->orWhere('billing_ke', 'like', "%{$search}%")
                        ->orWhere('paid_l11', 'like', "%{$search}%")
                        ->orWhere('tgl_paid', 'like', "%{$search}%")
                        ->orWhere('paid_rp', 'like', "%{$search}%")
                        ->orWhere('coll_agent', 'like', "%{$search}%")
                        ->orWhere('tgl_klaim', 'like', "%{$search}%")
                        ->orWhere('amount_klaim', 'like', "%{$search}%")
                        ->orWhere('user_klaim', 'like', "%{$search}%")
                        ->orWhere('agency_psb', 'like', "%{$search}%")
                        ->orWhere('sales_agency', 'like', "%{$search}%")
                        ->orWhere('ppp', 'like', "%{$search}%")
                        ->orWhere('caring_mybrains', 'like', "%{$search}%");

                })

                ->orderBy('billing_ke')
                ->orderBy('agency_psb')
                ->orderBy('nama')
                ->get();
        }

        // ============================================================
        // FILTER AGENCY & SALES
        // ============================================================

        $filters = [

            'agency_psb' => Customer::whereNotNull(
                'agency_psb'
            )
                ->where(
                    'agency_psb',
                    '!=',
                    ''
                )
                ->where(
                    'status_bayar',
                    'Blm Bayar'
                )
                ->whereBetween(
                    'billing_ke',
                    [3, 6]
                )
                ->distinct()
                ->orderBy('agency_psb')
                ->pluck('agency_psb'),

            'sales_agency' => Customer::whereNotNull(
                'sales_agency'
            )
                ->where(
                    'sales_agency',
                    '!=',
                    ''
                )
                ->where(
                    'status_bayar',
                    'Blm Bayar'
                )
                ->whereBetween(
                    'billing_ke',
                    [3, 6]
                )
                ->when(
                    $request->filled('agency_psb'),
                    function ($q) use ($request) {

                        $q->where(
                            'agency_psb',
                            $request->agency_psb
                        );
                    }
                )
                ->distinct()
                ->orderBy('sales_agency')
                ->pluck('sales_agency'),
        ];

        // ============================================================
        // FUNCTION BILLING TABLE
        // ============================================================

        $createBillingTable =
            function ($billingKe) use ($request) {

                $raw = Customer::select(
                    'agency_psb',
                    'datel',
                    DB::raw(
                        'COUNT(*) as total'
                    )
                )

                    ->where(
                        'billing_ke',
                        $billingKe
                    )

                    ->where(
                        'status_bayar',
                        'Blm Bayar'
                    )

                    ->whereNotNull(
                        'agency_psb'
                    )

                    ->where(
                        'agency_psb',
                        '!=',
                        ''
                    )

                    ->whereNotNull(
                        'datel'
                    )

                    ->where(
                        'datel',
                        '!=',
                        ''
                    )

                    // AGENCY
                    ->when(
                        $request->filled('agency_psb'),
                        function ($q) use ($request) {

                            $q->where(
                                'agency_psb',
                                $request->agency_psb
                            );
                        }
                    )

                    // SALES
                    ->when(
                        $request->filled('sales_agency'),
                        function ($q) use ($request) {

                            $q->where(
                                'sales_agency',
                                $request->sales_agency
                            );
                        }
                    )

                    ->groupBy(
                        'agency_psb',
                        'datel'
                    )

                    ->orderBy(
                        'agency_psb',
                        'ASC'
                    )

                    ->orderBy(
                        'datel',
                        'ASC'
                    )

                    ->get();

                $agencies = $raw
                    ->pluck('agency_psb')
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();

                $datels = $raw
                    ->pluck('datel')
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();

                $data = [];

                foreach ($raw as $item) {

                    $data[
                        $item->agency_psb
                    ][
                        $item->datel
                    ] = $item->total;
                }

                return [
                    'agencies' => $agencies,
                    'datels' => $datels,
                    'data' => $data,
                ];
            };

        // BILLING 3
        $billing3 = $createBillingTable(3);

        $witelAgencies3 =
            $billing3['agencies'];

        $witelDatels3 =
            $billing3['datels'];

        $witelData3 =
            $billing3['data'];

        // BILLING 4
        $billing4 = $createBillingTable(4);

        $witelAgencies4 =
            $billing4['agencies'];

        $witelDatels4 =
            $billing4['datels'];

        $witelData4 =
            $billing4['data'];

        // BILLING 5
        $billing5 = $createBillingTable(5);

        $witelAgencies5 =
            $billing5['agencies'];

        $witelDatels5 =
            $billing5['datels'];

        $witelData5 =
            $billing5['data'];

        // BILLING 6
        $billing6 = $createBillingTable(6);

        $witelAgencies6 =
            $billing6['agencies'];

        $witelDatels6 =
            $billing6['datels'];

        $witelData6 =
            $billing6['data'];

        return view(
            'rekap-billing36-hotd',
            compact(
                'rekap',
                'filters',
                'billing',
'search',
'searchCustomers',

                'witelAgencies3',
                'witelDatels3',
                'witelData3',

                'witelAgencies4',
                'witelDatels4',
                'witelData4',

                'witelAgencies5',
                'witelDatels5',
                'witelData5',

                'witelAgencies6',
                'witelDatels6',
                'witelData6'
            )
        );
    }


    /**
     * ============================================
     * EXPORT EXCEL
     * ============================================
     */
    public function exportExcel(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'nama',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'snd',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'alamat',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('status_bayar')) {

            $query->where(
                'status_bayar',
                $request->status_bayar
            );
        }

        if ($request->filled('datel')) {

            $query->where(
                'datel',
                $request->datel
            );
        }

        if ($request->filled('agency')) {

            $query->where(
                'agency',
                $request->agency
            );
        }

        $customers = $query->get();

        return view(
            'customers.export_excel',
            compact('customers')
        );
    }


    /**
     * ============================================
     * EXPORT PDF
     * ============================================
     */
    public function exportPdf(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'nama',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'snd',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'alamat',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('status_bayar')) {

            $query->where(
                'status_bayar',
                $request->status_bayar
            );
        }

        if ($request->filled('datel')) {

            $query->where(
                'datel',
                $request->datel
            );
        }

        if ($request->filled('agency')) {

            $query->where(
                'agency',
                $request->agency
            );
        }

        $customers = $query->get();

        return view(
            'customers.export_pdf',
            compact('customers')
        );
    }


    /**
     * ============================================
     * DOWNLOAD SSL
     * ============================================
     */
    public function downloadSsl($snd)
    {
        $customer = Customer::where(
            'snd',
            $snd
        )->firstOrFail();

        if (!$customer->ssl_file) {

            return back()->with(
                'error',
                'File SSL tidak ditemukan untuk customer ini.'
            );
        }

        $filePath = storage_path(
            'app/ssl/' . $customer->ssl_file
        );

        if (!file_exists($filePath)) {

            return back()->with(
                'error',
                'File SSL tidak ditemukan di server.'
            );
        }

        return response()->download(
            $filePath,
            $customer->ssl_file
        );
    }


    /**
     * ============================================
     * EXPORT EXCEL HOTD DETAIL
     * ============================================
     */
    public function exportHotdExcel(
        $billingKe,
        $datel,
        Request $request
    ) {
        return \Maatwebsite\Excel\Facades\Excel::download(

            new \App\Exports\HotdDetailExport(
                $billingKe,
                $datel,
                $request->agency,
                $request->sales
            ),

            'hotd_billing_' .
            $billingKe .
            '_' .
            str_replace(
                [' ', '-', '/'],
                '_',
                $datel
            ) .
            '.xlsx'
        );
    }
}