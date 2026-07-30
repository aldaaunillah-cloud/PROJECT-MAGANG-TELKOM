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
    public function dashboard()
    {
        // Data statistik
        $totalCustomer = Customer::count();
        $totalLunas = Customer::where('status_bayar', 'Sdh Bayar')->count();
        $totalBelumLunas = Customer::where('status_bayar', '!=', 'Sdh Bayar')->count();
        $totalTagihan = Customer::sum('tag_total');
        $totalSaldo = Customer::sum('saldo');
        $totalAgency = Customer::distinct('agency')->count('agency');
        $totalSales = Customer::distinct('sales')->count('sales');
        $persentaseLunas = $totalCustomer > 0 ? ($totalLunas / $totalCustomer) * 100 : 0;
        
        // Billing Summary 1-6
        $billingSummary = Customer::select(
            'billing_ke',
            DB::raw('COUNT(*) as total_customer'),
            DB::raw('SUM(tag_total) as total_tagihan'),
            DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) as lunas'),
            DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as belum_lunas')
        )
        ->whereNotNull('billing_ke')
        ->whereBetween('billing_ke', [1, 6])
        ->groupBy('billing_ke')
        ->orderBy('billing_ke')
        ->get();
        
        // HOTD Data - Rekapan per billing per datel
        $hotdData = Customer::select(
            'datel',
            'billing_ke',
            DB::raw('COUNT(*) as total_customer'),
            DB::raw('SUM(tag_total) as total_tagihan'),
            DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as blm_bayar'),
            DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN tag_total ELSE 0 END) as blm_bayar_rp'),
            DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) as sdh_bayar'),
            DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN tag_total ELSE 0 END) as sdh_bayar_rp')
        )
        ->whereNotNull('datel')
        ->whereBetween('billing_ke', [1, 6])
        ->groupBy('datel', 'billing_ke')
        ->orderBy('billing_ke')
        ->orderBy('datel')
        ->get();
        
        // Status Bayar
        $statusBayar = Customer::select('status_bayar', DB::raw('COUNT(*) as total'))
            ->whereNotNull('status_bayar')
            ->groupBy('status_bayar')
            ->get();
            
        // Latest 10 Customers
        $latestCustomers = Customer::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboard', compact(
            'totalCustomer',
            'totalLunas',
            'totalBelumLunas',
            'totalTagihan',
            'totalSaldo',
            'totalAgency',
            'totalSales',
            'persentaseLunas',
            'billingSummary',
            'hotdData',
            'statusBayar',
            'latestCustomers'
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
            'total_saldo' => $customers->sum('saldo'),
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
            $query->where(function($q) use ($search) {
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
    // Ambil data customer yang BELUM BAYAR
    $query = Customer::where('status_bayar', '!=', 'Sdh Bayar')
        ->whereNotNull('status_bayar')
        ->where('tag_total', '>', 0);
    
    // Filter Agency
    if ($request->filled('agency')) {
        $query->where('agency', $request->agency);
    }
    
    // Filter Sales
    if ($request->filled('sales')) {
        $query->where('sales', $request->sales);
    }
    
    // Filter Billing
    if ($request->filled('billing_ke')) {
        $query->where('billing_ke', $request->billing_ke);
    }
    
    // Filter Datel
    if ($request->filled('datel')) {
        $query->where('datel', $request->datel);
    }
    
    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('snd', 'like', "%{$search}%")
              ->orWhere('agency', 'like', "%{$search}%")
              ->orWhere('sales', 'like', "%{$search}%")
              ->orWhere('alamat', 'like', "%{$search}%");
        });
    }
    
    $reminders = $query->orderBy('tag_total', 'DESC')->paginate(20);
    
    // Data untuk filter
    $agencies = Customer::where('status_bayar', '!=', 'Sdh Bayar')
        ->where('tag_total', '>', 0)
        ->distinct('agency')
        ->whereNotNull('agency')
        ->pluck('agency');
        
    $sales = Customer::where('status_bayar', '!=', 'Sdh Bayar')
        ->where('tag_total', '>', 0)
        ->distinct('sales')
        ->whereNotNull('sales')
        ->pluck('sales');
    
    $datels = Customer::where('status_bayar', '!=', 'Sdh Bayar')
        ->where('tag_total', '>', 0)
        ->distinct('datel')
        ->whereNotNull('datel')
        ->pluck('datel');
    
    $billingList = [1, 2, 3, 4, 5, 6];
    
    return view('reminders.index', compact('reminders', 'agencies', 'sales', 'datels', 'billingList'));
}

    /**
 * ============================================
 * REKAP AGENCY BILLING 1-2 (LENGKAP)
 * ============================================
 */
public function rekapAgency(Request $request)
{
    // Query untuk rekap per agency_psb dan sales_agency
    $rekap = Customer::select(
        'agency_psb',
        'sales_agency',
        DB::raw('SUM(CASE WHEN billing_ke = 1 THEN 1 ELSE 0 END) as billing_1_ssl'),
        DB::raw('SUM(CASE WHEN billing_ke = 1 THEN saldo ELSE 0 END) as billing_1_saldo'),
        DB::raw('SUM(CASE WHEN billing_ke = 2 THEN 1 ELSE 0 END) as billing_2_ssl'),
        DB::raw('SUM(CASE WHEN billing_ke = 2 THEN saldo ELSE 0 END) as billing_2_saldo'),
        DB::raw('COUNT(*) as total_ssl'),
        DB::raw('SUM(saldo) as total_saldo')
    )
    ->whereNotNull('agency_psb')
    ->whereBetween('billing_ke', [1, 2])
    ->when($request->filled('agency_psb'), function($q) use ($request) {
        return $q->where('agency_psb', $request->agency_psb);
    })
    ->when($request->filled('sales_agency'), function($q) use ($request) {
        return $q->where('sales_agency', $request->sales_agency);
    })
    ->groupBy('agency_psb', 'sales_agency')
    ->orderBy('total_saldo', 'DESC')
    ->paginate(50);
    
    // Summary
    $summaryQuery = Customer::whereNotNull('agency_psb')
        ->whereBetween('billing_ke', [1, 2]);
    
    $summary = [
        'total_customer' => $summaryQuery->count(),
        'total_sudah_bayar' => (clone $summaryQuery)->where('status_bayar', 'Sdh Bayar')->count(),
        'total_belum_bayar' => (clone $summaryQuery)->where('status_bayar', '!=', 'Sdh Bayar')->count(),
        'total_saldo' => (clone $summaryQuery)->sum('saldo'),
    ];
    
    // Data untuk filter
    $filters = [
        'agency_psb' => Customer::whereNotNull('agency_psb')->distinct('agency_psb')->pluck('agency_psb'),
        'sales_agency' => Customer::whereNotNull('sales_agency')->distinct('sales_agency')->pluck('sales_agency'),
    ];
    
    return view('rekap-agency', compact('rekap', 'summary', 'filters'));
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
            $query->where(function($q) use ($search) {
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
            $query->where(function($q) use ($search) {
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