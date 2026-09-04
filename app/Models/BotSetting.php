<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotSetting extends Model
{
    use HasFactory;

    protected $table = 'bot_settings';

    protected $fillable = [
        'bot_name',
        'bot_token',
        'telegram_group_id',
        'hotd_mentions',
        'app_url',
        'api_token',
        'delay_ms',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'delay_ms' => 'integer',
    ];

    /**
     * Mengambil konfigurasi aktif atau membuat default jika belum ada.
     */
    public static function current(): self
    {
        $setting = self::first();

        if (!$setting) {
            $setting = self::create([
                'bot_name' => 'Telkom Reminder Bot',
                'bot_token' => '8887064573:AAH1ah0uPr0DzfRRACTJYthrtjIHsAch9kk',
                'telegram_group_id' => '-5430258004',
                'hotd_mentions' => '@hotdAinun @hotdDhea',
                'app_url' => config('app.url') ?: 'https://reminder.crafts.web.id',
                'api_token' => config('services.reminder.api_token') ?: 'telkom_reminder_secret_token_2026',
                'delay_ms' => 1500,
                'is_active' => true,
            ]);
        }

        return $setting;
    }
}
