<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SalesAgencyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kolom-kolom baru otomatis dibuat jika belum ada di database
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'username')) {
            \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                $table->string('username', 100)->nullable()->after('name');
            });
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'kode')) {
            \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                $table->string('kode', 50)->nullable()->after('username');
            });
        }

        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'telegram_id')) {
            \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                $table->string('telegram_id', 50)->nullable()->after('email');
            });
        }

        // Pastikan kolom role diubah dari ENUM ke VARCHAR agar menerima 'sa' dan 'pikol'
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(30) DEFAULT 'sa'");
        } catch (\Exception $e) {}

        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
            \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                $table->string('status', 20)->default('aktif')->after('role');
            });
        }

        $members = [
            ['name' => 'Agnes prawesti puspa lestari', 'kode' => 'MN02817', 'email' => 'agnez.kunezt@gmail.com', 'telegram_id' => '114341071'],
            ['name' => 'Ahmad Ali Subarkah', 'kode' => 'MN01700', 'email' => 'jatinunggal708@gmail.com', 'telegram_id' => '1370726522'],
            ['name' => 'Ayu Nofikasari Zulkifli', 'kode' => 'MN02379', 'email' => 'ayunofikasari1321@gmail.com', 'telegram_id' => '902815312'],
            ['name' => 'Dea Raudhah Jannah Sudrajat', 'kode' => 'MN02562', 'email' => 'ardea170207@gmail.com', 'telegram_id' => '1237513901'],
            ['name' => 'Dinda Ninengah Sandra', 'kode' => 'MN02769', 'email' => 'dindans2804@gmail.com', 'telegram_id' => '2036330785'],
            ['name' => 'Fahry Muhammad Alghifari', 'kode' => 'MN02792', 'email' => 'zahrasitisalmah@gmail.com', 'telegram_id' => '8969096338'],
            ['name' => 'Fajar Ramdhani Ishak', 'kode' => 'MN01705', 'email' => 'sachiajay8@gmail.com', 'telegram_id' => '412030210'],
            ['name' => 'Fitria', 'kode' => 'MN02405', 'email' => 'gugunpurnama215@gmail.com', 'telegram_id' => '6206107278'],
            ['name' => '(ISE) AJIE YUDHISTIRA', 'kode' => 'MN00715', 'email' => 'ajie010203@gmail.com', 'telegram_id' => '100791214'],
            ['name' => '(ISE) Raden Bella Arpemin Santia', 'kode' => 'MN00698', 'email' => 'bellarfmn@gmail.com', 'telegram_id' => '965219730'],
            ['name' => 'MERIN MERIANI', 'kode' => 'MN02818', 'email' => 'merin.meriani1991@gmail.com', 'telegram_id' => '8101154139'],
            ['name' => 'Mohamad allan sadat', 'kode' => 'MN01085', 'email' => 'allansadat@gmail.com', 'telegram_id' => '80669463'],
            ['name' => 'MUHAMMAD SALMAN AL FARIS', 'kode' => 'MN02791', 'email' => 'anaheryani53@gmail.com', 'telegram_id' => '1446666817'],
            ['name' => 'Nina rosana', 'kode' => 'MN02489', 'email' => 'rosanarosan267@gmail.com', 'telegram_id' => '8418180266'],
            ['name' => 'Yessa Lorenza', 'kode' => 'MN02551', 'email' => 'yessalorenza@gmail.com', 'telegram_id' => '5651813279'],
            ['name' => 'ADE NENI MULYANI', 'kode' => 'MB00454', 'email' => 'ade.87neni@gmail.com', 'telegram_id' => '6361404016'],
            ['name' => 'ANGGI SITI LOGAYAH', 'kode' => 'MB00457', 'email' => 'anggisl.indibiz@gmail.com', 'telegram_id' => '6018747843'],
            ['name' => 'Brian Firmansyah', 'kode' => 'MB00460', 'email' => 'brianfirmansyah12341@gmail.com', 'telegram_id' => '6450979867'],
            ['name' => 'Dimas Kevin', 'kode' => 'MB00465', 'email' => 'dimaskevin07@gmail.com', 'telegram_id' => '876554648'],
            ['name' => 'Drajat suratman', 'kode' => 'MB00480', 'email' => 'lanangbuana21@gmail.com', 'telegram_id' => '8674090162'],
            ['name' => 'Gilang Muhamad sainudin', 'kode' => 'MB00471', 'email' => 'gilangmcp135@gmail.com', 'telegram_id' => '671414207'],
            ['name' => 'MUZIA ALIF LUKMAN', 'kode' => 'MB00485', 'email' => 'muziaaliflukman31@gmail.com', 'telegram_id' => '556931921'],
            ['name' => 'Reynaldi Saputra', 'kode' => 'MB00491', 'email' => 'reynaldisaputra33@gmail.com', 'telegram_id' => '6314146134'],
            ['name' => 'Yoga Usman Zaelani', 'kode' => 'MB00501', 'email' => 'yogausmanzaelani182@gmail.com', 'telegram_id' => '6067719937'],
            ['name' => 'ANISA NUR AZIZAH', 'kode' => 'MB80095', 'email' => 'dedechickcool@gmail.com', 'telegram_id' => '1643397865'],
            ['name' => 'Dana iswara', 'kode' => 'MB80090', 'email' => 'danstasik73@gmail.com', 'telegram_id' => '123186059'],
            ['name' => 'Elsa Mayori', 'kode' => 'MB80265', 'email' => 'elsamayori700@gmail.com', 'telegram_id' => '595771010'],
            ['name' => 'Fredy', 'kode' => 'MB80263', 'email' => 'fredyxxx99@gmail.com', 'telegram_id' => '1369463905'],
            ['name' => 'Gunawan', 'kode' => 'MB80247', 'email' => 'gunawanjayadiharja4253@gmail.com', 'telegram_id' => '1280531755'],
            ['name' => 'Ida Herlina', 'kode' => 'MB80087', 'email' => 'ndaherlina@gmail.com', 'telegram_id' => '1966107700'],
            ['name' => 'Ika Sarikah S.Pd', 'kode' => 'MB80109', 'email' => 'jsi.nadira@gmail.com', 'telegram_id' => '781746972'],
            ['name' => 'IRMAN MAULANA,S.Pd', 'kode' => 'MB80091', 'email' => 'irmanm631@gmail.com', 'telegram_id' => '390841943'],
            ['name' => 'JAJAT SUDRAJAT', 'kode' => 'MB80019', 'email' => 'jats.mc@gmail.com', 'telegram_id' => '8371556165'],
            ['name' => 'RANGGA KALAM SIDIQ', 'kode' => 'MB80014', 'email' => 'intansundari13@gmail.com', 'telegram_id' => '6533095811'],
            ['name' => 'Resta Rangga Yanita', 'kode' => 'MB80267', 'email' => 'resta.rangg@gmail.com', 'telegram_id' => '6437295273'],
            ['name' => 'REZA PRATAMA NUGRAHA', 'kode' => 'MB80016', 'email' => 'retma31@gmail.com', 'telegram_id' => '8823999895'],
            ['name' => 'Rian Abdul Rochman', 'kode' => 'MB80250', 'email' => 'arahmanrian29@gmail.com', 'telegram_id' => '6472813028'],
            ['name' => 'Ripan Nurdiansah', 'kode' => 'MB80190', 'email' => 'ripan.nurdiansah27@gmail.com', 'telegram_id' => '889293028'],
            ['name' => 'WAWAN DARMAWAN', 'kode' => 'MB80011', 'email' => 'dwawan059@gmail.com', 'telegram_id' => '6365248046'],
            ['name' => 'Yusup Dimas Supriadi', 'kode' => 'MB80031', 'email' => 'usengdimas4@gmail.com', 'telegram_id' => '1989897060'],
            ['name' => 'ADEN LALANANG JAYA SPD', 'kode' => 'MB80182', 'email' => 'adenlj04061980@gmail.com', 'telegram_id' => '439281136'],
            ['name' => 'Ahyani', 'kode' => 'MB80102', 'email' => 'yaniahyani22@gmail.com', 'telegram_id' => '8699352578'],
            ['name' => 'Arif Hidayat', 'kode' => 'MB80026', 'email' => 'nfl.scorpio@gmail.com', 'telegram_id' => '5504882817'],
            ['name' => 'Citra aji agustian', 'kode' => 'MB80065', 'email' => 'alrioaji@gmail.com', 'telegram_id' => '6137534985'],
            ['name' => 'Fajar heryanto', 'kode' => 'MB80068', 'email' => 'fajarmia88@gmail.com', 'telegram_id' => '6246504236'],
            ['name' => 'HAFID ANDINA', 'kode' => 'MB80258', 'email' => 'hafidandinaajah11@gmail.com', 'telegram_id' => '590666199'],
            ['name' => 'HERDI MAULADAN', 'kode' => 'MB80234', 'email' => 'herdimauladan4@gmail.com', 'telegram_id' => null],
            ['name' => 'MIFTAHUL HASANAH', 'kode' => 'MB80266', 'email' => 'agnahabil8@gmail.com', 'telegram_id' => '142770899'],
            ['name' => 'Mimin Mintarsih', 'kode' => 'MB80084', 'email' => 'deniamimin@gmail.com', 'telegram_id' => '345714550'],
            ['name' => 'MOCHAMMAD MUGGY WIBOWO', 'kode' => 'MB80249', 'email' => 'muggywibowo@gmail.com', 'telegram_id' => '309984879'],
            ['name' => 'Moh Dion', 'kode' => 'MB80078', 'email' => 'diyon2319@gmail.com', 'telegram_id' => '2098659271'],
            ['name' => 'Nuki Widiastuti', 'kode' => 'MB80006', 'email' => 'nukiwidia@gmail.com', 'telegram_id' => '7369251238'],
            ['name' => 'Ruspandi', 'kode' => 'MB80130', 'email' => '4ndy.endut@gmail.com', 'telegram_id' => '6508546896'],
        ];

        $defaultPassword = Hash::make('password123');

        foreach ($members as $m) {
            $email = strtolower(trim($m['email']));
            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0]));

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => trim($m['name']),
                    'username' => $username,
                    'kode' => trim($m['kode']),
                    'telegram_id' => $m['telegram_id'] ? trim($m['telegram_id']) : null,
                    'password' => $defaultPassword,
                    'role' => 'sa',
                    'status' => 'aktif',
                    'divisi' => 'Sales Agency',
                    'witel' => 'Witel Priangan Timur',
                ]
            );
        }
    }
}
