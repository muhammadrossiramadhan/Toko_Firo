# Log Pengerjaan Toko Firo

## Tabel Status Milestone & Log Aktivitas

| No | Kegiatan | Status | Catatan |
|---|---|---|---|
| 1 | Review 6 diagram (use case, activity, 4 sequence) | Selesai | Analisis kebutuhan modul inventori & transaksi |
| 2 | Diagram Mermaid Kelola Data Barang (use case admin, activity, sequence) | Selesai | Diagram alur dan arsitektur data |
| 3 | Inventaris Laravel 12 dan struktur tabel | Selesai | Pemetaan tabel inti MariaDB vs migrasi |
| 4 | Dokumen rencana koreksi_copilot.md | Selesai | Keputusan teknis K1 sampai K19 |
| 5 | Setup FlyEnv: PHP 8.4.26, MariaDB, PATH | Selesai | Ekstensi PHP lengkap (pdo_mysql, curl, dll) |
| 6 | composer install | Selesai | Dependensi Laravel 12 terpasang |
| 7 | Konfigurasi .env dan application key | Selesai | Terisolasi dan aman di .gitignore |
| 8 | DemoBarangController dan barang.blade.php (47 data dummy) | Selesai | Prototype frontend awal |
| 9 | Route GET /demo/barang | Selesai | Rute demo prototype mandiri |
| 10 | Verifikasi HTTP 200 halaman 1 dan 7, tanpa SVG, 5 test lulus | Selesai | tests/Feature/DemoBarangTest.php dibuat |
| 11 | Label tombol Ubah Data dan Hapus Data | Selesai | Diperbaiki sesuai aturan K12 |
| 12 | Perbandingan visual dengan prototype & zoom 100% | Selesai | Layout modal flexbox sticky max-h-[90vh], font >= 16px, touch-target >= 44px, tanpa SVG |
| 13 | Model Pengguna, ItemBarang, isolasi rute, CRUD SP | Selesai | Milestone 2 (L1–L16 di koreksi_copilot.md tuntas 100%) |
| 14 | database/sql/09_procedure_ubah_hapus.sql | Selesai | sp_update_item_v2, sp_delete_item_v2 diterapkan |
| 15 | Handoff dan dokumentasi branch staging | Selesai | HANDOFF_CRUD_TOKO_FIRO.md dibuat |
| 16 | Controller dan rute Laporan Penjualan (Figma P11) | Selesai | LaporanPenjualanController.php, filter rentang waktu (hari ini, 7 hari, bulan ini, kustom), 12 dummy dataset |
| 17 | View Laporan Penjualan (penjualan.blade.php & pagination.blade.php) | Selesai | 3 kartu metrik ringkasan, riwayat transaksi tabel, paginasi 5 item, subtotal negatif merah, tombol cetak PDF window.print() |
| 18 | Navigasi sidebar terpadu antar-modul | Selesai | Menu sidebar admin/barang/index.blade.php dihubungkan ke route('admin.laporan.penjualan') |
| 19 | Decoupling autentikasi & isolasi rute laporan penjualan | Selesai | routes/admin_laporan.php mandiri tanpa sentuh routes/web.php; null-safe user header untuk cegah konflik merge |
| 20 | Standardisasi skala tampilan global 80% (Figma 100%) | Selesai | resources/views/partials/skala.blade.php (:root --skala: 80%), diterapkan seragam pada modul barang, demo, dan laporan |
| 21 | Test Suite Laporan Penjualan dan Skala Tampilan | Selesai | tests/Feature/LaporanPenjualanAccessTest.php dan TampilanSkalaTest.php |
| 22 | Controller, rute, dan view Pembelian Barang (Restock - Figma P21) | Selesai | PembelianController.php, routes/admin_pembelian.php, view pembelian index, 2 tab interaktif (Pembelian Baru & Riwayat), kartu restock, tabel riwayat, touch-target 2.75rem, bebas SVG |
| 23 | Test Suite Pembelian Barang (Restock) | Selesai | tests/Feature/PembelianAdminTest.php (DatabaseTransactions); total 46 test (38 passed, 8 skipped auth, 0 failed, 144 assertions) |
| 24 | Controller, rute, dan view Kelola Akun Pengguna (Figma P13) | Selesai | PenggunaController.php, routes/admin_pengguna.php, view admin/pengguna/index.blade.php, tabel hak akses, badge status AKTIF/NONAKTIF, modal ganti password dan tambah akun, patuh K12 & K18 |
| 25 | Test Suite Kelola Akun Pengguna | Selesai | tests/Feature/PenggunaAdminTest.php; 4 passed, 21 assertions |
| 26 | Controller, rute, dan view Batas Stok Minimum (Figma P14) | Selesai | BatasStokController.php, routes/admin_batas_stok.php, view admin/batas_stok/index.blade.php, banner pengingat Telegram tanpa emoji, input stepper tombol teks [-] dan [+], filter jenis, aksi cepat atur 3/5 pcs, bilah simpan mengambang (floating bar), patuh K12 & K18 |
| 27 | Test Suite Batas Stok Minimum | Selesai | tests/Feature/BatasStokAdminTest.php; 4 passed, 27 assertions |
| 28 | Controller, rute, dan view Dashboard Admin (Figma P10) | Selesai | DashboardController.php, routes/admin_dashboard.php, view admin/dashboard/index.blade.php, sapaan "Halo, Junaidi" tanpa emoji tangan, 4 kartu ringkasan metrik (Penjualan, Transaksi, Stok Menipis, Laba), diagram batang 7 hari murni Tailwind CSS (tanpa Chart.js), daftar Top Barang & Aktivitas Terbaru, patuh K12 & K18 |
| 29 | Test Suite Dashboard Admin | Selesai | tests/Feature/DashboardAdminTest.php; 4 passed, 29 assertions |
| 30 | Controller, rute, dan view Backup Data (Figma P15) | Selesai | BackupController.php, routes/admin_backup.php, view admin/backup/index.blade.php, kartu Backup Manual (border hijau) & Backup Otomatis (border netral), tabel 4 riwayat backup dengan tombol unduh teks murni, kotak pemberitahuan server lokal, patuh K12 & K18 |
| 31 | Test Suite Backup Data | Selesai | tests/Feature/BackupAdminTest.php; 4 passed, 21 assertions |
| 32 | Controller, rute, dan view Log Aktivitas (Figma P16) | Selesai | LogAktivitasController.php, routes/admin_log.php, view admin/log/index.blade.php, tombol teks "Ekspor CSV", baris filter keterangan/tanggal/pengguna/tipe aksi, tabel 10 baris riwayat audit dengan badge warna lembut, teks footer info total log, patuh K12 & K18 |
| 33 | Test Suite Log Aktivitas | Selesai | tests/Feature/LogAktivitasAdminTest.php; 4 passed, 19 assertions; Total seluruh proyek: 54 passed, 16 skipped, 0 failed (241 assertions) |
| 34 | Standardisasi rute login /masuk dan verifikasi 2FA OTP | Selesai | LoginController & OtpController, routes/web.php, rute dinamai 'login', redirect alias dari /login ke /masuk |
| 35 | Pengujian koneksi Telegram Bot API via Tinker & set-commands | Selesai | Verifikasi API token, kirim OTP grup Telegram, command artisan telegram:set-commands untuk pendaftaran 4 bot command |
| 36 | Konsistensi menu Pengaturan Toko pada sidebar admin | Selesai | resources/views/partials/sidebar_admin.blade.php menyertakan menu Pengaturan Toko dinamis di seluruh modul |
| 37 | Perbaikan layout scrolling sidebar admin terkunci (sticky) | Selesai | Tag aside distandarkan: h-screen sticky top-0 flex-shrink-0 flex flex-col justify-between overflow-y-auto w-64 |
| 38 | Antarmuka UI Sesi Kedaluwarsa (Expired) K12 & K18 | Selesai | resources/views/auth/expired.blade.php (rute /sesi-berakhir) dan resources/views/errors/419.blade.php, bebas SVG, target 44px |
| 39 | Implementasi arsitektur bot Telegram dua arah | Selesai | TelegramBotService, TelegramReportService, DomPDF laporan A4, TelegramWebhookController, rute api/telegram/webhook |
| 40 | Evaluasi keamanan & penetapan status bot webhook | Pending | Status PENDING (on hold) menunggu finalisasi backend admin; Cyber Security Checklist disusun rapi |
| 41 | Layanan LaporanPenjualanService & integrasi DB nyata | Selesai | app/Services/LaporanPenjualanService.php, agregasi penjualan/laba/qty/terlaris, paginasi 5 item, filter rentang tanggal inklusif |
| 42 | Layanan DashboardService & integrasi DB nyata | Selesai | app/Services/DashboardService.php, 4 kartu ringkasan, tren 7 hari (nilai 0 hari kosong), top barang hari ini, 10 aktivitas audit log (null -> Sistem) |
| 43 | Test Suite Feature Data Nyata Laporan & Dashboard | Selesai | tests/Feature/LaporanPenjualanDataTest.php (8 passed, 35 assertions) dan DashboardDataTest.php (5 passed, 21 assertions) |
| 44 | Layanan BackupService & integrasi DB nyata (Figma P15) | Selesai | app/Services/BackupService.php, integrasi sp_catat_backup, BackupController data riil, tests/Feature/BackupDataTest.php (4 passed, 26 assertions) |

