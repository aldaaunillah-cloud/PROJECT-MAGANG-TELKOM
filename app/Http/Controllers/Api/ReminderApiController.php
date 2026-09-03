<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReminderApiController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Token Keamanan
        $token = config('api.reminder_token');
        $bearerToken = $request->bearerToken();

        if (empty($token) || $bearerToken !== $token) {
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
                'message' => 'Gagal menyimpan riwayat reminder.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil daftar status anggota (Aktif / Tidak Aktif) untuk Bot Telegram
     */
    public function getMembersStatus(Request $request)
    {
        // 1. Validasi Token Keamanan
        $token = config('api.reminder_token');
        $bearerToken = $request->bearerToken();

        if (empty($token) || $bearerToken !== $token) {
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
}

