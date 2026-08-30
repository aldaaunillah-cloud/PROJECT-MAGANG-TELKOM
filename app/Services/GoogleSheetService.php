<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GoogleSheetService
{
    protected $service;
    protected $spreadsheetId;
    protected $range;

    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEETS_SPREADSHEET_ID');
        $this->range = env('GOOGLE_SHEETS_RANGE', 'DataAll!A1:AE');
        $this->service = $this->getGoogleSheetsService();
    }

    protected function getGoogleSheetsService()
    {
        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets Sync');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAccessType('offline');

        $paths = [
            storage_path('app/google-credentials.json'),
            storage_path('app/google-sheets/credentials.json'),
            base_path('google-credentials.json'),
        ];

        $credentialsPath = null;
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $credentialsPath = $path;
                break;
            }
        }

        if (!$credentialsPath) {
            Log::error('Google credentials not found in any path', ['paths' => $paths]);
            throw new \Exception('Google credentials file not found!');
        }

        Log::info('Using credentials from: ' . $credentialsPath);
        $client->setAuthConfig($credentialsPath);
        return new Sheets($client);
    }

    public function getRowCount(): int
    {
        try {
            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                'DataAll!A:A',
                ['valueRenderOption' => 'UNFORMATTED_VALUE']
            );
            $values = $response->getValues();
            return is_array($values) ? count($values) : 0;
        } catch (\Exception $e) {
            Log::error('Failed to fetch row count from Google Sheets: ' . $e->getMessage());
            return 0;
        }
    }

    public function getHeaders(): array
    {
        try {
            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                'DataAll!A1:AE1',
                ['valueRenderOption' => 'UNFORMATTED_VALUE']
            );
            $rows = $response->getValues();
            if (empty($rows)) {
                return [];
            }
            return array_map('trim', $rows[0]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch headers from Google Sheets: ' . $e->getMessage());
            return [];
        }
    }

    public function getDataWithHeaders(string $range, array $headers): array
    {
        try {
            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                $range,
                ['valueRenderOption' => 'UNFORMATTED_VALUE']
            );
            
            $rows = $response->getValues();
            if (empty($rows)) {
                return [];
            }

            $data = [];
            foreach ($rows as $row) {
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $header = trim($header);
                    $value = isset($row[$index]) ? trim($row[$index]) : '';
                    $key = $this->mapHeaderToKey($header);
                    if ($key) {
                        $rowData[$key] = $value;
                    }
                }
                $data[] = $rowData;
            }
            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to fetch data with headers from Google Sheets: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getData(?string $range = null): array
    {
        $targetRange = $range ?? $this->range;
        try {
            Log::info('Fetching data from Google Sheets', [
                'spreadsheet_id' => $this->spreadsheetId,
                'range' => $targetRange
            ]);

            $response = $this->service->spreadsheets_values->get(
                $this->spreadsheetId,
                $targetRange,
                ['valueRenderOption' => 'UNFORMATTED_VALUE']
            );
            
            $rows = $response->getValues();
            
            if (empty($rows)) {
                Log::warning('No data found in Google Sheets');
                return [];
            }

            Log::info('Total rows from Google Sheets: ' . count($rows));

            $headers = $rows[0];
            
            // LOG SEMUA HEADER UNTUK DEBUG
            Log::info('=== HEADER SPREADSHEET ===');
            foreach ($headers as $index => $header) {
                $header = trim($header);
                $mapped = $this->mapHeaderToKey($header);
                Log::info("Header[$index]: '$header' → mapped to: '" . ($mapped ?? 'NULL') . "'");
            }

            $data = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowData = [];
                
                foreach ($headers as $index => $header) {
                    $header = trim($header);
                    $value = isset($row[$index]) ? trim($row[$index]) : '';
                    
                    $key = $this->mapHeaderToKey($header);
                    if ($key) {
                        $rowData[$key] = $value;
                    }
                }
                
                if (!empty($rowData['snd'])) {
                    $data[] = $rowData;
                }
            }

            Log::info('Data loaded: ' . count($data) . ' rows');
            
            if (count($data) > 0) {
                Log::info('Sample data: ' . json_encode(array_slice($data, 0, 2)));
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Failed to get Google Sheets data: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    protected function mapHeaderToKey($header): ?string
    {
        $header = trim($header);
        $headerUpper = strtoupper($header);

        $map = [
            // Status
            'STATUS BAYAR' => 'status_bayar',
            'STATUS' => 'status_bayar',
            
            // Tagihan
            'TAG_INET' => 'tag_inet',
            'TAG INET' => 'tag_inet',
            'TAG_TLP' => 'tag_tlp',
            'TAG TLP' => 'tag_tlp',
            'TAG_TOTAL' => 'tag_total',
            'TAG TOTAL' => 'tag_total',
            'TOTAL' => 'tag_total',
            
            // Identitas
            'SND' => 'snd',
            'SDN' => 'snd',
            'SND_GROUP' => 'snd_group',
            'SDN_GROUP' => 'snd_group',
            'SND GROUP' => 'snd_group',
            'SDN GROUP' => 'snd_group',
            'NCLI' => 'ncli',
            'NAMA' => 'nama',
            'ALAMAT' => 'alamat',
            
            // Lokasi
            'STO' => 'sto',
            'DATEL' => 'datel',
            
            // Produk
            'PRODUK' => 'produk',
            
            // Keterangan
            'EKSEPSI_DESC' => 'eksepsi_desc',
            'EKSEPSI' => 'eksepsi_desc',
            'DESC_NEWBILL' => 'desc_newbill',
            'NEWBILL' => 'desc_newbill',
            'USAGE_DESC' => 'usage_desc',
            'USAGE' => 'usage_desc',
            
            // Keuangan
            'SALDO' => 'saldo',
            'UMUR_CUSTOMER' => 'umur_customer',
            'UMUR' => 'umur_customer',
            
            // Billing
            'BILLING KE-' => 'billing_ke',
            'BILLING KE' => 'billing_ke',
            'BILLING_KE' => 'billing_ke',
            'BILLING' => 'billing_ke',
            
            // Pembayaran
            'PAID_L11' => 'paid_l11',
            'PAID L11' => 'paid_l11',
            'TGL PAID' => 'tgl_paid',
            'TANGGAL PAID' => 'tgl_paid',
            'PAID_RP' => 'paid_rp',
            'PAID RP' => 'paid_rp',
            'PAID' => 'paid_rp',
            'COLL AGENT' => 'coll_agent',
            'COLLECTOR' => 'coll_agent',
            
            // Klaim
            'TGL KLAIM' => 'tgl_klaim',
            'TANGGAL KLAIM' => 'tgl_klaim',
            'AMOUNT KLAIM' => 'amount_klaim',
            'AMOUNT' => 'amount_klaim',
            'USER KLAIM' => 'user_klaim',
            'USER' => 'user_klaim',
            
            // Lainnya
            'TGL PAID N-1' => 'tgl_paid_n1',
            'PAID N-1' => 'tgl_paid_n1',
            'AGENCY PSB' => 'agency_psb',
            'SALES AGENCY' => 'sales_agency',
            'PPP' => 'ppp',
            'CARING MYBRAINS' => 'caring_mybrains',
            'CARING' => 'caring_mybrains',
            'AGENCY' => 'agency',
            'SALES' => 'sales',
            'SSL_FILE' => 'ssl_file',
        ];

        // Exact match
        foreach ($map as $key => $value) {
            if (strtoupper($key) === $headerUpper) {
                return $value;
            }
        }

        // Partial match
        foreach ($map as $key => $value) {
            if (strpos($headerUpper, strtoupper($key)) !== false) {
                return $value;
            }
            if (strpos(strtoupper($key), $headerUpper) !== false) {
                return $value;
            }
        }

        Log::warning('Unknown header: ' . $header);
        return null;
    }
}
