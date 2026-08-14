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
            $query->where('agency_psb', $request->agency);
        }

        if ($request->filled('sales')) {
            $query->where('sales_agency', $request->sales);
        }

        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        $customers = $query->paginate(30)->withQueryString();

        $agencies = Customer::where('billing_ke', $billing_ke)
            ->whereNotNull('agency_psb')
            ->where('agency_psb', '!=', '')
            ->select('agency_psb as agency_val')
            ->distinct()
            ->orderBy('agency_val')
            ->pluck('agency_val');

        $statuses = ['Sdh Bayar', 'Blm Bayar'];

        return view('billing-detail', compact('customers', 'billing_ke', 'agencies', 'statuses'));
    }
}