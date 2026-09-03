<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus tabel-tabel hantu / sisa desain lama yang tidak memiliki kolom data dan tidak dipakai
        Schema::dropIfExists('datels');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('sales_agencies');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('datels')) {
            Schema::create('datels', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('agents')) {
            Schema::create('agents', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sales_agencies')) {
            Schema::create('sales_agencies', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }
    }
};
