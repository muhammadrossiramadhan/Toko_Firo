# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data barang, stok, transaksi penjualan kasir, akun pengguna, batas stok minimum, cadangan data (backup), notifikasi bot, dan laporan pada Toko Firo (usaha ATK dan fotokopi di Kraksaan, Probolinggo).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

---

## Anggota Kelompok
- **Muhammad Rossi Ramadhan** (250535625057): Ketua Kelompok, Database, Modul Data Barang, Laporan Penjualan, Pembelian Barang, Kelola Akun Pengguna, Batas Stok Minimum, Dashboard Admin, dan Backup Data.
- **Muhammad Totti Al Fikri** (250535630175): Backend & Integrasi Layanan.
- **Refina Aprilia Dewi** (250535624776): UI/UX dan Analisis Kebutuhan Sistem.
- **Teofilus Aditya Suseno Pandeangan** (250535626205): Frontend, Autentikasi 2FA, Backend, dan QA.

---

## Teknologi Utama
- **Framework**: Laravel 12, PHP 8.2 ke atas (dikembangkan dengan PHP 8.4 via FlyEnv).
- **Database**: MariaDB 13.1.1, dengan logika bisnis pada Stored Procedure, Trigger, dan View.
- **Frontend**: Blade + Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas via Vanilla JavaScript).
- **Dokumen PDF**: Barryvdh Laravel DomPDF untuk cetak laporan penjualan resmi A4.
- **Notifikasi & OTP**: Telegram Bot API untuk kode 2FA staf dan integrasi notifikasi berkala toko.
- **Lingkungan Pengembangan**: Windows dengan FlyEnv.

---

## Struktur Dokumentasi
- `README.md`: Ringkasan umum proyek, stack teknologi, arsitektur, dan panduan menjalankan sistem.
- `CHANGELOG.md`: Catatan rilis dan riwayat pembaruan fitur sistem secara kronologis.
- `PROJECT_STATUS.md`: Laporan status teknis menyeluruh internal, status branch, file, dan pengujian.
- `TRACKING.md`: Log pengerjaan berkala dan status tiap tahapan milestone proyek.
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima arsitektur teknis, isolasi rute, dan mitigasi merge modul admin.
- `DATABASE.md`: ERD, skema tabel, view, stored procedure, trigger, hak akses, dan catatan prosedur.
- `koreksi_copilot.md`: Keputusan teknis final pemilik (K1 sampai K19) dan verifikasi standar kerja.

---

## Status Implementasi Fitur

### 1. Autentikasi Staf & Two-Factor Authentication (2FA)
- **Standardisasi Rute**:
  - Jalur masuk utama: `/masuk` (`LoginController@showLoginForm`, named `login`).
  - Verifikasi OTP: `/verifikasi` (`OtpController@showVerifyForm` dan `verify`).
  - Rute lama `/login` dialihkan otomatis via redirect alias ke `/masuk`.
- **Alur Login 2FA Telegram**:
  1. Pengguna memasukkan username dan password di `/masuk`.
  2. Sistem memvalidasi kredensial, menghasilkan kode OTP 6-digit acak (berlaku 5 menit), dan menyimpannya di cache/sesi.
  3. Kode OTP dikirimkan secara instan ke grup Telegram Toko Firo via `TelegramService`.
  4. Pengguna memasukkan OTP di `/verifikasi`. Jika berhasil, pengguna dialihkan ke dashboard sesuai perannya (`/admin/dashboard` untuk ADMIN, `/kasir/dashboard` untuk KASIR).
- **Pengujian Bot via Tinker**: Koneksi bot dan pengiriman pesan telah diverifikasi aktif dan responsif via API Telegram resmi.

### 2. Antarmuka UI Sesi Kedaluwarsa (Expired)
- Mengatasi timeout sesi web dan error HTTP 419 (*Page Expired*) dengan antarmuka ramah pengguna:
  - Berkas: `resources/views/auth/expired.blade.php` dan `resources/views/errors/419.blade.php`.
  - Rute statis: `GET /sesi-berakhir` (`auth.expired`).
