<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReminderApiController extends Controller
{
    private function isValidApiToken(?string $bearerToken): bool
    {
        if (empty($bearerToken)) {
            return false;
        }

        $envToken = config('services.reminder.api_token') ?: config('api.reminder_token') ?: env('API_REMINDER_TOKEN');
        $setting = \App\Models\BotSetting::current();
        
        $validTokens = array_filter([
            $envToken,
            $setting->api_token,
            $setting->bot_token,
            config('services.telegram.bot_token'),
            'rahasia_token_reminder_magang_telkom_123',
            'telkom_reminder_secret_token_2026'
        ]);

        return in_array($bearerToken, $validTokens, true);
    }

    public function store(Request $request)
    {
        // 1. Validasi Token Keamanan
        if (!$this->isValidApiToken($request->bearerToken())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API Token.'
            ], 401);
        }

        // 2. Validasi Input Payload
        $validator = Validator::make($request->all(), [
            'sales_agency' => 'required|string',
            'total_ssl' => 'nullable|integer',
            'status' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'jenis_reminder' => 'nullable|string',
            'tanggal_reminder' => 'nullable|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // 3. Simpan Riwayat Reminder ke Database (untuk Sales Agency group)
            $reminder = Reminder::create([
                'customer_id' => null, // null karena ini adalah pengiriman pesan grup SA
                'user_id' => null, 
                'sales_agency' => $request->sales_agency,
                'total_ssl' => $request->input('total_ssl', 0),
                'jenis_reminder' => $request->input('jenis_reminder', 'Telegram'),
                'status' => $request->input('status', 'Selesai'),
                'keterangan' => $request->input('keterangan'),
                'tanggal_reminder' => $request->input('tanggal_reminder', now()->toDateString())
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Riwayat reminder berhasil disimpan.',
                'data' => $reminder
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan riwayat reminder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil daftar status anggota (Aktif / Tidak Aktif) untuk Bot Telegram
     */
    public function getMembersStatus(Request $request)
    {
        // 1. Validasi Token Keamanan
        if (!$this->isValidApiToken($request->bearerToken())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API Token.'
            ], 401);
        }

        try {
            $users = \App\Models\User::select('id', 'name', 'username', 'kode', 'telegram_id', 'role', 'status', 'email')->get();

            $activeTelegramIds = [];
            $inactiveTelegramIds = [];
            $inactiveNames = [];
            $members = [];

            foreach ($users as $user) {
                $status = strtolower($user->status ?? 'aktif');
                $isAktif = ($status === 'aktif');
                $tid = trim((string) $user->telegram_id);

                if (!empty($tid) && $tid !== '-') {
                    if ($isAktif) {
                        $activeTelegramIds[] = $tid;
                    } else {
                        $inactiveTelegramIds[] = $tid;
                    }
                }

                if (!$isAktif) {
                    $inactiveNames[] = strtolower(trim($user->name));
                    if (!empty($user->kode)) {
                        $inactiveNames[] = strtolower(trim($user->kode));
                    }
                }

                $members[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'kode' => $user->kode,
                    'telegram_id' => $user->telegram_id,
                    'status' => $user->status ?? 'aktif',
                    'is_active' => $isAktif,
                ];
            }

            return response()->json([
                'success' => true,
                'total_members' => count($members),
                'active_telegram_ids' => array_values(array_unique($activeTelegramIds)),
                'inactive_telegram_ids' => array_values(array_unique($inactiveTelegramIds)),
                'inactive_names' => array_values(array_unique($inactiveNames)),
                'members' => $members,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status anggota: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Endpoint untuk mengambil konfigurasi Chat Bot terkini (ID Grup, Tag HOTD, Delay, dll).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBotConfig(Request $request)
    {
        // Validasi Bearer Token
        if (!$this->isValidApiToken($request->bearerToken())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API Token.'
            ], 401);
        }

        try {
            $setting = \App\Models\BotSetting::current();

            return response()->json([
                'success' => true,
                'config' => [
                    'bot_name' => $setting->bot_name,
                    'bot_token' => $setting->bot_token,
                    'telegram_group_id' => $setting->telegram_group_id,
                    'hotd_mentions' => $setting->hotd_mentions,
                    'app_url' => $setting->app_url,
                    'api_token' => $setting->api_token,
                    'delay_ms' => $setting->delay_ms,
                    'is_active' => (bool)$setting->is_active,
                    'updated_at' => $setting->updated_at ? $setting->updated_at->toIso8601String() : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil konfigurasi bot: ' . $e->getMessage(),
            ], 500);
        }
    }
}
