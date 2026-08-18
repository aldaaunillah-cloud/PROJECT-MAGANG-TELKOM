<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

    public function exportExcel(Request $request, $billing_ke)
    {
        $datel = $request->datel;
        $agency = $request->agency;
        $sales = $request->sales;
        $status = $request->status;

        $fileName = 'Detail_Billing_' . $billing_ke;
        if ($datel) $fileName .= '_' . str_replace(' ', '_', $datel);
        if ($agency) $fileName .= '_' . str_replace(' ', '_', $agency);
        $fileName .= '.xlsx';

        return Excel::download(new \App\Exports\BillingDetailExport($billing_ke, $datel, $agency, $sales, $status), $fileName);
    }

    public function printPdf(Request $request, $billing_ke)
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

        $customers = $query->orderBy('tag_total', 'DESC')->get();

        return view('customers.print_billing_detail', compact('customers', 'billing_ke'));
    }
}