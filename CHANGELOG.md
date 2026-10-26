# Changelog - Toko Firo

Seluruh perubahan penting pada proyek Toko Firo dicatat dalam berkas ini.

Format pencatatan mengacu pada prinsip [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased] - Branch `staging` (26 Oktober 2026)

### Added
- **Penyambungan Rute Backend Kasir & Admin Pembelian**:
  - `routes/kasir_stok.php`: Pendaftaran rute endpoint AJAX pencarian cepat barcode scanner `GET /kasir/stok/cari` (`kasir.stok.cari`).
  - `routes/kasir_transaksi.php`: Pendaftaran endpoint POS penjualan `POST /kasir/transaksi/simpan` (`kasir.transaksi.simpan`), detail transaksi `GET /kasir/transaksi/{nota}/detail` (`kasir.transaksi.detail`), dan cetak resi/struk thermal `GET /kasir/transaksi/{nota}/cetak` (`kasir.transaksi.cetak`).
  - `routes/admin_pembelian.php`: Pendaftaran endpoint pencatatan pembelian restock barang baru `POST /admin/pembelian` (`admin.pembelian.store`).
- **Migrasi Database Skema Saldo Harian**:
  - Eksekusi migrasi pending `2026_10_24_105035_add_saldo_akhir_to_saldo_harian_table` untuk penambahan kolom `saldo_akhir` pada tabel `saldo_harian`.
- **Hasil Pengujian Validasi 100% Lulus**:
  - `tests/Feature/Kasir/StokKasirTest.php`: 5 passed (100%).
  - `tests/Feature/StokKasirTest.php`: 5 passed (100%).
  - `tests/Feature/Kasir/TransaksiKasirTest.php`: 5 passed (100%).
  - `tests/Feature/TransaksiKasirTest.php`: 5 passed (100%).
  - `tests/Feature/PembelianDataTest.php`: 5 passed, 2 skipped auth (100%).
  - `tests/Feature/DashboardKasirTest.php`: HTTP 200 bebas error 500.

### Added
- **Sistem Telegram Bot Webhook Dua Arah Aman (Cyber Security Hardened)**:
  - `app/Http/Controllers/TelegramWebhookController.php`: Validasi wajib header `X-Telegram-Bot-Api-Secret-Token` (403 Forbidden bila token invalid) dan whitelist otorisasi `chat_id` pengirim sebelum eksekusi payload.
  - Penanganan NLP ringkas (keyword *laporan penjualan*, *laporan hari ini*, *rekap hari ini*), 10 submenu Admin, 5 submenu Kasir, dan callback query acknowledgement.
  - `app/Console/Commands/KirimRekapHarianTelegram.php`: Perintah console `app:kirim-rekap-telegram` untuk pengiriman otomatis rekap omzet beserta PDF resmi ke grup Telegram toko.
- **Enkripsi Cadangan Database (ZIP AES-256) & Notifikasi Telegram**:
  - `app/Services/BackupService.php`: Pengemasan dump SQL ke dalam arsip `.zip` terenkripsi AES-256 (`\ZipArchive::EM_AES_256`) menggunakan password terkonfigurasi, pengiriman otomatis dokumen ke Admin Telegram, sanitasi penghapusan file sementara (`unlink`), dan pencatatan audit log via `sp_catat_backup`.
- **Alert Stok Menipis Otomatis**:
  - `app/Services/BatasStokService.php`: Hook notifikasi otomatis peringatan stok kritis (`stok <= batas_minimum`) langsung ke Telegram Admin saat pembaruan batas stok.
- **Otomatisasi Dokumen PDF (DomPDF)**:
  - `resources/views/pdf/lembar_restock.blade.php`: Template PDF cetak rekomendasi restock A4 portrait (K12 compliant, pure CSS table).
- **Automated Feature Test Suites**:
  - `tests/Feature/TelegramWebhookSecurityTest.php` (6 passed, 40 assertions).
  - `tests/Feature/TelegramServiceTest.php` (4 passed, 19 assertions).
  - `tests/Feature/ExpiredSessionUiTest.php` (4 passed, 14 assertions).

### Changed
- **Konfigurasi & Keamanan Rute**:
  - `config/services.php`: Blok konfigurasi `telegram` terpusat (`bot_token`, `chat_id`, `admin_chat_id`, `webhook_secret`, `backup_password`).
  - `routes/api.php`: Pemasangan rate limiting `throttle:30,1` pada rute webhook `POST /api/telegram/webhook`.
  - `bootstrap/app.php`: Pengecualian verifikasi CSRF untuk `api/telegram/webhook`.

---

## [Unreleased] - Branch `Rossi/Min-Stock-Threshold_Admins` (25 Oktober 2026)

### Added
- **Backend Batas Stok Minimum (Data Nyata MariaDB)**:
  - `app/Services/BatasStokService.php`: Query builder `daftar_item` terpaginasi 10 item per halaman, filter jenis item, pencarian keyword kebal SQL injection, pemetaan status stok (Habis, Menipis, Normal, Aman), dan eksekusi Stored Procedure `sp_set_batas_minimum` (satuan) serta `sp_set_batas_minimum_semua` (massal).
  - Test suite data nyata `tests/Feature/BatasStokDataTest.php` (8 passed, 2 skipped, 30 assertions).

