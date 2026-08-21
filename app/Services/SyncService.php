<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SyncService
{
    protected GoogleSheetService $googleSheetService;
    protected int $chunkSize = 1000;

    public function __construct(?GoogleSheetService $googleSheetService = null)
    {
        $this->googleSheetService = $googleSheetService ?? new GoogleSheetService();
    }

    public function syncFromGoogleSheets(): array
    {
        // Tingkatkan limit waktu eksekusi dan memori untuk memproses data besar
        ini_set('max_execution_time', 300);
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $startTime = microtime(true);

        try {
            $googleCustomers = $this->googleSheetService->getData();

            if (empty($googleCustomers)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ada data dari Google Sheets.',
                    'data' => []
                ];
            }

            Log::info('Google Sheet berhasil dibaca', ['rows' => count($googleCustomers)]);

            // Hapus duplikat data customer di database agar datanya bersih
            DB::statement("
                DELETE FROM customers 
                WHERE id NOT IN (
                    SELECT max_id FROM (
                        SELECT MAX(id) as max_id 
                        FROM customers 
                        GROUP BY snd
                    ) as tmp
                )
            ");

            if (!empty($googleCustomers)) {
                Log::info('=== FIRST GOOGLE CUSTOMER ===', ['data' => $googleCustomers[0]]);
            }

            // Ambil data customer yang ada untuk perbandingan (hanya kolom penting untuk menghemat memori)
            $dbCustomers = Customer::select([
                'snd', 'nama', 'alamat', 'datel', 'agency', 'sales',
                'billing_ke', 'saldo', 'status_bayar', 'tag_total',
                'tag_inet', 'tag_tlp', 'snd_group', 'ncli', 'sto',
                'produk', 'eksepsi_desc', 'desc_newbill', 'usage_desc',
                'umur_customer', 'paid_l11', 'tgl_paid', 'paid_rp',
                'coll_agent', 'tgl_klaim', 'amount_klaim', 'user_klaim',
                'tgl_paid_n1', 'agency_psb', 'sales_agency', 'ppp',
                'caring_mybrains', 'ssl_file'
            ])->get()->keyBy('snd');

            $skip = 0;
            $insertedCount = 0;
            $updatedCount = 0;
            $now = now();

            // Bagi data Google Sheets menjadi chunk 2000 baris agar tidak memakan RAM
            $chunks = array_chunk($googleCustomers, 2000);
            unset($googleCustomers); // Bebaskan memori array utama yang sangat besar

            DB::transaction(function () use ($chunks, $dbCustomers, $now, &$insertedCount, &$updatedCount, &$skip) {
                foreach ($chunks as $chunk) {
                    $insertData = [];

                    foreach ($chunk as $customer) {
                        $snd = trim($customer['snd'] ?? '');
                        if (empty($snd)) {
                            $skip++;
                            continue;
                        }

                        $row = [
                            'snd' => $snd,
                            'snd_group' => trim($customer['snd_group'] ?? ''),
                            'ncli' => trim($customer['ncli'] ?? ''),
                            'nama' => trim($customer['nama'] ?? ''),
                            'alamat' => trim($customer['alamat'] ?? ''),
                            'sto' => trim($customer['sto'] ?? ''),
                            'datel' => trim($customer['datel'] ?? ''),
                            'agency' => trim($customer['agency'] ?? ''),
                            'sales' => trim($customer['sales'] ?? ''),
                            'billing_ke' => $this->parseBillingKe($customer['billing_ke'] ?? null),
                            'saldo' => $this->parseCurrency($customer['saldo'] ?? 0),
                            'status_bayar' => trim($customer['status_bayar'] ?? ''),
                            'tag_total' => $this->parseCurrency($customer['tag_total'] ?? 0),
                            'tag_inet' => $this->parseCurrency($customer['tag_inet'] ?? 0),
                            'tag_tlp' => $this->parseCurrency($customer['tag_tlp'] ?? 0),
                            'produk' => trim($customer['produk'] ?? ''),
                            'eksepsi_desc' => trim($customer['eksepsi_desc'] ?? ''),
                            'desc_newbill' => trim($customer['desc_newbill'] ?? ''),
                            'usage_desc' => trim($customer['usage_desc'] ?? ''),
                            'umur_customer' => (int) ($customer['umur_customer'] ?? 0),
                            'paid_l11' => trim($customer['paid_l11'] ?? ''),
                            'tgl_paid' => $this->parseDate($customer['tgl_paid'] ?? null),
                            'paid_rp' => $this->parseCurrency($customer['paid_rp'] ?? 0),
                            'coll_agent' => trim($customer['coll_agent'] ?? ''),
                            'tgl_klaim' => $this->parseDate($customer['tgl_klaim'] ?? null),
                            'amount_klaim' => $this->parseCurrency($customer['amount_klaim'] ?? 0),
                            'user_klaim' => trim($customer['user_klaim'] ?? ''),
                            'tgl_paid_n1' => $this->parseDate($customer['tgl_paid_n1'] ?? null),
                            'agency_psb' => trim($customer['agency_psb'] ?? ''),
                            'sales_agency' => trim($customer['sales_agency'] ?? ''),
                            'ppp' => trim($customer['ppp'] ?? ''),
                            'caring_mybrains' => trim($customer['caring_mybrains'] ?? ''),
                            'ssl_file' => trim($customer['ssl_file'] ?? ''),
                            'updated_at' => $now,
                        ];

                        if (!isset($dbCustomers[$snd])) {
                            $row['created_at'] = $now;
                            $insertData[] = $row;
                            $insertedCount++;
                        } else {
                            $existing = $dbCustomers[$snd];
                            $existingHash = $this->generateHash($existing);
                            $newHash = $this->generateHash($row);
                            
                            if ($existingHash !== $newHash) {
                                // Jangan overwrite ssl_file milik customer jika di spreadsheet kosong
                                if (empty($row['ssl_file'])) {
                                    unset($row['ssl_file']);
                                }
                                Customer::where('snd', $snd)->update($row);
                                $updatedCount++;
                            } else {
                                $skip++;
                            }
                        }
                    }

                    // Insert data baru dalam batch chunk
                    if (!empty($insertData)) {
                        Customer::insert($insertData);
                    }

                    // Bersihkan memori variabel penampung chunk
                    unset($insertData);
                }
            });

            $this->clearCache();

            $duration = round(microtime(true) - $startTime, 2);
            $totalCustomer = Customer::count();
            $tagTotalSum = Customer::sum('tag_total');

            Log::info('=== AFTER SYNC ===', [
                'total_customer' => $totalCustomer,
                'tag_total_sum' => $tagTotalSum,
                'insert' => count($insertData),
                'update' => count($updateData),
                'skip' => $skip
            ]);

            return [
                'success' => true,
                'message' => 'Sinkronisasi berhasil!',
                'data' => [
                    'google_rows' => count($googleCustomers),
                    'insert' => count($insertData),
                    'update' => count($updateData),
                    'skip' => $skip,
                    'total_customer' => $totalCustomer,
                    'tag_total_sum' => number_format($tagTotalSum, 0, ',', '.'),
                    'duration' => $duration . ' detik'
                ]
            ];

        } catch (\Throwable $e) {
            Log::error('Sinkronisasi gagal', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [
                'success' => false,
                'message' => 'Sinkronisasi gagal: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function syncBatch(int $batchIndex, int $startRow, int $endRow, array $headers): array
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $startTime = microtime(true);
        $range = "DataAll!A{$startRow}:AE{$endRow}";

        try {
            $googleCustomers = $this->googleSheetService->getDataWithHeaders($range, $headers);

            if (empty($googleCustomers)) {
                return [
                    'success' => true,
                    'message' => 'Tidak ada data di range ini.',
                    'data' => [
                        'inserted' => 0,
                        'updated' => 0,
                        'skip' => 0
                    ]
                ];
            }

            // Hapus duplikat data customer di database jika batch = 1
            if ($batchIndex === 1) {
                DB::statement("
                    DELETE FROM customers 
                    WHERE id NOT IN (
                        SELECT max_id FROM (
                            SELECT MAX(id) as max_id 
                            FROM customers 
                            GROUP BY snd
                        ) as tmp
                    )
                ");
            }

            // Ambil data customer yang ada di database untuk perbandingan (hanya untuk SND yang ada di batch saat ini)
            $sndsInBatch = array_map(function($customer) {
                return trim($customer['snd'] ?? '');
            }, $googleCustomers);
            $sndsInBatch = array_filter($sndsInBatch);

            $dbCustomers = Customer::select([
                'snd', 'nama', 'alamat', 'datel', 'agency', 'sales',
                'billing_ke', 'saldo', 'status_bayar', 'tag_total',
                'tag_inet', 'tag_tlp', 'snd_group', 'ncli', 'sto',
                'produk', 'eksepsi_desc', 'desc_newbill', 'usage_desc',
                'umur_customer', 'paid_l11', 'tgl_paid', 'paid_rp',
                'coll_agent', 'tgl_klaim', 'amount_klaim', 'user_klaim',
                'tgl_paid_n1', 'agency_psb', 'sales_agency', 'ppp',
                'caring_mybrains', 'ssl_file'
            ])->whereIn('snd', $sndsInBatch)->get()->keyBy('snd');

            $skip = 0;
            $insertedCount = 0;
            $updatedCount = 0;
            $now = now();
            $insertData = [];

            DB::transaction(function () use ($googleCustomers, $dbCustomers, $now, &$insertData, &$insertedCount, &$updatedCount, &$skip) {
                foreach ($googleCustomers as $customer) {
                    $snd = trim($customer['snd'] ?? '');
                    if (empty($snd)) {
                        $skip++;
                        continue;
                    }

                    $row = [
                        'snd' => $snd,
                        'snd_group' => trim($customer['snd_group'] ?? ''),
                        'ncli' => trim($customer['ncli'] ?? ''),
                        'nama' => trim($customer['nama'] ?? ''),
                        'alamat' => trim($customer['alamat'] ?? ''),
                        'sto' => trim($customer['sto'] ?? ''),
                        'datel' => trim($customer['datel'] ?? ''),
                        'agency' => trim($customer['agency'] ?? ''),
                        'sales' => trim($customer['sales'] ?? ''),
                        'billing_ke' => $this->parseBillingKe($customer['billing_ke'] ?? null),
                        'saldo' => $this->parseCurrency($customer['saldo'] ?? 0),
                        'status_bayar' => trim($customer['status_bayar'] ?? ''),
                        'tag_total' => $this->parseCurrency($customer['tag_total'] ?? 0),
                        'tag_inet' => $this->parseCurrency($customer['tag_inet'] ?? 0),
                        'tag_tlp' => $this->parseCurrency($customer['tag_tlp'] ?? 0),
                        'produk' => trim($customer['produk'] ?? ''),
                        'eksepsi_desc' => trim($customer['eksepsi_desc'] ?? ''),
                        'desc_newbill' => trim($customer['desc_newbill'] ?? ''),
                        'usage_desc' => trim($customer['usage_desc'] ?? ''),
                        'umur_customer' => (int) ($customer['umur_customer'] ?? 0),
                        'paid_l11' => trim($customer['paid_l11'] ?? ''),
                        'tgl_paid' => $this->parseDate($customer['tgl_paid'] ?? null),
                        'paid_rp' => $this->parseCurrency($customer['paid_rp'] ?? 0),
                        'coll_agent' => trim($customer['coll_agent'] ?? ''),
                        'tgl_klaim' => $this->parseDate($customer['tgl_klaim'] ?? null),
                        'amount_klaim' => $this->parseCurrency($customer['amount_klaim'] ?? 0),
                        'user_klaim' => trim($customer['user_klaim'] ?? ''),
                        'tgl_paid_n1' => $this->parseDate($customer['tgl_paid_n1'] ?? null),
                        'agency_psb' => trim($customer['agency_psb'] ?? ''),
                        'sales_agency' => trim($customer['sales_agency'] ?? ''),
                        'ppp' => trim($customer['ppp'] ?? ''),
                        'caring_mybrains' => trim($customer['caring_mybrains'] ?? ''),
                        'ssl_file' => trim($customer['ssl_file'] ?? ''),
                        'updated_at' => $now,
                    ];

                    if (!isset($dbCustomers[$snd])) {
                        $row['created_at'] = $now;
                        $insertData[] = $row;
                        $insertedCount++;
                    } else {
                        $existing = $dbCustomers[$snd];
                        $existingHash = $this->generateHash($existing);
                        $newHash = $this->generateHash($row);
                        
                        if ($existingHash !== $newHash) {
                            if (empty($row['ssl_file'])) {
                                unset($row['ssl_file']);
                            }
                            Customer::where('snd', $snd)->update($row);
                            $updatedCount++;
                        } else {
                            $skip++;
                        }
                    }
                }

                if (!empty($insertData)) {
                    foreach (array_chunk($insertData, 1000) as $chunk) {
                        Customer::insert($chunk);
                    }
                }
            });

            $duration = round(microtime(true) - $startTime, 2);

            return [
                'success' => true,
                'message' => "Batch {$batchIndex} berhasil diproses.",
                'data' => [
                    'inserted' => $insertedCount,
                    'updated' => $updatedCount,
                    'skip' => $skip,
                    'duration' => $duration . ' detik'
                ]
            ];

        } catch (\Throwable $e) {
            Log::error("Sinkronisasi Batch {$batchIndex} gagal", ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => "Batch {$batchIndex} gagal: " . $e->getMessage(),
                'data' => []
            ];
        }
    }

    protected function generateHash($data): string
    {
        if (is_object($data)) {
            $data = $data->toArray();
        }
        
        $compareFields = [
            'snd', 'snd_group', 'ncli', 'nama', 'alamat', 'sto',
            'datel', 'agency', 'sales', 'billing_ke', 'saldo', 'status_bayar',
            'tag_total', 'tag_inet', 'tag_tlp', 'produk', 'eksepsi_desc',
            'desc_newbill', 'usage_desc', 'umur_customer', 'paid_l11', 'tgl_paid',
            'paid_rp', 'coll_agent', 'tgl_klaim', 'amount_klaim', 'user_klaim',
            'tgl_paid_n1', 'agency_psb', 'sales_agency', 'ppp', 'caring_mybrains', 'ssl_file'
        ];
        
        $normalized = [];
        foreach ($compareFields as $field) {
            $val = $data[$field] ?? null;
            if (is_numeric($val)) {
                $val = (string) round((float) $val, 2);
            } elseif ($val === null) {
                $val = '';
            } else {
                $val = trim((string) $val);
            }
            $normalized[$field] = $val;
        }
        
        ksort($normalized);
        
        return md5(json_encode($normalized));
    }

    protected function parseBillingKe($value): ?int
    {
        if (empty($value)) return 0;
        
        if (is_numeric($value)) {
            $num = (int) $value;
            return ($num >= 1 && $num <= 6) ? $num : 0;
        }
        
        if (is_string($value)) {
            // Cari pola "Billing ke 1" atau "Billing ke-1"
            if (preg_match('/Billing\s*ke\s*[-]?\s*(\d+)/i', $value, $match)) {
                $num = (int) $match[1];
                return ($num >= 1 && $num <= 6) ? $num : 0;
            }
            // Cari pola "Billing 1"
            if (preg_match('/Billing\s*(\d+)/i', $value, $match)) {
                $num = (int) $match[1];
                return ($num >= 1 && $num <= 6) ? $num : 0;
            }
            // Jika value angka murni
            if (is_numeric(trim($value))) {
                $num = (int) trim($value);
                return ($num >= 1 && $num <= 6) ? $num : 0;
            }
        }
        
        return 0;
    }

    protected function parseCurrency($value): float
    {
        if (empty($value)) return 0.0;
        
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Hapus karakter 'Rp', spasi, dll
        $value = preg_replace('/[^0-9.,-]/', '', $value);
        
        // Hapus titik sebagai ribuan (format Indonesia)
        $value = str_replace('.', '', $value);
        // Ganti koma dengan titik (sebagai desimal)
        $value = str_replace(',', '.', $value);
        
        return (float) $value;
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) return null;
        
        $value = trim($value);
        
        try {
            if (is_numeric($value)) {
                // Excel/Google Sheets serial date (e.g. 44500)
                $unixDate = ($value - 25569) * 86400;
                return gmdate("Y-m-d", $unixDate);
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }
            
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/', $value, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = strlen($matches[3]) == 2 ? '20' . $matches[3] : $matches[3];
                return $year . '-' . $month . '-' . $day;
            }
            
            if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2,4})$/', $value, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                $year = strlen($matches[3]) == 2 ? '20' . $matches[3] : $matches[3];
                return $year . '-' . $month . '-' . $day;
            }
            
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function syncFromExcel($file): array
    {
        $startTime = microtime(true);

        try {
            // Load Excel sheet data
            $sheets = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
            if (empty($sheets) || empty($sheets[0])) {
                return [
                    'success' => false,
                    'message' => 'File Excel kosong atau tidak terbaca.',
                    'data' => []
                ];
            }

            // Target the first sheet
            $rows = $sheets[0];
            $headers = array_map(function($h) {
                return $h !== null ? trim((string)$h) : '';
            }, $rows[0]);

            $excelCustomers = [];
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowData = [];
                
                foreach ($headers as $index => $header) {
                    if (empty($header)) continue;
                    $value = isset($row[$index]) ? trim((string)$row[$index]) : '';
                    
                    $key = $this->mapHeaderToKey($header);
                    if ($key) {
                        $rowData[$key] = $value;
                    }
                }
                
                if (!empty($rowData['snd'])) {
                    $excelCustomers[] = $rowData;
                }
            }

            if (empty($excelCustomers)) {
                return [
                    'success' => false,
                    'message' => 'Tidak ditemukan baris data customer yang valid (kolom SND wajib terisi).',
                    'data' => []
                ];
            }

            // Hapus duplikat data customer yang ada di database agar datanya bersih
            DB::statement("
                DELETE FROM customers 
                WHERE id NOT IN (
                    SELECT max_id FROM (
                        SELECT MAX(id) as max_id 
                        FROM customers 
                        GROUP BY snd
                    ) as tmp
                )
            ");

            $dbCustomers = Customer::select([
                'snd', 'nama', 'alamat', 'datel', 'agency', 'sales',
                'billing_ke', 'saldo', 'status_bayar', 'tag_total',
                'tag_inet', 'tag_tlp', 'snd_group', 'ncli', 'sto',
                'produk', 'eksepsi_desc', 'desc_newbill', 'usage_desc',
                'umur_customer', 'paid_l11', 'tgl_paid', 'paid_rp',
                'coll_agent', 'tgl_klaim', 'amount_klaim', 'user_klaim',
                'tgl_paid_n1', 'agency_psb', 'sales_agency', 'ppp',
                'caring_mybrains', 'ssl_file'
            ])->get()->keyBy('snd');

            $insertData = [];
            $updateData = [];
            $skip = 0;
            $now = now();

            foreach ($excelCustomers as $customer) {
                $snd = trim($customer['snd'] ?? '');
                if (empty($snd)) {
                    $skip++;
                    continue;
                }

                $row = [
                    'snd' => $snd,
                    'snd_group' => trim($customer['snd_group'] ?? ''),
                    'ncli' => trim($customer['ncli'] ?? ''),
                    'nama' => trim($customer['nama'] ?? ''),
                    'alamat' => trim($customer['alamat'] ?? ''),
                    'sto' => trim($customer['sto'] ?? ''),
                    'datel' => trim($customer['datel'] ?? ''),
                    'agency' => trim($customer['agency'] ?? ''),
                    'sales' => trim($customer['sales'] ?? ''),
                    'billing_ke' => $this->parseBillingKe($customer['billing_ke'] ?? null),
                    'saldo' => $this->parseCurrency($customer['saldo'] ?? 0),
                    'status_bayar' => trim($customer['status_bayar'] ?? ''),
                    'tag_total' => $this->parseCurrency($customer['tag_total'] ?? 0),
                    'tag_inet' => $this->parseCurrency($customer['tag_inet'] ?? 0),
                    'tag_tlp' => $this->parseCurrency($customer['tag_tlp'] ?? 0),
                    'produk' => trim($customer['produk'] ?? ''),
                    'eksepsi_desc' => trim($customer['eksepsi_desc'] ?? ''),
                    'desc_newbill' => trim($customer['desc_newbill'] ?? ''),
                    'usage_desc' => trim($customer['usage_desc'] ?? ''),
                    'umur_customer' => (int) ($customer['umur_customer'] ?? 0),
                    'paid_l11' => trim($customer['paid_l11'] ?? ''),
                    'tgl_paid' => $this->parseDate($customer['tgl_paid'] ?? null),
                    'paid_rp' => $this->parseCurrency($customer['paid_rp'] ?? 0),
                    'coll_agent' => trim($customer['coll_agent'] ?? ''),
                    'tgl_klaim' => $this->parseDate($customer['tgl_klaim'] ?? null),
                    'amount_klaim' => $this->parseCurrency($customer['amount_klaim'] ?? 0),
                    'user_klaim' => trim($customer['user_klaim'] ?? ''),
                    'tgl_paid_n1' => $this->parseDate($customer['tgl_paid_n1'] ?? null),
                    'agency_psb' => trim($customer['agency_psb'] ?? ''),
                    'sales_agency' => trim($customer['sales_agency'] ?? ''),
                    'ppp' => trim($customer['ppp'] ?? ''),
                    'caring_mybrains' => trim($customer['caring_mybrains'] ?? ''),
                    'ssl_file' => trim($customer['ssl_file'] ?? ''),
                    'updated_at' => $now,
                ];

                if (!isset($dbCustomers[$snd])) {
                    $row['created_at'] = $now;
                    $insertData[] = $row;
                } else {
                    $existing = $dbCustomers[$snd];
                    $existingHash = $this->generateHash($existing);
                    $newHash = $this->generateHash($row);
                    
                    if ($existingHash !== $newHash) {
                        $updateData[] = $row;
                    } else {
                        $skip++;
                    }
                }
            }

            DB::transaction(function () use ($insertData, $updateData) {
                if (!empty($insertData)) {
                    foreach (array_chunk($insertData, $this->chunkSize) as $chunk) {
                        Customer::insert($chunk);
                    }
                }

                if (!empty($updateData)) {
                    foreach ($updateData as $row) {
                        if (empty($row['ssl_file'])) {
                            unset($row['ssl_file']);
                        }
                        Customer::where('snd', $row['snd'])->update($row);
                    }
                }
            });

            $this->clearCache();

            $duration = round(microtime(true) - $startTime, 2);
            $totalCustomer = Customer::count();
            $tagTotalSum = Customer::sum('tag_total');

            return [
                'success' => true,
                'message' => 'Sinkronisasi Excel berhasil!',
                'data' => [
                    'google_rows' => count($excelCustomers),
                    'insert' => count($insertData),
                    'update' => count($updateData),
                    'skip' => $skip,
                    'total_customer' => $totalCustomer,
                    'tag_total_sum' => number_format($tagTotalSum, 0, ',', '.'),
                    'duration' => $duration . ' detik'
                ]
            ];

        } catch (\Throwable $e) {
            Log::error('Sinkronisasi Excel gagal: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Sinkronisasi Excel gagal: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    protected function mapHeaderToKey($header): ?string
    {
        $header = trim($header);
        $headerUpper = strtoupper($header);

        $map = [
            'STATUS BAYAR' => 'status_bayar',
            'STATUS' => 'status_bayar',
            'TAG_INET' => 'tag_inet',
            'TAG INET' => 'tag_inet',
            'TAG_TLP' => 'tag_tlp',
            'TAG TLP' => 'tag_tlp',
            'TAG_TOTAL' => 'tag_total',
            'TAG TOTAL' => 'tag_total',
            'TOTAL' => 'tag_total',
            'SND' => 'snd',
            'SND_GROUP' => 'snd_group',
            'SND GROUP' => 'snd_group',
            'NCLI' => 'ncli',
            'NAMA' => 'nama',
            'ALAMAT' => 'alamat',
            'STO' => 'sto',
            'DATEL' => 'datel',
            'PRODUK' => 'produk',
            'EKSEPSI_DESC' => 'eksepsi_desc',
            'EKSEPSI' => 'eksepsi_desc',
            'DESC_NEWBILL' => 'desc_newbill',
            'NEWBILL' => 'desc_newbill',
            'USAGE_DESC' => 'usage_desc',
            'USAGE' => 'usage_desc',
            'SALDO' => 'saldo',
            'UMUR_CUSTOMER' => 'umur_customer',
            'UMUR' => 'umur_customer',
            'BILLING KE-' => 'billing_ke',
            'BILLING KE' => 'billing_ke',
            'BILLING_KE' => 'billing_ke',
            'BILLING' => 'billing_ke',
            'PAID_L11' => 'paid_l11',
            'PAID L11' => 'paid_l11',
            'TGL PAID' => 'tgl_paid',
            'TANGGAL PAID' => 'tgl_paid',
            'PAID_RP' => 'paid_rp',
            'PAID RP' => 'paid_rp',
            'PAID' => 'paid_rp',
            'COLL AGENT' => 'coll_agent',
            'COLLECTOR' => 'coll_agent',
            'TGL KLAIM' => 'tgl_klaim',
            'TANGGAL KLAIM' => 'tgl_klaim',
            'AMOUNT KLAIM' => 'amount_klaim',
            'AMOUNT' => 'amount_klaim',
            'USER KLAIM' => 'user_klaim',
            'USER' => 'user_klaim',
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

        foreach ($map as $key => $value) {
            if (strtoupper($key) === $headerUpper) {
                return $value;
            }
        }

        foreach ($map as $key => $value) {
            if (strpos($headerUpper, strtoupper($key)) !== false) {
                return $value;
            }
            if (strpos(strtoupper($key), $headerUpper) !== false) {
                return $value;
            }
        }

        return null;
    }

    protected function clearCache(): void
    {
        Cache::flush();
        
        try {
            if (class_exists(\App\Services\DashboardService::class)) {
                app(\App\Services\DashboardService::class)->clearCache();
            }
            if (class_exists(\App\Services\CustomerService::class)) {
                app(\App\Services\CustomerService::class)->clearCache();
            }
        } catch (\Exception $e) {
            // Ignore
        }
    }

    public function getSyncStatus(): array
    {
        $totalCustomer = Customer::count();
        $lastUpdate = Customer::max('updated_at');

        if ($lastUpdate) {
            $carbon = \Carbon\Carbon::parse($lastUpdate);
            return [
                'total_customer' => $totalCustomer,
                'last_update' => $carbon->format('Y-m-d H:i:s'),
                'last_update_human' => $carbon->diffForHumans(),
            ];
        }

        return [
            'total_customer' => $totalCustomer,
            'last_update' => 'Never',
            'last_update_human' => 'Never',
        ];
    }
}