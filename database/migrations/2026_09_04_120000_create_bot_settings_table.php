<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bot_settings', function (Blueprint $table) {
            $table->id();
            $table->string('bot_name', 100)->default('Telkom Reminder Bot');
            $table->string('bot_token')->nullable()->default('8887064573:AAH1ah0uPr0DzfRRACTJYthrtjIHsAch9kk');
            $table->string('telegram_group_id', 50)->nullable()->default('-5430258004');
            $table->string('hotd_mentions')->nullable()->default('@hotdAinun @hotdDhea');
            $table->string('app_url')->nullable()->default('https://reminder.crafts.web.id');
            $table->string('api_token')->nullable()->default('telkom_reminder_secret_token_2026');
            $table->integer('delay_ms')->default(1500);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert initial default configuration row
        DB::table('bot_settings')->insert([
            'bot_name' => 'Telkom Reminder Bot',
            'bot_token' => '8887064573:AAH1ah0uPr0DzfRRACTJYthrtjIHsAch9kk',
            'telegram_group_id' => '-5430258004',
            'hotd_mentions' => '@hotdAinun @hotdDhea',
            'app_url' => 'https://reminder.crafts.web.id',
            'api_token' => 'telkom_reminder_secret_token_2026',
            'delay_ms' => 1500,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_settings');
    }
};
