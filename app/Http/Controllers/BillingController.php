<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function detail(Request $request, $billing_ke)
    {
        $query = Customer::where('billing_ke', $billing_ke);

        if ($request->filled('agency')) {
            $query->where('agency', $request->agency);
        }

        if ($request->filled('status')) {
            $query->where('status_bayar', $request->status);
        }

        $customers = $query->paginate(30)->withQueryString();

        $agencies = Customer::where('billing_ke', $billing_ke)
            ->whereNotNull('agency')
            ->distinct()
            ->pluck('agency');

        $statuses = ['Sdh Bayar', 'Blm Bayar'];

        return view('billing-detail', compact('customers', 'billing_ke', 'agencies', 'statuses'));
    }
}