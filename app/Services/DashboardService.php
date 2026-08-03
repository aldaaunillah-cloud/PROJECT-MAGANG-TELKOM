<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function getDashboardData(): array
    {
        return Cache::remember('dashboard_data', 900, function () {
            try {
                // QUERY 1: Main Statistics
                $stats = Customer::select([
                    DB::raw('COUNT(*) as total_customer'),
                    DB::raw('SUM(tag_total) as total_tagihan'),
                    DB::raw('SUM(tag_total) as total_saldo'),
                    DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) as total_lunas'),
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as total_belum_lunas'),
                    DB::raw('COUNT(DISTINCT agency) as total_agency'),
                    DB::raw('COUNT(DISTINCT sales) as total_sales'),
                    DB::raw('ROUND((SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as persentase_lunas')
                ])->first();

                // QUERY 2: Billing Summary - BILLING 1-6
                $billingSummary = Customer::select(
                    'billing_ke',
                    DB::raw('COUNT(*) as total_customer'),
                    DB::raw('SUM(tag_total) as total_tagihan'),
                    DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) as lunas'),
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as belum_lunas'),
                    DB::raw('ROUND((SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as persentase_lunas')
                )
                ->whereNotNull('billing_ke')
                ->whereBetween('billing_ke', [1, 6])
                ->groupBy('billing_ke')
                ->orderBy('billing_ke')
                ->get();

                // ============================================
                // QUERY 6: HOTD - DETAIL PER DATEL & BILLING 1-6
                // ============================================
                $hotdData = Customer::select(
                    'datel',
                    'billing_ke',
                    // Total customer per billing
                    DB::raw('COUNT(*) as total_customer'),
                    // Belum Bayar
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as blm_bayar'),
                    DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN tag_total ELSE 0 END) as blm_bayar_rp'),
                    // Sudah Bayar
                    DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) as sdh_bayar'),
                    DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN tag_total ELSE 0 END) as sdh_bayar_rp'),
                    // Total tagihan
                    DB::raw('SUM(tag_total) as total_tagihan')
                )
                ->whereBetween('billing_ke', [1, 6])
                ->whereNotNull('datel')
                ->where('datel', '!=', '')
                ->groupBy('datel', 'billing_ke')
                ->orderBy('billing_ke')
                ->orderBy('datel')
                ->get();

                // QUERY 3: Status Pembayaran
                $statusBayar = Customer::select('status_bayar', DB::raw('COUNT(*) as total'))
                    ->whereNotNull('status_bayar')
                    ->groupBy('status_bayar')
                    ->get();

                // QUERY 4: Billing Chart
                $billingKe = $billingSummary->map(function ($item) {
                    return (object) [
                        'billing_ke' => $item->billing_ke,
                        'total' => $item->total_customer
                    ];
                });

                // QUERY 5: Latest 10 Customers
                $latestCustomers = Customer::select([
                    'id', 'snd', 'nama', 'alamat', 'datel', 'agency', 'sales',
                    'billing_ke', 'status_bayar', 'tag_total', 'created_at'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

                return [
                    'totalCustomer' => $stats->total_customer ?? 0,
                    'totalTagihan' => $stats->total_tagihan ?? 0,
                    'totalSaldo' => $stats->total_saldo ?? 0,
                    'totalLunas' => $stats->total_lunas ?? 0,
                    'totalBelumLunas' => $stats->total_belum_lunas ?? 0,
                    'totalAgency' => $stats->total_agency ?? 0,
                    'totalSales' => $stats->total_sales ?? 0,
                    'persentaseLunas' => $stats->persentase_lunas ?? 0,
                    'billingSummary' => $billingSummary ?? collect([]),
                    'hotdData' => $hotdData ?? collect([]), // <-- TAMBAHKAN INI!
                    'statusBayar' => $statusBayar ?? collect([]),
                    'billingKe' => $billingKe ?? collect([]),
                    'latestCustomers' => $latestCustomers ?? collect([]),
                ];
            } catch (\Exception $e) {
                return [
                    'totalCustomer' => 0,
                    'totalTagihan' => 0,
                    'totalSaldo' => 0,
                    'totalLunas' => 0,
                    'totalBelumLunas' => 0,
                    'totalAgency' => 0,
                    'totalSales' => 0,
                    'persentaseLunas' => 0,
                    'billingSummary' => collect([]),
                    'hotdData' => collect([]),
                    'statusBayar' => collect([]),
                    'billingKe' => collect([]),
                    'latestCustomers' => collect([]),
                ];
            }
        });
    }

    public function clearCache(): void
    {
        Cache::forget('dashboard_data');
    }
}