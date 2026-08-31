<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * SAFETY CHECK
         * Migration dibatalkan jika SND belum aman menjadi primary key.
         */
        $stats = DB::table('customers')
            ->selectRaw("
                COUNT(*) AS total_rows,
                COUNT(DISTINCT snd) AS distinct_snd,
                SUM(CASE WHEN snd IS NULL THEN 1 ELSE 0 END) AS null_snd,
                SUM(CASE WHEN snd IS NOT NULL AND TRIM(snd) = '' THEN 1 ELSE 0 END) AS blank_snd
            ")
            ->first();

        if (
            (int) $stats->total_rows !== (int) $stats->distinct_snd ||
            (int) $stats->null_snd > 0 ||
            (int) $stats->blank_snd > 0
        ) {
            throw new RuntimeException(
                'Migration dibatalkan: kolom snd mengandung duplicate, NULL, atau nilai kosong.'
            );
        }

        /*
         * id tetap dipertahankan.
         *
         * Karena id menggunakan AUTO_INCREMENT, id harus tetap memiliki
         * index setelah PRIMARY KEY dipindahkan ke snd.
         *
         * Maka:
         * - id       => UNIQUE + AUTO_INCREMENT
         * - snd      => PRIMARY KEY
         * - NCLI dan seluruh kolom lain tidak disentuh.
         */
        DB::statement(
            'ALTER TABLE customers
             ADD UNIQUE INDEX customers_id_unique (id),
             DROP PRIMARY KEY,
             ADD PRIMARY KEY (snd)'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /*
         * Mengembalikan primary key ke id.
         *
         * Index UNIQUE bawaan snd (customers_snd_unique)
         * tetap dipertahankan.
         */
        DB::statement(
            'ALTER TABLE customers
             DROP PRIMARY KEY,
             ADD PRIMARY KEY (id),
             DROP INDEX customers_id_unique'
        );
    }
};