---

## Daftar Berkas Proyek per Modul Admin

### 1. Modul Kelola Data Barang (Figma P12)
- `routes/admin_barang.php`: Grup rute mandiri data barang (`admin.barang`, `admin.barang.store`, dll).
- `app/Http/Controllers/Admin/BarangController.php`: Controller Eloquent & Stored Procedure (index, store, update, updateHarga, destroy).
- `app/Models/ItemBarang.php`: Model pembaca tabel `daftar_item` dengan accessor `status_stok`.
- `app/Models/Pengguna.php`: Model pengguna dengan hash password khusus.
- `resources/views/admin/barang/index.blade.php`: Halaman utama inventori barang (tabel, search, filter, urut).
- `resources/views/admin/barang/modals.blade.php`: Modal dialog responsive sticky zoom 100% (Tambah, Ubah Data, Ubah Harga, Hapus).
- `tests/Feature/BarangCrudTest.php`: Uji operasi tulis stored procedure & jejak audit.
- `tests/Feature/BarangDaftarTest.php`: Uji pembacaan daftar, pencarian, filter, dan paginasi 7 baris.
- `tests/Feature/BarangAdminAccessTest.php`: Uji otorisasi hak akses dan proteksi SVG.

### 2. Modul Laporan Penjualan (Figma P11)
- `routes/admin_laporan.php`: Rute mandiri `GET /admin/laporan-penjualan` (`admin.laporan.penjualan`).
- `app/Http/Controllers/Admin/LaporanPenjualanController.php`: Controller laporan penjualan (filter rentang, 12 dataset transaksi realistis, paginasi 5 item).
- `resources/views/admin/laporan/penjualan.blade.php`: Tampilan laporan penjualan (3 kartu metrik ringkasan, riwayat transaksi tabel, badge retur subtotal merah, print PDF).
- `resources/views/admin/laporan/pagination.blade.php`: Komponen navigasi paginasi kustom untuk laporan penjualan.
- `tests/Feature/LaporanPenjualanAccessTest.php`: Uji akses, render elemen, filter tanggal, dan proteksi K12.

