<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Tambah kolom yang dibutuhkan untuk HOTD detail
            $table->string('snd_group')->nullable()->after('snd');
            $table->string('ncli')->nullable()->after('snd_group');
            $table->string('sto')->nullable()->after('alamat');
            $table->string('produk')->nullable()->after('datel');
            $table->string('eksepsi_desc')->nullable()->after('produk');
            $table->string('desc_newbill')->nullable()->after('eksepsi_desc');
            $table->string('usage_desc')->nullable()->after('desc_newbill');
            $table->integer('umur_customer')->nullable()->after('saldo');
            $table->string('paid_l11')->nullable()->after('billing_ke');
            $table->decimal('paid_rp', 15, 2)->default(0)->after('tgl_paid');
            $table->string('coll_agent')->nullable()->after('paid_rp');
            $table->decimal('amount_klaim', 15, 2)->default(0)->after('tgl_klaim');
            $table->string('user_klaim')->nullable()->after('amount_klaim');
            $table->string('agency_psb')->nullable()->after('tgl_paid_n1');
            $table->string('sales_agency')->nullable()->after('agency_psb');
            $table->string('ppp')->nullable()->after('sales_agency');
            $table->string('caring_mybrains')->nullable()->after('ppp');
            
            // Tambah index untuk optimize query
            $table->index('sto');
            $table->index('produk');
            $table->index('status_bayar');
            $table->index(['billing_ke', 'datel']);
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'snd_group',
                'ncli',
                'sto',
                'produk',
                'eksepsi_desc',
                'desc_newbill',
                'usage_desc',
                'umur_customer',
                'paid_l11',
                'paid_rp',
                'coll_agent',
                'amount_klaim',
                'user_klaim',
                'agency_psb',
                'sales_agency',
                'ppp',
                'caring_mybrains'
            ]);
            
            $table->dropIndex(['sto']);
            $table->dropIndex(['produk']);
            $table->dropIndex(['status_bayar']);
            $table->dropIndex(['billing_ke', 'datel']);
        });
    }
};