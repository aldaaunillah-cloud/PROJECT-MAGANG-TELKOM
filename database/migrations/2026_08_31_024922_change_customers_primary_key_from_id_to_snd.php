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
        // Cek apakah PRIMARY KEY sudah pada kolom snd
        $primaryKey = DB::select("SHOW KEYS FROM customers WHERE Key_name = 'PRIMARY'");
        if (!empty($primaryKey) && isset($primaryKey[0]->Column_name) && strtolower($primaryKey[0]->Column_name) === 'snd') {
            return; // Sudah menjadi primary key
        }

        // Bersihkan baris yang snd bernilai NULL atau kosong
        DB::statement("DELETE FROM customers WHERE snd IS NULL OR TRIM(snd) = ''");

        // Hapus duplikat snd lama jika ada (mempertahankan baris pertama)
        DB::statement("
            DELETE c1 FROM customers c1
            INNER JOIN customers c2 
            WHERE c1.id > c2.id AND c1.snd = c2.snd
        ");

        /*
         * SAFETY CHECK
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
         */
        // Cek apakah index customers_id_unique sudah ada
        $indexes = DB::select("SHOW INDEXES FROM customers WHERE Key_name = 'customers_id_unique'");
        $addUniqueSql = empty($indexes) ? "ADD UNIQUE INDEX customers_id_unique (id)," : "";

        DB::statement(
            "ALTER TABLE customers
             {$addUniqueSql}
             DROP PRIMARY KEY,
             ADD PRIMARY KEY (snd)"
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