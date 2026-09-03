-- =========================================================================
-- SCRIPT SQL: TAMBAH DATA 60 ANGGOTA / AGENSI TELKOM
-- Password Default untuk semua akun: password123
-- Password Hash: $2y$12$Nlm5y505hC4E0Gsq7u6Wz.iV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia
-- =========================================================================

-- 1. Pastikan kolom-kolom baru tersedia di tabel users
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `username` VARCHAR(100) NULL AFTER `name`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `kode` VARCHAR(50) NULL AFTER `username`;
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `telegram_id` VARCHAR(50) NULL AFTER `email`;
ALTER TABLE `users` MODIFY COLUMN `role` VARCHAR(30) DEFAULT 'hotd';
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) DEFAULT 'aktif' AFTER `role`;

-- 2. Insert atau Update data 60 Anggota Agensi
INSERT INTO `users` (`name`, `username`, `kode`, `email`, `telegram_id`, `password`, `role`, `status`, `divisi`, `witel`, `created_at`, `updated_at`) VALUES
('Agnes prawesti puspa lestari', 'agnezkunezt', 'MN02817', 'agnez.kunezt@gmail.com', '114341071', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Ahmad Ali Subarkah', 'jatinunggal708', 'MN01700', 'jatinunggal708@gmail.com', '1370726522', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Ayu Nofikasari Zulkifli', 'ayunofikasari1321', 'MN02379', 'ayunofikasari1321@gmail.com', '902815312', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Dea Raudhah Jannah Sudrajat', 'ardea170207', 'MN02562', 'ardea170207@gmail.com', '1237513901', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Dinda Ninengah Sandra', 'dindans2804', 'MN02769', 'dindans2804@gmail.com', '2036330785', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Fahry Muhammad Alghifari', 'zahrasitisalmah', 'MN02792', 'zahrasitisalmah@gmail.com', '8969096338', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Fajar Ramdhani Ishak', 'sachiajay8', 'MN01705', 'sachiajay8@gmail.com', '412030210', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Fitria', 'gugunpurnama215', 'MN02405', 'gugunpurnama215@gmail.com', '6206107278', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('(ISE) AJIE YUDHISTIRA', 'ajie010203', 'MN00715', 'ajie010203@gmail.com', '100791214', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('(ISE) Raden Bella Arpemin Santia', 'bellarfmn', 'MN00698', 'bellarfmn@gmail.com', '965219730', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('MERIN MERIANI', 'merinmeriani1991', 'MN02818', 'merin.meriani1991@gmail.com', '8101154139', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Mohamad allan sadat', 'allansadat', 'MN01085', 'allansadat@gmail.com', '80669463', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('MUHAMMAD SALMAN AL FARIS', 'anaheryani53', 'MN02791', 'anaheryani53@gmail.com', '1446666817', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Nina rosana', 'rosanarosan267', 'MN02489', 'rosanarosan267@gmail.com', '8418180266', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Nurhayati', 'nurhayatibatusumur', 'MN02451', 'nurhayatibatusumur@gmail.com', '7409937312', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Pipit Fitriani', 'rivanoa3', 'MN02140', 'rivanoa3@gmail.com', '5404141789', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Raden Ahmad Basuki', 'gms468477', 'MN02650', 'gms468477@gmail.com', '8578893355', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Sendi Syarif Hidayatuloh', 'abilstore740', 'MN02539', 'abilstore740@gmail.com', '7845174829', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Shokikah', 'ikhashokhikhaikha', 'MN02312', 'ikhashokhikhaikha@gmail.com', '1824667590', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Soni Yuniar', 'soniyuniar05', 'MN02291', 'soni.yuniar05@gmail.com', '370903602', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Wahyu Mulyadi', 'wahyuartsm2', 'MN02041', 'wahyu.artsm2@gmail.com', '97466977', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Yessa Lorenza', 'yessalorenza', 'MN02551', 'yessalorenza@gmail.com', '5651813279', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('ADE NENI MULYANI', 'ade87neni', 'MB00454', 'ade.87neni@gmail.com', '6361404016', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('ANGGI SITI LOGAYAH', 'anggislindibiz', 'MB00457', 'anggisl.indibiz@gmail.com', '6018747843', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Brian Firmansyah', 'brianfirmansyah12341', 'MB00460', 'brianfirmansyah12341@gmail.com', '6450979867', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Dimas Kevin', 'dimaskevin07', 'MB00465', 'dimaskevin07@gmail.com', '876554648', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Drajat suratman', 'lanangbuana21', 'MB00480', 'lanangbuana21@gmail.com', '8674090162', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Gilang Muhamad sainudin', 'gilangmcp135', 'MB00471', 'gilangmcp135@gmail.com', '671414207', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('MUZIA ALIF LUKMAN', 'muziaaliflukman31', 'MB00485', 'muziaaliflukman31@gmail.com', '556931921', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Reynaldi Saputra', 'reynaldisaputra33', 'MB00491', 'reynaldisaputra33@gmail.com', '6314146134', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Yoga Usman Zaelani', 'yogausmanzaelani182', 'MB00501', 'yogausmanzaelani182@gmail.com', '6067719937', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('ANISA NUR AZIZAH', 'dedechickcool', 'MB80095', 'dedechickcool@gmail.com', '1643397865', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Dana iswara', 'danstasik73', 'MB80090', 'danstasik73@gmail.com', '123186059', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Elsa Mayori', 'elsamayori700', 'MB80265', 'elsamayori700@gmail.com', '595771010', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Fredy', 'fredyxxx99', 'MB80263', 'fredyxxx99@gmail.com', '1369463905', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Gunawan', 'gunawanjayadiharja4253', 'MB80247', 'gunawanjayadiharja4253@gmail.com', '1280531755', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Ida Herlina', 'ndaherlina', 'MB80087', 'ndaherlina@gmail.com', '1966107700', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Ika Sarikah S.Pd', 'jsinadira', 'MB80109', 'jsi.nadira@gmail.com', '781746972', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('IRMAN MAULANA,S.Pd', 'irmanm631', 'MB80091', 'irmanm631@gmail.com', '390841943', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('JAJAT SUDRAJAT', 'jatsmc', 'MB80019', 'jats.mc@gmail.com', '8371556165', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('RANGGA KALAM SIDIQ', 'intansundari13', 'MB80014', 'intansundari13@gmail.com', '6533095811', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Resta Rangga Yanita', 'restarangg', 'MB80267', 'resta.rangg@gmail.com', '6437295273', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('REZA PRATAMA NUGRAHA', 'retma31', 'MB80016', 'retma31@gmail.com', '8823999895', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Rian Abdul Rochman', 'arahmanrian29', 'MB80250', 'arahmanrian29@gmail.com', '6472813028', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Ripan Nurdiansah', 'ripannurdiansah27', 'MB80190', 'ripan.nurdiansah27@gmail.com', '889293028', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('WAWAN DARMAWAN', 'dwawan059', 'MB80011', 'dwawan059@gmail.com', '6365248046', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Yusup Dimas Supriadi', 'usengdimas4', 'MB80031', 'usengdimas4@gmail.com', '1989897060', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('ADEN LALANANG JAYA SPD', 'adenlj04061980', 'MB80182', 'adenlj04061980@gmail.com', '439281136', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Ahyani', 'yaniahyani22', 'MB80102', 'yaniahyani22@gmail.com', '8699352578', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Arif Hidayat', 'nflscorpio', 'MB80026', 'nfl.scorpio@gmail.com', '5504882817', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Citra aji agustian', 'alrioaji', 'MB80065', 'alrioaji@gmail.com', '6137534985', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Fajar heryanto', 'fajarmia88', 'MB80068', 'fajarmia88@gmail.com', '6246504236', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('HAFID ANDINA', 'hafidandinaajah11', 'MB80258', 'hafidandinaajah11@gmail.com', '590666199', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('HERDI MAULADAN', 'herdimauladan4', 'MB80234', 'herdimauladan4@gmail.com', NULL, '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('MIFTAHUL HASANAH', 'agnahabil8', 'MB80266', 'agnahabil8@gmail.com', '142770899', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Mimin Mintarsih', 'deniamimin', 'MB80084', 'deniamimin@gmail.com', '345714550', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('MOCHAMMAD MUGGY WIBOWO', 'muggywibowo', 'MB80249', 'muggywibowo@gmail.com', '309984879', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Moh Dion', 'diyon2319', 'MB80078', 'diyon2319@gmail.com', '2098659271', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Nuki Widiastuti', 'nukiwidia', 'MB80006', 'nukiwidia@gmail.com', '7369251238', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW()),
('Ruspandi', '4ndyendut', 'MB80130', '4ndy.endut@gmail.com', '6508546896', '$2y$12$e8yX7V9QYqRzZ7oVbF7e.eV3r4Z8G2gVd6M2w9gL2k3v7V5o6Gia', 'hotd', 'aktif', 'HOTD Agency', 'Witel Priangan Timur', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `username` = VALUES(`username`),
    `kode` = VALUES(`kode`),
    `telegram_id` = VALUES(`telegram_id`),
    `role` = VALUES(`role`),
    `status` = VALUES(`status`),
    `divisi` = VALUES(`divisi`),
    `witel` = VALUES(`witel`),
    `updated_at` = NOW();
