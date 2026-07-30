<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Customer([
            'snd' => $row['snd'],
            'nama' => $row['nama'],
            'alamat' => $row['alamat'],
            'datel' => $row['datel'],
            'agency' => $row['agency_psb'],
            'sales' => $row['sales_agency'],
            'billing_ke' => $row['billing_ke'],
            'saldo' => $row['saldo'],
            'status_bayar' => $row['status_bayar'],
        ]);
    }
}