<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
    }

    /**
     * Kirim pesan Telegram ke Chat ID tertentu (HTML mode)
     */
    public function sendMessage(string|int $chatId, string $message): array
    {
        if (empty($this->botToken)) {
            Log::error('TelegramService: TELEGRAM_BOT_TOKEN belum dikonfigurasi di .env / services.php');
            return [
                'success' => false,
                'message' => 'Token Bot Telegram belum dikonfigurasi di server.'
            ];
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            
            $response = Http::timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['ok'] ?? false)) {
                return [
                    'success' => true,
                    'message_id' => $result['result']['message_id'] ?? null,
                    'data' => $result,
                ];
            }

            Log::warning('TelegramService failed response', [
                'chat_id' => $chatId,
                'response' => $result,
            ]);

            return [
                'success' => false,
                'message' => $result['description'] ?? 'Gagal mengirim pesan Telegram.',
                'data' => $result,
            ];

        } catch (\Exception $e) {
            Log::error('TelegramService exception: ' . $e->getMessage(), [
                'chat_id' => $chatId,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan koneksi ke server Telegram: ' . $e->getMessage()
            ];
        }
    }
}
