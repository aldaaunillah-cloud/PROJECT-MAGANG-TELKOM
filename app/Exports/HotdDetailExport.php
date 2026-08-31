<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HotdDetailExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithCustomValueBinder
{
    use Exportable;

    protected $billingKe;
    protected $datel;
    protected $agency;
    protected $sales;

    private $rowNumber = 0;

    public function __construct($billingKe, $datel, $agency = null, $sales = null)
    {
        $this->billingKe = $billingKe;
        $this->datel = $datel;
        $this->agency = $agency;
        $this->sales = $sales;
    }

    public function collection()
    {
        $query = Customer::query()
            ->where('status_bayar', '!=', 'Sdh Bayar');

        if (
            $this->billingKe &&
            $this->billingKe !== 'All' &&
            $this->billingKe !== 'Semua Billing' &&
            $this->billingKe !== 'all' &&
            $this->billingKe !== 'TOTAL' &&
            $this->billingKe != 0
        ) {
            $query->where('billing_ke', $this->billingKe);
        }

        if (
            $this->datel &&
            $this->datel !== 'Nasional' &&
            $this->datel !== 'Semua Datel' &&
            $this->datel !== 'Semua' &&
            $this->datel !== 'TOTAL'
        ) {
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

        $rawCustomers = $query
            ->orderBy('tag_total', 'DESC')
            ->get();

        return $rawCustomers
            ->groupBy(function ($customer) {
                $ncli = trim((string) $customer->ncli);

                if ($ncli !== '') {
                    return 'NCLI_' . $ncli;
                }

                return 'SND_' . $customer->snd;
            })
            ->map(function ($group) {
                $primaryCustomer = $group->first(function ($customer) {
                    return stripos((string) $customer->produk, 'internet') !== false;
                });

                if (!$primaryCustomer) {
                    $primaryCustomer = $group->first();
                }

                $customer = clone $primaryCustomer;

                // Ambil SND Group asli dari source/Spreadsheet jika ada
                // pada salah satu anggota NCLI yang sama.
                $groupSnd = $group
                    ->pluck('snd_group')
                    ->filter(function ($value) {
                        return $value !== null && trim((string) $value) !== '';
                    })
                    ->first();

                if ($groupSnd !== null && trim((string) $groupSnd) !== '') {
                    $customer->snd_group = $groupSnd;
                }

                // Jumlahkan nilai antar-SND pada NCLI yang sama.
                $customer->tag_total = $group->sum(function ($item) {
                    return (float) ($item->tag_total ?? 0);
                });

                $customer->tag_inet = $group->sum(function ($item) {
                    return (float) ($item->tag_inet ?? 0);
                });

                $customer->tag_tlp = $group->sum(function ($item) {
                    return (float) ($item->tag_tlp ?? 0);
                });

                $customer->saldo = $group->sum(function ($item) {
                    return (float) ($item->saldo ?? 0);
                });

                return $customer;
            })
            ->sortByDesc('tag_total')
            ->values();
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

    public function map($customer): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $customer->status_bayar,
            (string) $customer->snd,
            $customer->snd_group !== null && trim((string) $customer->snd_group) !== ''
                ? (string) $customer->snd_group
                : '',
            $customer->ncli !== null ? (string) $customer->ncli : '',
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

    /**
     * Paksa kolom SND, SND Group, dan NCLI menjadi string Excel.
     * Ini mencegah Excel mengubah SND menjadi scientific notation (1,31E+11).
     */
    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['C', 'D', 'E'], true)) {
            $cell->setValueExplicit(
                $value === null ? '' : (string) $value,
                DataType::TYPE_STRING
            );

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
