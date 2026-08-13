<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function detail(Request $request, $billing_ke)
    {
        $query = Customer::where('billing_ke', $billing_ke);

        if ($request->filled('datel')) {
            $query->where('datel', $request->datel);
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

        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        $customers = $query->paginate(30)->withQueryString();

        $agencies = Customer::where('billing_ke', $billing_ke)
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('agency_psb')->where('agency_psb', '!=', '');
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('agency')->where('agency', '!=', '');
                });
            })
            ->select(\DB::raw("COALESCE(NULLIF(agency_psb, ''), agency) as agency_val"))
            ->distinct()
            ->orderBy('agency_val')
            ->pluck('agency_val');

        $statuses = ['Sdh Bayar', 'Blm Bayar'];

        return view('billing-detail', compact('customers', 'billing_ke', 'agencies', 'statuses'));
    }
}