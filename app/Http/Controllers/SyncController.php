<?php

namespace App\Http\Controllers;

use App\Services\SyncService;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    protected SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
        // MIDDLEWARE SUDAH DI ROUTE
    }

    public function index()
    {
        $status = $this->syncService->getSyncStatus();
        return view('sync.index', compact('status'));
    }

    public function sync(Request $request)
    {
        $result = $this->syncService->syncFromGoogleSheets();

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('sync.index')->with('sync_result', $result);
    }

    public function syncExcel(Request $request)
    {
        if ($request->filled('excel_path')) {
            $filePath = trim($request->excel_path);
            
            // Normalize path separators for Windows
            $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

            if (!file_exists($filePath)) {
                return redirect()->route('sync.index')->with('error', 'File tidak ditemukan di path lokal: ' . $filePath);
            }

            $result = $this->syncService->syncFromExcel($filePath);
            return redirect()->route('sync.index')->with('sync_result', $result);
        }

        $request->validate([
            'excel_file' => ['required_without:excel_path', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'excel_file.required_without' => 'Pilih file Excel atau masukkan Path File lokal.',
            'excel_file.file' => 'Input harus berupa file.',
            'excel_file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'excel_file.max' => 'Ukuran file maksimal adalah 10MB.',
        ]);

        $result = $this->syncService->syncFromExcel($request->file('excel_file'));

        return redirect()->route('sync.index')->with('sync_result', $result);
    }

    public function init()
    {
        try {
            $googleSheetService = new \App\Services\GoogleSheetService();
            $headers = $googleSheetService->getHeaders();
            if (empty($headers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca header kolom dari Google Sheets.'
                ], 400);
            }

            // Simpan header ke Cache selama 10 menit
            \Illuminate\Support\Facades\Cache::put('sync_headers', $headers, 600);

            // Hitung total baris secara cepat menggunakan getRowCount()
            $totalRows = $googleSheetService->getRowCount();
            $totalDataRows = max(0, $totalRows - 1);

            return response()->json([
                'success' => true,
                'total_rows' => $totalDataRows,
                'batch_size' => 2000,
                'message' => 'Inisialisasi berhasil.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal inisialisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncBatch(Request $request)
    {
        $request->validate([
            'batch' => 'required|integer',
            'start' => 'required|integer',
            'end' => 'required|integer'
        ]);

        $batchIndex = (int) $request->batch;
        $startRow = (int) $request->start;
        $endRow = (int) $request->end;

        $headers = \Illuminate\Support\Facades\Cache::get('sync_headers');
        if (empty($headers)) {
            $googleSheetService = new \App\Services\GoogleSheetService();
            $headers = $googleSheetService->getHeaders();
            if (empty($headers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Gagal membaca header kolom.'
                ], 400);
            }
            \Illuminate\Support\Facades\Cache::put('sync_headers', $headers, 600);
        }

        $result = $this->syncService->syncBatch($batchIndex, $startRow, $endRow, $headers);

        return response()->json($result);
    }

    public function status()
    {
        $status = $this->syncService->getSyncStatus();
        return view('sync-status', compact('status'));
    }
}