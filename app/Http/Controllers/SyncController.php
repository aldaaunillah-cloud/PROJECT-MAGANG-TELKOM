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

    public function status()
    {
        $status = $this->syncService->getSyncStatus();
        return view('sync-status', compact('status'));
    }
}