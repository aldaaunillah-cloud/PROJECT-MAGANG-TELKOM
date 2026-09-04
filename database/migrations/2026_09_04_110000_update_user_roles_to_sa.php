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
        // Update default role to 'sa'
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) DEFAULT 'sa'");
        } catch (\Exception $e) {}

        // Ubah semua role 'hotd' atau 'sales' menjadi 'sa' (kecuali admin)
        DB::table('users')
            ->where(function ($q) {
                $q->whereIn('role', ['hotd', 'sales'])
                  ->orWhereNull('role');
            })
            ->whereNotIn('email', ['admin@telkom.com', 'admin@telkom.co.id'])
            ->update([
                'role' => 'sa'
            ]);

        // Update divisi 'HOTD Agency' menjadi 'Sales Agency'
        DB::table('users')
            ->where('divisi', 'HOTD Agency')
            ->update([
                'divisi' => 'Sales Agency'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) DEFAULT 'hotd'");
        } catch (\Exception $e) {}

        DB::table('users')
            ->where('role', 'sa')
            ->update([
                'role' => 'hotd'
            ]);
    }
};
