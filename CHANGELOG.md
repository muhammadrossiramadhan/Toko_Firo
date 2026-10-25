# Changelog - Toko Firo

Seluruh perubahan penting pada proyek Toko Firo dicatat dalam berkas ini.

Format pencatatan mengacu pada prinsip [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased] - Branch `Rossi/Activity-Log_Admins` (25 Oktober 2026)

### Added
- **Modul Log Aktivitas (Data Riil MariaDB)**:
  - `app/Services/LogAktivitasService.php`: layanan pembacaan data audit trail dari tabel `log_aktivitas` (LEFT JOIN `pengguna`), filter tipe aksi, filter rentang tanggal `waktu`, pencarian kata kunci aman dengan parameter binding, paginasi 10 item per halaman (`withQueryString`), dan ekspor data ke format CSV murni.
  - `tests/Feature/LogAktivitasDataTest.php`: feature test suite data riil dengan transaksi database (7 passed, 2 skipped, 27 assertions).

### Changed
- **LogAktivitasController**:
  - Menghapus array mock dummy statis lama dan mengintegrasikan pembacaan data nyata via `LogAktivitasService`.
  - Menambahkan method `ekspor()` untuk pengunduhan berkas CSV stream `log-aktivitas-YYYY-MM-DD.csv` dengan header `text/csv; charset=UTF-8`.

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