- **Kepatuhan K12 & K18**:
  - Desain kartu terpusat dengan logo teks "F", badge "Sesi Telah Berakhir", judul "Waktu Masuk Kedaluwarsa", dan penjelasan keamanan akun.
  - Dua tombol aksi touch-target `min-h-[44px]`: "Masuk Kembali" (`route('login')`) dan "Kembali ke Beranda" (`url('/')`).
  - Bebas tag `<svg>` dan bebas emoji grafis.

### 3. Modul Admin & Sidebar Terkunci (Sticky)
- **Konsistensi Navigasi Sidebar**: Seluruh 10 menu admin terdaftar konsisten melalui komponen terpusat `resources/views/partials/sidebar_admin.blade.php` (Dashboard, Laporan Penjualan, Kelola Data Barang, Pembelian, Kelola Akun Pengguna, Batas Stok Minimum, Backup Data, Log Aktivitas, Pengaturan Notifikasi, dan Pengaturan Toko).
- **Layout Scrolling Terkunci**: Tag `<aside>` distandarkan menggunakan `h-screen sticky top-0 flex-shrink-0 flex flex-col justify-between overflow-y-auto w-64` sehingga sidebar tetap terkunci di viewport layar dan tidak ikut tergeser saat konten modul di-scroll.
- **Skala Adaptif (K18)**: Seluruh tampilan Blade memuat `@include('partials.skala')` (skala 80%) agar proporsi tata letak pada browser zoom 100% presisi dengan desain Figma.

### 4. Status Integrasi Telegram Bot Webhook
> [!IMPORTANT]
> **STATUS FITUR: PENDING (ON HOLD)**
> Fungsionalitas penerimaan pesan dua arah (*two-way Telegram Bot Webhook*) saat ini **ditunda (*on hold*)** menunggu penyelesaian final modul backend admin dan stabilisasi alur transaksi kasir di branch `staging`.

#### Catatan Teknis Keamanan (Cyber Security Checklist):
Saat webhook dua arah diaktifkan pada fase rilis berikutnya, daftar periksa keamanan berikut **wajib dipenuhi**:
1. **Validasi Secret Token**: Wajib memvalidasi header request `X-Telegram-Bot-Api-Secret-Token` sesuai nilai rahasia yang didaftarkan saat `setWebhook` untuk memastikan request berasal dari server resmi Telegram.
2. **Whitelist Chat ID Pengirim**: Wajib memverifikasi bahwa `chat.id` atau `from.id` pengirim terdaftar pada whitelist `TELEGRAM_GROUP_CHAT_ID` atau tabel pengguna terotorisasi sebelum mengeksekusi perintah manajemen atau laporan toko.
3. **Penerapan Rate Limiting**: Wajib menerapkan middleware rate limiting (misal: `throttle:30,1`) pada endpoint webhook untuk mencegah serangan penolakan layanan (*Denial-of-Service* / *flooding*).
4. **Proteksi Dokumen PDF**: Rekap PDF yang digenerate oleh `TelegramReportService` hanya boleh dikirim ke channel atau grup berwenang demi menjaga kerahasiaan data omset toko.

---

## Panduan Menjalankan Sistem (Lokal)

1. **Pastikan berada di branch staging**:
   ```bash
   git checkout staging
   git pull origin staging
   ```

2. **Jalankan Migrasi & Persiapan Server**:
   ```powershell
   & "C:\Program Files\FlyEnv-Data\env\php\php.exe" artisan route:clear
   & "C:\Program Files\FlyEnv-Data\env\php\php.exe" artisan view:clear
   & "C:\Program Files\FlyEnv-Data\env\php\php.exe" artisan serve
   ```

3. **Akses Rute Utama**:
   - Beranda Publik: [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
   - Halaman Masuk Staf: [http://127.0.0.1:8000/masuk](http://127.0.0.1:8000/masuk)
   - Sesi Kedaluwarsa: [http://127.0.0.1:8000/sesi-berakhir](http://127.0.0.1:8000/sesi-berakhir)
   - Dashboard Admin: [http://127.0.0.1:8000/admin/dashboard](http://127.0.0.1:8000/admin/dashboard)
   - Dashboard Kasir: [http://127.0.0.1:8000/kasir/dashboard](http://127.0.0.1:8000/kasir/dashboard)

4. **Jalankan Uji Otomatis**:
   ```powershell
   & "C:\Program Files\FlyEnv-Data\env\php\php.exe" artisan test
   ```
