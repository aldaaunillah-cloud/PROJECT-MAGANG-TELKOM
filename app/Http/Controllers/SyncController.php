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

        return redirect()->route('dashboard')->with('sync_result', $result);
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
                'batch_size' => 5000,
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