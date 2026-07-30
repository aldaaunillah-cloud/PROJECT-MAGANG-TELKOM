<?php

namespace App\Services;

use App\Models\Customer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportService
{
    public function exportCustomersExcel(Request $request)
    {
        $customers = $this->getExportData($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = ['No', 'SND', 'Nama', 'Alamat', 'Datel', 'Agency', 'Sales', 'Billing Ke', 'Status Bayar', 'Tagihan', 'Saldo', 'Tgl Klaim', 'Tgl Paid', 'Tgl Paid N-1'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E2001A');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }

        $row = 2;
        foreach ($customers as $index => $customer) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $customer->snd ?? '');
            $sheet->setCellValue('C' . $row, $customer->nama ?? '');
            $sheet->setCellValue('D' . $row, $customer->alamat ?? '');
            $sheet->setCellValue('E' . $row, $customer->datel ?? '');
            $sheet->setCellValue('F' . $row, $customer->agency ?? '');
            $sheet->setCellValue('G' . $row, $customer->sales ?? '');
            $sheet->setCellValue('H' . $row, $customer->billing_ke ?? '');
            $sheet->setCellValue('I' . $row, $customer->status_bayar ?? '');
            $sheet->setCellValue('J' . $row, $customer->tag_total ?? 0);
            $sheet->setCellValue('K' . $row, $customer->saldo ?? 0);
            $sheet->setCellValue('L' . $row, $customer->tgl_klaim ?? '');
            $sheet->setCellValue('M' . $row, $customer->tgl_paid ?? '');
            $sheet->setCellValue('N' . $row, $customer->tgl_paid_n1 ?? '');

            // Status color - FIXED!
            $statusColor = ($customer->status_bayar ?? '') == 'Sdh Bayar' ? '92D050' : 'FF0000';
            $sheet->getStyle('I' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statusColor);
            $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('FFFFFF');

            $row++;
        }

        // Auto size
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);
        $filename = 'Data_Customer_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = storage_path('app/public/' . $filename);
        $writer->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }

    public function exportCustomersPdf(Request $request)
    {
        $customers = $this->getExportData($request);

        $data = [
            'customers' => $customers,
            'total' => $customers->count(),
            'title' => 'Laporan Data Customer',
            'company' => 'Telkom Indonesia',
        ];

        $pdf = Pdf::loadView('exports.customers-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Data_Customer_' . date('Y-m-d_His') . '.pdf');
    }

    protected function getExportData(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('snd', 'like', "%{$search}%")
                  ->orWhere('sales', 'like', "%{$search}%");
            });
        }

        if ($request->filled('agency')) {
            $query->where('agency', $request->agency);
        }

        if ($request->filled('sales')) {
            $query->where('sales', $request->sales);
        }

        return $query->limit(10000)->get();
    }
}