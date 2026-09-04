<?php

namespace App\Http\Controllers;

use App\Models\BotSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BotSettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan bot.
     */
    public function index()
    {
        $setting = BotSetting::current();
        
        // Pecah string tag HOTD (misal "@hotdAinun @hotdDhea") menjadi array
        $rawHotd = trim((string)$setting->hotd_mentions);
        $hotdList = array_values(array_filter(preg_split('/\s+/', $rawHotd)));

        if (empty($hotdList)) {
            $hotdList = [''];
        }

        return view('bot-settings.index', compact('setting', 'hotdList'));
    }

    /**
     * Simpan pembaruan pengaturan bot.
     */
    public function update(Request $request)
    {
        $request->validate([
            'bot_name' => 'nullable|string|max:100',
            'bot_token' => 'required|string|max:255',
            'telegram_group_id' => 'required|string|max:50',
            'hotd_mentions' => 'nullable|array',
            'hotd_mentions.*' => 'nullable|string|max:100',
            'app_url' => 'required|url|max:255',
            'api_token' => 'required|string|max:255',
            'delay_ms' => 'required|integer|min:500|max:10000',
            'is_active' => 'nullable|boolean',
        ], [
            'bot_token.required' => 'Token Bot Telegram wajib diisi.',
            'telegram_group_id.required' => 'ID Grup Telegram wajib diisi.',
            'app_url.required' => 'URL Aplikasi Web wajib diisi.',
            'app_url.url' => 'Format URL Aplikasi Web tidak valid (harus diawali https:// atau http://).',
            'api_token.required' => 'API Secret Token wajib diisi.',
            'delay_ms.required' => 'Jeda pengiriman wajib diisi.',
            'delay_ms.min' => 'Jeda pengiriman minimal 500 ms.',
        ]);

        // Proses array HOTD Mentions menjadi string rapi berawalan @
        $hotdArray = (array) ($request->hotd_mentions ?? []);
        $cleanHotd = [];
        foreach ($hotdArray as $item) {
            $val = trim((string)$item);
            if (!empty($val) && $val !== '-') {
                if (!str_starts_with($val, '@')) {
                    $val = '@' . $val;
                }
                $cleanHotd[] = $val;
            }
        }
        $hotdString = implode(' ', array_unique($cleanHotd));

        $setting = BotSetting::current();
        $setting->update([
            'bot_name' => trim($request->bot_name ?: 'Telkom Reminder Bot'),
            'bot_token' => trim($request->bot_token),
            'telegram_group_id' => trim($request->telegram_group_id),
            'hotd_mentions' => $hotdString,
            'app_url' => rtrim(trim($request->app_url), '/'),
            'api_token' => trim($request->api_token),
            'delay_ms' => (int)$request->delay_ms,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('bot-settings.index')->with('success', 'Pengaturan Chat Bot berhasil disimpan dan disinkronkan!');
    }

    /**
     * Uji coba kirim pesan test ke grup Telegram.
     */
    public function testTelegram(Request $request)
    {
        $setting = BotSetting::current();

        $token = trim($request->bot_token ?: $setting->bot_token);
        $groupId = trim($request->telegram_group_id ?: $setting->telegram_group_id);
        $hotd = trim($request->hotd_mentions ?: $setting->hotd_mentions);

        if (empty($token) || empty($groupId)) {
            return response()->json([
                'success' => false,
                'message' => 'Token Bot dan ID Grup Telegram tidak boleh kosong.'
            ], 422);
        }

        try {
            $pesan = "🔔 <b>TES KONEKSI CHATBOT TELKOM</b> 🔔\n\n";
            $pesan .= "Pesan uji coba berhasil dikirim dari Web Reminder Telkom.\n";
            if (!empty($hotd)) {
                $pesan .= "Tag HOTD Terdaftar: " . htmlspecialchars($hotd) . "\n";
            }
            $pesan .= "Waktu Uji Coba: " . now()->format('d-m-Y H:i:s') . " WIB\n\n";
            $pesan .= "<i>Sistem Bot Telegram & Web terhubung normal.</i> ✅";

            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $groupId,
                'text' => $pesan,
                'parse_mode' => 'HTML',
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['ok']) && $result['ok'] === true) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan uji coba berhasil terkirim ke Grup Telegram!',
                    'data' => $result
                ]);
            } else {
                $errMsg = $result['description'] ?? 'Gagal mengirim pesan ke Telegram.';
                return response()->json([
                    'success' => false,
                    'message' => 'Telegram Error: ' . $errMsg
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Test Telegram Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }
}
