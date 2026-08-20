<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class HotdDetailExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $billingKe;
    protected $datel;
    protected $agency;
    protected $sales;

    public function __construct($billingKe, $datel, $agency = null, $sales = null)
    {
        $this->billingKe = $billingKe;
        $this->datel = $datel;
        $this->agency = $agency;
        $this->sales = $sales;
    }

    public function query()
    {
        $query = Customer::query()
            ->where('status_bayar', '!=', 'Sdh Bayar');

        if ($this->billingKe && $this->billingKe !== 'All' && $this->billingKe !== 'Semua Billing' && $this->billingKe !== 'all' && $this->billingKe !== 'TOTAL' && $this->billingKe != 0) {
            $query->where('billing_ke', $this->billingKe);
        }

        if ($this->datel && $this->datel !== 'Nasional' && $this->datel !== 'Semua Datel' && $this->datel !== 'Semua' && $this->datel !== 'TOTAL') {
            $query->where('datel', $this->datel);
        }

        if ($this->agency) {
            $agency = $this->agency;
            $query->where(function ($q) use ($agency) {
                $q->where('agency_psb', $agency)
                  ->orWhere('agency', $agency);
            });
        }

        if ($this->sales) {
            $sales = $this->sales;
            $query->where(function ($q) use ($sales) {
                $q->where('sales_agency', $sales)
                  ->orWhere('sales', $sales);
            });
        }

        return $query->orderBy('tag_total', 'DESC');
    }

    public function headings(): array
    {
        return [
            'No',
            'Status',
            'SND',
            'SND Group',
            'NCLI',
            'Nama',
            'Alamat',
            'STO',
            'Datel',
            'Produk',
            'Eksepsi',
            'New Bill',
            'Usage',
            'Tagihan Total',
            'Saldo',
            'Umur',
            'Billing',
            'Paid L11',
            'Tgl Paid',
            'Paid Rp',
            'Coll Agent',
            'Tgl Klaim',
            'Amount Klaim',
            'User Klaim',
            'Tgl Paid N-1',
            'Agency PSB',
            'Sales Agency',
            'PPP',
            'Caring'
        ];
    }

    private $rowNumber = 0;

    public function map($customer): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $customer->status_bayar,
            $customer->snd,
            $customer->snd_group,
            $customer->ncli,
            $customer->nama,
            $customer->alamat,
            $customer->sto,
            $customer->datel,
            $customer->produk,
            $customer->eksepsi_desc,
            $customer->desc_newbill,
            $customer->usage_desc,
            $customer->tag_total,
            $customer->saldo,
            $customer->umur_customer,
            $customer->billing_ke,
            $customer->paid_l11,
            $customer->tgl_paid,
            $customer->paid_rp,
            $customer->coll_agent,
            $customer->tgl_klaim,
            $customer->amount_klaim,
            $customer->user_klaim,
            $customer->tgl_paid_n1,
            $customer->agency_psb,
            $customer->sales_agency,
            $customer->ppp,
            $customer->caring_mybrains
        ];
    }
}
