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

    public function status()
    {
        $status = $this->syncService->getSyncStatus();
        return view('sync-status', compact('status'));
    }
}