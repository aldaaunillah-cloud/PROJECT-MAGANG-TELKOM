<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->index('billing_ke');
            $table->index('status_bayar');
            $table->index('agency');
            $table->index('sales');
            $table->index('datel');
            $table->index('tag_total');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['billing_ke']);
            $table->dropIndex(['status_bayar']);
            $table->dropIndex(['agency']);
            $table->dropIndex(['sales']);
            $table->dropIndex(['datel']);
            $table->dropIndex(['tag_total']);
        });
    }
};