### 3. Modul Pembelian Barang / Restock (Figma P21)
- `routes/admin_pembelian.php`: Rute mandiri `GET /admin/pembelian` (`admin.pembelian`).
- `app/Http/Controllers/Admin/PembelianController.php`: Controller pembelian barang (data 3 item restock dan 4 riwayat transaksi).
- `resources/views/admin/pembelian/index.blade.php`: Tampilan pembelian barang sesuai Figma P21 (Tab Pembelian Baru & Riwayat, kartu Perlu Segera Restock, tombol Catat Restock).
- `tests/Feature/PembelianAdminTest.php`: Uji otorisasi, render konten restock, bebas `<svg`, partial skala, dan menu aktif.

### 4. Modul Kelola Akun Pengguna (Figma P13)
- `routes/admin_pengguna.php`: Rute mandiri `GET /admin/pengguna` (`admin.pengguna`).
- `app/Http/Controllers/Admin/PenggunaController.php`: Controller manajemen pengguna dengan data master peran Admin dan Kasir.
- `resources/views/admin/pengguna/index.blade.php`: Tampilan daftar pengguna, tabel hak akses, indikator status AKTIF/NONAKTIF, dan modal manajemen akun.
- `tests/Feature/PenggunaAdminTest.php`: Uji akses, struktur tabel pengguna, bebas SVG, dan skala tampilan.

### 5. Modul Batas Stok Minimum (Figma P14)
- `routes/admin_batas_stok.php`: Rute mandiri `GET /admin/batas-stok` (`admin.batas-stok`).
- `app/Http/Controllers/Admin/BatasStokController.php`: Controller batas stok minimum (5 data item contoh Figma P14).
- `resources/views/admin/batas_stok/index.blade.php`: Banner bot Telegram teks murni, stepper tombol teks `[-]` dan `[+]`, tombol aksi cepat 3/5 pcs, dan bilah simpan mengambang (*floating bar*).
- `tests/Feature/BatasStokAdminTest.php`: Uji HTTP 200, bebas SVG, partial skala 80%, dan verifikasi elemen stepper.

