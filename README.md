Toko Firo

Sistem informasi berbasis web untuk pengelolaan data barang, stok, transaksi penjualan, dan laporan pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

Anggota Kelompok

Muhammad Rossi Ramadhan (250535625057), Ketua Kelompok, Database, Modul Data Barang, Laporan Penjualan, dan Pembelian Barang

Muhammad Totti Al Fikri (250535630175), Backend

Refina Aprilia Dewi (250535624776), UI/UX dan Analisis

Teofilus Aditya Suseno Pandeangan (250535626205), Frontend, Backend, dan QA

Teknologi

Framework: Laravel 12, PHP 8.2 ke atas (dikembangkan dengan PHP 8.4 via FlyEnv)

Database: MariaDB 13.1.1, dengan logika bisnis pada Stored Procedure & Trigger

Frontend: Blade dan Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas tab navigasi dan filter via Vanilla JavaScript murni)

Lingkungan Pengembangan: Windows dengan FlyEnv

Struktur Dokumentasi

HANDOFF_CRUD_TOKO_FIRO.md: Panduan serah terima resmi arsitektur teknis, isolasi rute, dan mitigasi merge modul mandiri

DATABASE.md: ERD, skema tabel (daftar_pembelian, daftar_item), view, stored procedure (sp_create_pembelian), trigger, dan hak akses

database/sql/: Skrip SQL berurutan 00 sampai 09 (skema inti, tabel restock, SP, trigger, dan seed dev)

TRACKING.md: Log pengerjaan berkala dan status tiap tahapan milestone modul pembelian barang

koreksi_copilot.md: Keputusan teknis final pemilik (K1 sampai K19) dan verifikasi antarmuka

PROJECT_STATUS.md: Laporan status teknis menyeluruh internal (status branch, file, test suite, dan kesiapan merge)

Status Fitur: Modul Pembelian Barang / Restock (Admin)

Modul Pembelian Barang Restock (sesuai prototype Figma frame P21) telah diimplementasikan secara mandiri dan terisolasi:

Dua Tab Interaktif (Vanilla JavaScript):

Tab Pembelian Baru: Menampilkan dashboard kebutuhan restock dan kartu pemantauan stok menipis.

Tab Riwayat Pembelian: Menampilkan rekapitulasi nota pembelian barang yang telah dilakukan.

Berpindah tab secara instan di sisi klien tanpa perlu memuat ulang halaman (reload).

Kartu "Perlu Segera Restock":

Menampilkan daftar barang dengan stok kritis berlatar kuning muda dan indikator status teks merah (3 data contoh realistis: Pensil 2B - Habis, Isi Staples No.10 - Menipis, Kertas HVS A4 - Menipis).

Aksi Cepat Restock:

Tombol aksi "Catat Restock" berwarna hijau dengan touch-target minimal 44px (min-h-[2.75rem]) untuk mencatat barang masuk setelah pembelian eksternal (Shopee/grosir fisik).

Tabel Riwayat Pembelian:

Menampilkan 4 transaksi contoh terstruktur: Waktu & Tanggal, Kode Pembelian (PB-20261018-001), Nama Barang, Jumlah, Total (format nominal Rupiah), dan status badge "Selesai" (bg-emerald-100 text-emerald-800).

Kepatuhan UI/UX (K12) & Skala Global 80% (K18):

Bersih dari tag <svg>, ikon grafis, maupun karakter emoji.

Seluruh tombol interaktif memiliki tinggi minimal 44px dan font minimal 16px (text-base).

Terintegrasi dengan partial skala global 80% agar tampilan proporsional pada peramban zoom 100%.

Arsitektur Skala Tampilan Global (K12 Adaptif)

Untuk memastikan antarmuka pada zoom browser 100% presisi dengan desain Figma (lebar sidebar ~260px, bukan 325px yang terjadi jika font-size akar tidak disesuaikan):

Menggunakan partial global terpusat: resources/views/partials/skala.blade.php.

Skala akar halaman ditetapkan sebesar 80% (:root { --skala: 80%; } html { font-size: var(--skala); }).

Seluruh elemen tetap mematuhi standar touch-target K12: ukuran tombol minimal 2.75rem (setara 44px pada font basis 16px) dan font minimum 1rem (text-base).

Diterapkan seragam pada halaman Pembelian Barang (admin.pembelian) dan sidebar navigasi admin.

Isolasi Arsitektur & Decoupling Sesi

Untuk mencegah konflik (conflict) merge dengan modul Autentikasi dan branch tim lain:

Rute modul pembelian didaftarkan mandiri di routes/admin_pembelian.php.

Controller terisolasi di app/Http/Controllers/Admin/PembelianController.php.

Tampilan antarmuka berada di resources/views/admin/pembelian/index.blade.php.

Berkas rute dimuat otomatis melalui pendaftaran mandiri pada bootstrap/app.php tanpa memodifikasi routes/web.php milik rekan tim.

Middleware auth dan role:ADMIN dilepas sementara pada rute lokal (dengan komentar pengingat pemulihan TODO WAJIB).

Pada View, pembacaan user menggunakan fallback aman null-safe: Auth::user()?->nama ?? 'Admin / Pemilik' pada kartu profil sidebar.

Cara Menjalankan (Lingkungan Lokal)

Beralih ke branch kerja:

git checkout Rossi/Purchase_Admins


Instal dependensi PHP (jika belum):

composer install


Konfigurasi berkas .env:

Pastikan variabel DB_DATABASE=toko_firo dan konfigurasi aplikasi sudah sesuai.

Jalankan server lokal:

php artisan serve


Akses modul langsung di browser:

http://127.0.0.1:8000/admin/pembelian


Pengujian (Testing)

Pengujian fitur antarmuka modul pembelian dijalankan menggunakan PHPUnit / Pest:

php artisan test --filter=PembelianAdminTest


Status pengujian modul pembelian: 4 passed, 2 skipped (otorisasi auth dilepas sementara untuk pengujian lokal mandiri), 0 failed (16 assertions).

Pengujian memverifikasi:

Status respons HTTP 200 pada rute admin.pembelian.

Keberadaan kartu "Perlu Segera Restock", teks "Catat Restock", dan kode transaksi PB-.

Ketiadaan tag <svg> (K12).

Pemuatan partial skala 80% (K18).

Status aktif menu navigasi sidebar "Pembelian Barang".

Aturan Keamanan

Seluruh interaksi form mendatang dilindungi dengan token proteksi @csrf.

Isolasi endpoint admin mencegah injeksi rute publik yang tidak terotorisasi.

Fallback sesi null-safe mencegah aplikasi mengalami crash saat data otentikasi belum lengkap.

Strategi Branching

main: Versi rilis produksi yang stabil.

staging: Basis integrasi utama tim sebelum penggabungan ke main.

Rossi/Manage-Product-Data-Items_Admins: Modul master Kelola Data Barang & Riwayat Transaksi (P12).

Rossi/Sales_Report_Admins: Modul Laporan Penjualan (P11).

Rossi/Purchase_Admins: Branch aktif saat ini untuk modul Pembelian Barang Restock (P21).

Rossi/User-Settings: Modul Kelola Akun Pengguna (P13).