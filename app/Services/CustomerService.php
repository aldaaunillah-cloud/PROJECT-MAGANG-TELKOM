<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    protected Customer $model;

    public function __construct(Customer $customer)
    {
        $this->model = $customer;
    }

    public function getFilterOptions(): array
    {
        return Cache::remember('customer_filter_options', 3600, function () {
            try {
                $results = DB::table('customers')
                    ->select('datel as value', DB::raw("'datel' as type"))
                    ->whereNotNull('datel')
                    ->where('datel', '!=', '')
                    ->distinct()
                    ->union(
                        DB::table('customers')
                            ->select('agency as value', DB::raw("'agency' as type"))
                            ->whereNotNull('agency')
                            ->where('agency', '!=', '')
                            ->distinct()
                    )
                    ->union(
                        DB::table('customers')
                            ->select('sales as value', DB::raw("'sales' as type"))
                            ->whereNotNull('sales')
                            ->where('sales', '!=', '')
                            ->distinct()
                    )
                    ->orderBy('value')
                    ->get();

                $grouped = [
                    'datel' => [],
                    'agency' => [],
                    'sales' => [],
                    'status' => $this->getDistinctValues('status_bayar')->toArray(),
                    'billing_ke' => range(1, 6),
                ];

                foreach ($results as $row) {
                    if (isset($grouped[$row->type])) {
                        $grouped[$row->type][] = $row->value;
                    }
                }

                return $grouped;
            } catch (\Exception $e) {
                return [
                    'datel' => [],
                    'agency' => [],
                    'sales' => [],
                    'status' => [],
                    'billing_ke' => range(1, 6),
                ];
            }
        });
    }

    protected function getDistinctValues(string $column): Collection
    {
        return $this->model->query()
            ->select($column)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }

    public function getAgencyRekap(Request $request): LengthAwarePaginator
    {
        $query = $this->buildAgencyQuery($request);
        return $query->paginate(30)->withQueryString();
    }

    public function getAgencySummary(Request $request): array
    {
        try {
            $query = $this->buildAgencyQuery($request);
            $result = $query->first();
            
            if (!$result) {
                return [
                    'total_customer' => 0,
                    'total_tagihan' => 0,
                    'total_belum_bayar' => 0,
                    'total_sudah_bayar' => 0,
                    'total_saldo' => 0,
                ];
            }

            return [
                'total_customer' => $result->total_customer_summary ?? 0,
                'total_tagihan' => $result->total_tagihan_summary ?? 0,
                'total_belum_bayar' => $result->total_belum_bayar ?? 0,
                'total_sudah_bayar' => $result->total_sudah_bayar ?? 0,
                'total_saldo' => $result->total_saldo_summary ?? 0,
            ];
        } catch (\Exception $e) {
            return [
                'total_customer' => 0,
                'total_tagihan' => 0,
                'total_belum_bayar' => 0,
                'total_sudah_bayar' => 0,
                'total_saldo' => 0,
            ];
        }
    }

    protected function buildAgencyQuery(Request $request)
    {
        $query = $this->model->query()
            ->select(
                'agency',
                DB::raw('COUNT(*) as total_customer'),
                DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) as lunas'),
                DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as belum_lunas'),
                DB::raw('SUM(tag_total) as total_tagihan'),
                DB::raw('AVG(tag_total) as rata_rata_tagihan'),
                DB::raw('SUM(saldo) as total_saldo'),
                DB::raw('COUNT(CASE WHEN billing_ke <= 2 THEN 1 END) as billing_1_2'),
                DB::raw('COUNT(CASE WHEN billing_ke >= 3 THEN 1 END) as billing_3_6'),
                DB::raw('COUNT(*) as total_customer_summary'),
                DB::raw('SUM(tag_total) as total_tagihan_summary'),
                DB::raw('SUM(CASE WHEN status_bayar != "Sdh Bayar" THEN 1 ELSE 0 END) as total_belum_bayar'),
                DB::raw('SUM(CASE WHEN status_bayar = "Sdh Bayar" THEN 1 ELSE 0 END) as total_sudah_bayar'),
                DB::raw('SUM(saldo) as total_saldo_summary')
            )
            ->whereNotNull('agency')
            ->where('agency', '!=', '')
            ->groupBy('agency');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('agency')) {
            $query->where('agency', $request->agency);
        }

        if ($request->filled('sales')) {
            $query->where('sales', $request->sales);
        }

        return $query;
    }

    public function getReminderCustomers(Request $request): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->select([
                'id', 'snd', 'nama', 'alamat', 'datel', 'agency', 'sales',
                'billing_ke', 'status_bayar', 'tag_total', 'ssl_file',
                'tgl_klaim', 'tgl_paid', 'tgl_paid_n1', 'created_at'
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('snd', 'like', "%{$search}%")
                  ->orWhere('sales', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status == 'klaim') {
                $query->whereNotNull('tgl_klaim')->whereNull('tgl_paid');
            } elseif ($status == 'paid') {
                $query->whereNotNull('tgl_paid');
            } elseif ($status == 'paid_n1') {
                $query->whereNotNull('tgl_paid_n1');
            }
        }

        if ($request->filled('agency')) {
            $query->where('agency', $request->agency);
        }

        if ($request->filled('datel')) {
            $query->where('datel', $request->datel);
        }

        if ($request->filled('sales')) {
            $query->where('sales', $request->sales);
        }

        $query->orderBy('tgl_klaim', 'desc')
              ->orderBy('created_at', 'desc');

        return $query->paginate(30)->withQueryString();
    }

    public function downloadSslCertificate($id)
    {
        $customer = $this->model->findOrFail($id);
        
        if (!$customer->ssl_file) {
            throw new \Exception('SSL certificate tidak ditemukan untuk customer ini.');
        }

        $filePath = storage_path('app/public/ssl/' . $customer->ssl_file);
        
        if (!file_exists($filePath)) {
            throw new \Exception('File SSL tidak ditemukan.');
        }

        return response()->download($filePath, 'ssl_' . $customer->snd . '.crt');
    }

    public function clearCache(): void
    {
        Cache::forget('customer_filter_options');
    }
}