### 6. Modul Dashboard Admin (Figma P10)
- `routes/admin_dashboard.php`: Rute mandiri `GET /admin/dashboard` (`admin.dashboard`).
- `app/Http/Controllers/Admin/DashboardController.php`: Controller ringkasan toko hari ini, data diagram 7 hari, top barang, dan aktivitas terbaru.
- `resources/views/admin/dashboard/index.blade.php`: Sapaan "Halo, Junaidi", 4 kartu metrik besar, diagram batang 7 hari murni Tailwind CSS (tanpa Chart.js), kartu Top Barang, dan kartu Aktivitas Terbaru.
- `tests/Feature/DashboardAdminTest.php`: Uji HTTP 200, konten ringkasan, diagram batang, bebas SVG, dan sidebar aktif.

### 7. Modul Backup Data (Figma P15)
- `routes/admin_backup.php`: Rute mandiri `GET /admin/backup` (`admin.backup`).
- `app/Services/BackupService.php`: Layanan backend pencadangan database (`getRiwayatBackup`, `catatBackup`, `prosesBackup`) terhubung tabel `riwayat_backup` dan Stored Procedure `sp_catat_backup`.
- `app/Http/Controllers/Admin/BackupController.php`: Controller pencadangan database terhubung data riil via BackupService.
- `resources/views/admin/backup/index.blade.php`: Kartu Backup Manual (`border-emerald-500`), kartu Backup Otomatis (`border-slate-200`), tabel riwayat backup, dan kotak pemberitahuan keamanan lokal.
- `tests/Feature/BackupAdminTest.php`: Uji prototype HTTP 200, kartu backup, tabel unduh, bebas SVG, dan partial skala 80%.
- `tests/Feature/BackupDataTest.php`: Feature test data nyata MariaDB (4 passed, 2 skipped, 26 assertions).

### 8. Modul Log Aktivitas (Figma P16)
- `routes/admin_log.php`: Rute mandiri `GET /admin/log-aktivitas` (`admin.log`).
- `app/Http/Controllers/Admin/LogAktivitasController.php`: Controller riwayat jejak audit (10 data log transaksi, stok, login, barang, harga, modal).
- `resources/views/admin/log/index.blade.php`: Tombol teks "Ekspor CSV", 3 dropdown filter, tabel riwayat log dengan badge status lembut, dan info footer total log.
- `tests/Feature/LogAktivitasAdminTest.php`: Uji HTTP 200, teks log, tombol ekspor, bebas SVG, dan partial skala 80%.

### 9. Autentikasi Staf & Two-Factor Authentication (2FA)
- `routes/web.php`: Rute utama `/masuk` (`login`) dan `/verifikasi` (`verifikasi`), redirect alias dari `/login` ke `/masuk`.
- `app/Http/Controllers/LoginController.php`: Controller login validasi username/password dan dispatch OTP Telegram.
- `app/Http/Controllers/OtpController.php`: Controller verifikasi kode 6 digit OTP dan redirect cerdas sesuai role (Admin ke `/admin/dashboard`, Kasir ke `/kasir/dashboard`).
- `app/Services/TelegramService.php`: Pengiriman pesan kode OTP ke grup Telegram staf.

### 10. Sesi Kedaluwarsa & Halaman Error 419 (K12/K18)
- `resources/views/auth/expired.blade.php`: Tampilan kartu sesi berakhir dengan tombol Masuk Kembali dan Kembali ke Beranda.
- `resources/views/errors/419.blade.php`: Penanganan error page 419 Page Expired menggunakan template yang sama.
- `routes/web.php`: Rute statis `GET /sesi-berakhir` (`auth.expired`).

### 11. Integrasi Telegram Bot Webhook (STATUS: PENDING)
- `app/Services/Telegram/TelegramBotService.php`: Layanan HTTP client API bot Telegram.
- `app/Services/Telegram/TelegramReportService.php`: Layanan generator PDF rekap penjualan via DomPDF dan caption HTML.
- `resources/views/admin/laporan/pdf.blade.php`: Template cetak A4 laporan penjualan harian.
- `app/Http/Controllers/TelegramWebhookController.php`: Controller pemroses pesan teks dan interaksi callback query.
- `app/Console/Commands/SetTelegramCommands.php`: Artisan command `telegram:set-commands` pendaftaran menu bot.
- `routes/api.php`: Endpoint `POST /api/telegram/webhook` (status PENDING on-hold).

---

## Skala Tampilan & Arsitektur Global
- `resources/views/partials/skala.blade.php`: Partial `:root { --skala: 80%; } html { font-size: var(--skala); }` untuk keseragaman ukuran Figma 100% di semua halaman.
- `tests/Feature/TampilanSkalaTest.php`: Uji integrasi keberadaan partial skala pada halaman admin.
