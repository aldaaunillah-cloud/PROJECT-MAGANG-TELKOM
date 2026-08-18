<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BillingDetailExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $billingKe;
    protected $datel;
    protected $agency;
    protected $sales;
    protected $status;

    public function __construct($billingKe, $datel = null, $agency = null, $sales = null, $status = null)
    {
        $this->billingKe = $billingKe;
        $this->datel = $datel;
        $this->agency = $agency;
        $this->sales = $sales;
        $this->status = $status;
    }

    public function query()
    {
        $query = Customer::query()->where('billing_ke', $this->billingKe);

        if ($this->datel) {
            $query->where('datel', $this->datel);
        }

        if ($this->agency) {
            $query->where('agency_psb', $this->agency);
        }

        if ($this->sales) {
            $query->where('sales_agency', $this->sales);
        }

        if ($this->status) {
            $query->where('status_bayar', $this->status);
        }

        return $query->orderBy('tag_total', 'DESC');
    }

    public function headings(): array
    {
        return [
            'No',
            'SND',
            'Nama Customer',
            'Datel',
            'Agency PSB',
            'Sales Agency',
            'Billing Ke',
            'Status Bayar',
            'Tagihan Total'
        ];
    }

    private $rowNumber = 0;

    public function map($customer): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $customer->snd,
            $customer->nama,
            $customer->datel,
            $customer->agency_psb,
            $customer->sales_agency,
            'B' . $customer->billing_ke,
            $customer->status_bayar == 'Sdh Bayar' ? 'Sudah Bayar' : 'Belum Bayar',
            $customer->tag_total
        ];
    }
}
