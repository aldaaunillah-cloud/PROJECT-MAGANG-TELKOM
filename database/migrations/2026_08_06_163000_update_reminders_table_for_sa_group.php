<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            // 1. Drop foreign key constraint terlebih dahulu agar bisa modifikasi kolom
            $table->dropForeign(['customer_id']);
            
            // 2. Ubah kolom customer_id menjadi nullable
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            
            // 3. Pasang kembali foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            // 4. Tambah kolom baru untuk data Sales Agency dan total SSL
            $table->string('sales_agency')->nullable()->after('user_id');
            $table->integer('total_ssl')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('reminders', function (Blueprint $table) {
            // 1. Hapus kolom baru
            $table->dropColumn(['sales_agency', 'total_ssl']);
            
            // 2. Drop foreign key
            $table->dropForeign(['customer_id']);
            
            // 3. Kembalikan customer_id menjadi required (not null)
            $table->unsignedBigInteger('customer_id')->change();
            
            // 4. Re-create foreign key
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
