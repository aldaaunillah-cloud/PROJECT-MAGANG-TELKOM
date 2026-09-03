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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 100)->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'kode')) {
                $table->string('kode', 50)->nullable()->after('username');
            }

            if (!Schema::hasColumn('users', 'telegram_id')) {
                $table->string('telegram_id', 50)->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('hotd')->after('telegram_id');
            } else {
                $table->string('role', 30)->default('hotd')->change();
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('aktif')->after('role');
            }
        });

        // Set semua akun admin / admin@telkom.com menjadi role 'pikol' dan status 'aktif'
        DB::table('users')->where('email', 'admin@telkom.com')
            ->orWhere('role', 'admin')
            ->orWhere('role', 'sales')
            ->orWhereNull('role')
            ->update([
                'role' => 'pikol',
                'status' => 'aktif'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'telegram_id')) {
                $table->dropColumn('telegram_id');
            }
            if (Schema::hasColumn('users', 'kode')) {
                $table->dropColumn('kode');
            }
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};
