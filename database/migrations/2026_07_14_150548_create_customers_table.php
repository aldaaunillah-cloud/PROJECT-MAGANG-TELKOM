<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            
            // Data utama dari spreadsheet
            $table->string('status_bayar')->nullable();
            $table->decimal('tag_inet', 15, 2)->default(0);
            $table->decimal('tag_tlp', 15, 2)->default(0);
            $table->decimal('tag_total', 15, 2)->default(0);
            $table->string('ssl_file')->nullable();
            $table->string('snd')->nullable()->index();
            $table->string('snd_group')->nullable();
            $table->string('ncli')->nullable();
            $table->string('nama')->nullable();
            $table->text('alamat')->nullable();
            $table->string('sto')->nullable();
            $table->string('datel')->nullable()->index();
            $table->string('produk')->nullable();
            $table->string('eksepsi_desc')->nullable();
            $table->string('desc_newbill')->nullable();
            $table->string('usage_desc')->nullable();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->integer('umur_customer')->nullable();
            $table->integer('billing_ke')->nullable()->index();
            
            // Kolom tambahan dari spreadsheet
            $table->string('paid_l11')->nullable();
            $table->date('tgl_paid')->nullable();
            $table->decimal('paid_rp', 15, 2)->default(0);
            $table->string('coll_agent')->nullable();
            $table->date('tgl_klaim')->nullable();
            $table->decimal('amount_klaim', 15, 2)->default(0);
            $table->string('user_klaim')->nullable();
            $table->date('tgl_paid_n1')->nullable();
            $table->string('agency_psb')->nullable();
            $table->string('sales_agency')->nullable();
            $table->string('ppp')->nullable();
            $table->string('caring_mybrains')->nullable();
            
            // Kolom tambahan
            $table->string('agency')->nullable();
            $table->string('sales')->nullable();
            
            $table->timestamps();
            
            // Index untuk optimize query
            $table->index(['billing_ke', 'datel']);
            $table->index('status_bayar');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};