### Changed
- **BatasStokController**:
  - Menghapus dataset mock statis lama dan mengalihkan pemanggilan ke `BatasStokService`.
  - Mengintegrasikan pembaruan batas stok satuan dan massal dengan validasi input batas minimum (0 - 1000).

---

## [Unreleased] - Branch `staging` (25 Oktober 2026)

### Added
- **Backend Laporan Penjualan & Dashboard Admin (Data Nyata)**:
  - `app/Services/LaporanPenjualanService.php`: perhitungan ringkasan penjualan kotor, jumlah nota, barang terjual bersih, barang terlaris, estimasi laba bersih, dan riwayat transaksi terpaginasi (5 per halaman) dengan rentang tanggal inklusif.
  - `app/Services/DashboardService.php`: penyedia metrik 4 kartu hari ini (penjualan, transaksi, stok menipis, laba), volume 7 hari mingguan (nilai 0 untuk hari kosong), top barang hari ini, dan 10 aktivitas log terbaru (pelaku null dipetakan ke "Sistem").
  - Test suite data nyata `tests/Feature/LaporanPenjualanDataTest.php` (8 passed, 2 skipped, 35 assertions).
  - Test suite data nyata `tests/Feature/DashboardDataTest.php` (5 passed, 2 skipped, 21 assertions).

### Changed
- **LaporanPenjualanController & DashboardController**:
  - Menghapus method mock `dataContoh()` dan dataset dummy admin pada controller.
  - Mengintegrasikan pembacaan data nyata via `LaporanPenjualanService` dan `DashboardService`.
  - Validasi parameter tanggal ketat (`Y-m-d`) dengan batas rentang kustom maksimal 366 hari serta proteksi injeksi SQL.

## [Unreleased] - Branch `staging` (22 Oktober 2026)

### Added
- **UI Sesi Kedaluwarsa (Expired)**:
  - Berkas tampilan `resources/views/auth/expired.blade.php` dan `resources/views/errors/419.blade.php`.
  - Rute statis `GET /sesi-berakhir` (`auth.expired`).
  - Mematuhi standar K12 (bebas `<svg>`, bebas emoji grafis) dan K18 (touch-target tombol `min-h-[44px]`, font minimal 16px).
- **Layanan Telegram Bot & Generator PDF**:
  - `app/Services/Telegram/TelegramBotService.php` untuk komunikasi API Telegram (pesan, dokumen, callback query, command).
  - `app/Services/Telegram/TelegramReportService.php` untuk ekstraksi data penjualan dan pembuatan berkas PDF via DomPDF.
  - Template cetak laporan penjualan harian A4 pada `resources/views/admin/laporan/pdf.blade.php`.
  - Controller webhook `app/Http/Controllers/TelegramWebhookController.php` pada rute `POST /api/telegram/webhook`.
  - Artisan command `app/Console/Commands/SetTelegramCommands.php` (`telegram:set-commands`) untuk mendaftarkan command `/start`, `/menu`, `/menu_admin`, dan `/menu_kasir`.
  - Test suite `tests/Feature/TelegramWebhookAndExpiredTest.php` (5 test passed, 21 assertions).

### Changed
- **Autentikasi & 2FA**:
  - Menstandarkan rute autentikasi utama ke `/masuk` (`LoginController`) dengan nama rute `login`.
  - Menghapus pemanggilan `AuthController` lama dan menambahkan alias pengalihan otomatis dari `/login` ke `/masuk`.
  - Mengarahkan alur setelah verifikasi OTP (`OtpController`) sesuai peran pengguna (ADMIN ke `/admin/dashboard`, KASIR ke `/kasir/dashboard`).
  - Mengarahkan tombol "Masuk Staf" pada footer publik (`resources/views/components/footer-publik.blade.php`) ke `route('login')`.
  - Memverifikasi pengiriman OTP dan koneksi bot Telegram via Tinker.
- **Sidebar Modul Admin**:
  - Menyematkan menu "Pengaturan Toko" ke dalam komponen partial terpusat `resources/views/partials/sidebar_admin.blade.php`.
  - Memperbarui styling `<aside>` menjadi `h-screen sticky top-0 flex-shrink-0 flex flex-col justify-between overflow-y-auto w-64` agar sidebar terkunci di viewport layar dan tidak ikut tergeser saat konten modul di-scroll.
  - Menyelaraskan pembungkus `<main>` di seluruh modul admin menjadi `flex-1 min-w-0 p-8`.

### Pending
- **Telegram Bot Webhook (Status: ON HOLD)**:
  - Fungsionalitas penerimaan pesan dua arah saat ini **ditunda (*on hold*)** menunggu penyelesaian final modul backend admin.
  - Catatan teknis keamanan (Cyber Security Checklist) wajib diselesaikan sebelum pendaftaran webhook publik:
    1. Validasi header `X-Telegram-Bot-Api-Secret-Token`.
    2. Whitelist `TELEGRAM_GROUP_CHAT_ID` / ID pengirim terdaftar.
    3. Penerapan rate limiting middleware `throttle:30,1`.

