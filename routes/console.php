<?php

use Illuminate\Support\Facades\Schedule;
use App\Services\SyncService;
use Illuminate\Support\Facades\Log;

// ============================================
// PERINTAH SYNC OTOMATIS (JALAN SETIAP 1 MENIT)
// ============================================
Schedule::call(function () {
    try {
        $syncService = app(SyncService::class);
        $result = $syncService->sync();
        
        Log::info('Sync otomatis berhasil: ' . json_encode($result));
        echo "✅ Sync otomatis berhasil: " . json_encode($result) . PHP_EOL;
        
    } catch (\Exception $e) {
        Log::error('Sync otomatis gagal: ' . $e->getMessage());
        echo "❌ Sync otomatis gagal: " . $e->getMessage() . PHP_EOL;
    }
})->everyMinute(); // Sync setiap 1 menit