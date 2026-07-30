<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Kolom tagihan
            if (!Schema::hasColumn('customers', 'tag_inet')) {
                $table->bigInteger('tag_inet')->nullable()->default(0)->after('snd');
            }
            if (!Schema::hasColumn('customers', 'tag_tlp')) {
                $table->bigInteger('tag_tlp')->nullable()->default(0)->after('tag_inet');
            }
            if (!Schema::hasColumn('customers', 'tag_total')) {
                $table->bigInteger('tag_total')->nullable()->default(0)->after('tag_tlp');
            }

            // Kolom NCLI
            if (!Schema::hasColumn('customers', 'ncli')) {
                $table->string('ncli')->nullable()->after('snd');
            }

            // Kolom STO
            if (!Schema::hasColumn('customers', 'sto')) {
                $table->string('sto')->nullable()->after('alamat');
            }

            // Kolom agency (rename dari agency ke agency_psb)
            if (Schema::hasColumn('customers', 'agency') && !Schema::hasColumn('customers', 'agency_psb')) {
                $table->renameColumn('agency', 'agency_psb');
            } elseif (!Schema::hasColumn('customers', 'agency_psb')) {
                $table->string('agency_psb')->nullable()->after('datel');
            }

            // Kolom sales (rename dari sales ke sales_agency)
            if (Schema::hasColumn('customers', 'sales') && !Schema::hasColumn('customers', 'sales_agency')) {
                $table->renameColumn('sales', 'sales_agency');
            } elseif (!Schema::hasColumn('customers', 'sales_agency')) {
                $table->string('sales_agency')->nullable()->after('agency_psb');
            }

            // Kolom tanggal
            if (!Schema::hasColumn('customers', 'tgl_klaim')) {
                $table->date('tgl_klaim')->nullable()->after('status_bayar');
            }
            if (!Schema::hasColumn('customers', 'tgl_paid')) {
                $table->date('tgl_paid')->nullable()->after('tgl_klaim');
            }
            if (!Schema::hasColumn('customers', 'tgl_paid_n1')) {
                $table->date('tgl_paid_n1')->nullable()->after('tgl_paid');
            }

            // Kolom tambahan lainnya
            if (!Schema::hasColumn('customers', 'snd_group')) {
                $table->string('snd_group')->nullable()->after('snd');
            }
            if (!Schema::hasColumn('customers', 'produk')) {
                $table->string('produk')->nullable()->after('datel');
            }
            if (!Schema::hasColumn('customers', 'eksepsi_desc')) {
                $table->string('eksepsi_desc')->nullable()->after('produk');
            }
            if (!Schema::hasColumn('customers', 'desc_newbill')) {
                $table->string('desc_newbill')->nullable()->after('eksepsi_desc');
            }
            if (!Schema::hasColumn('customers', 'usage_desc')) {
                $table->string('usage_desc')->nullable()->after('desc_newbill');
            }
            if (!Schema::hasColumn('customers', 'umur_customer')) {
                $table->string('umur_customer')->nullable()->after('saldo');
            }
            if (!Schema::hasColumn('customers', 'paid_l11')) {
                $table->string('paid_l11')->nullable()->after('billing_ke');
            }
            if (!Schema::hasColumn('customers', 'paid_rp')) {
                $table->bigInteger('paid_rp')->nullable()->default(0)->after('tgl_paid');
            }
            if (!Schema::hasColumn('customers', 'coll_agent')) {
                $table->string('coll_agent')->nullable()->after('paid_rp');
            }
            if (!Schema::hasColumn('customers', 'amount_klaim')) {
                $table->bigInteger('amount_klaim')->nullable()->default(0)->after('tgl_klaim');
            }
            if (!Schema::hasColumn('customers', 'user_klaim')) {
                $table->string('user_klaim')->nullable()->after('amount_klaim');
            }
            if (!Schema::hasColumn('customers', 'ppp')) {
                $table->string('ppp')->nullable()->after('sales_agency');
            }
            if (!Schema::hasColumn('customers', 'caring_mybrains')) {
                $table->string('caring_mybrains')->nullable()->after('ppp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columns = [
                'tag_inet', 'tag_tlp', 'tag_total', 'ncli', 'sto',
                'tgl_klaim', 'tgl_paid', 'tgl_paid_n1',
                'snd_group', 'produk', 'eksepsi_desc', 'desc_newbill',
                'usage_desc', 'umur_customer', 'paid_l11', 'paid_rp',
                'coll_agent', 'amount_klaim', 'user_klaim',
                'ppp', 'caring_mybrains'
            ];

            // Cek dan drop kolom yang ada
            foreach ($columns as $column) {
                if (Schema::hasColumn('customers', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Kembalikan rename agency -> agency_psb
            if (Schema::hasColumn('customers', 'agency_psb') && !Schema::hasColumn('customers', 'agency')) {
                $table->renameColumn('agency_psb', 'agency');
            }

            // Kembalikan rename sales -> sales_agency
            if (Schema::hasColumn('customers', 'sales_agency') && !Schema::hasColumn('customers', 'sales')) {
                $table->renameColumn('sales_agency', 'sales');
            }
        });
    }
};