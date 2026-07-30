<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Tagihan
            if (!Schema::hasColumn('customers', 'tag_inet')) {
                $table->bigInteger('tag_inet')->nullable()->default(0)->after('id');
            }
            if (!Schema::hasColumn('customers', 'tag_tlp')) {
                $table->bigInteger('tag_tlp')->nullable()->default(0)->after('tag_inet');
            }
            if (!Schema::hasColumn('customers', 'tag_total')) {
                $table->bigInteger('tag_total')->nullable()->default(0)->after('tag_tlp');
            }

            // SND Group
            if (!Schema::hasColumn('customers', 'snd_group')) {
                $table->string('snd_group')->nullable()->after('snd');
            }

            // NCLI
            if (!Schema::hasColumn('customers', 'ncli')) {
                $table->string('ncli')->nullable()->after('snd_group');
            }

            // STO
            if (!Schema::hasColumn('customers', 'sto')) {
                $table->string('sto')->nullable()->after('alamat');
            }

            // Produk
            if (!Schema::hasColumn('customers', 'produk')) {
                $table->string('produk')->nullable()->after('datel');
            }

            // Eksepsi Desc
            if (!Schema::hasColumn('customers', 'eksepsi_desc')) {
                $table->string('eksepsi_desc')->nullable()->after('produk');
            }

            // Desc Newbill
            if (!Schema::hasColumn('customers', 'desc_newbill')) {
                $table->string('desc_newbill')->nullable()->after('eksepsi_desc');
            }

            // Usage Desc
            if (!Schema::hasColumn('customers', 'usage_desc')) {
                $table->string('usage_desc')->nullable()->after('desc_newbill');
            }

            // Umur Customer
            if (!Schema::hasColumn('customers', 'umur_customer')) {
                $table->string('umur_customer')->nullable()->after('saldo');
            }

            // PAID L11
            if (!Schema::hasColumn('customers', 'paid_l11')) {
                $table->string('paid_l11')->nullable()->after('billing_ke');
            }

            // TGL PAID
            if (!Schema::hasColumn('customers', 'tgl_paid')) {
                $table->date('tgl_paid')->nullable()->after('paid_l11');
            }

            // PAID Rp
            if (!Schema::hasColumn('customers', 'paid_rp')) {
                $table->bigInteger('paid_rp')->nullable()->default(0)->after('tgl_paid');
            }

            // COLL AGENT
            if (!Schema::hasColumn('customers', 'coll_agent')) {
                $table->string('coll_agent')->nullable()->after('paid_rp');
            }

            // TGL KLAIM
            if (!Schema::hasColumn('customers', 'tgl_klaim')) {
                $table->date('tgl_klaim')->nullable()->after('coll_agent');
            }

            // AMOUNT KLAIM
            if (!Schema::hasColumn('customers', 'amount_klaim')) {
                $table->bigInteger('amount_klaim')->nullable()->default(0)->after('tgl_klaim');
            }

            // USER KLAIM
            if (!Schema::hasColumn('customers', 'user_klaim')) {
                $table->string('user_klaim')->nullable()->after('amount_klaim');
            }

            // TGL PAID N-1
            if (!Schema::hasColumn('customers', 'tgl_paid_n1')) {
                $table->date('tgl_paid_n1')->nullable()->after('user_klaim');
            }

            // PPP
            if (!Schema::hasColumn('customers', 'ppp')) {
                $table->string('ppp')->nullable()->after('sales');
            }

            // CARING MYBRAINS
            if (!Schema::hasColumn('customers', 'caring_mybrains')) {
                $table->string('caring_mybrains')->nullable()->after('ppp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'tag_inet', 'tag_tlp', 'tag_total', 'snd_group', 'ncli',
                'sto', 'produk', 'eksepsi_desc', 'desc_newbill', 'usage_desc',
                'umur_customer', 'paid_l11', 'tgl_paid', 'paid_rp',
                'coll_agent', 'tgl_klaim', 'amount_klaim', 'user_klaim',
                'tgl_paid_n1', 'ppp', 'caring_mybrains'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};