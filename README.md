# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data barang, stok, transaksi penjualan, dan laporan pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

## Anggota Kelompok
- Muhammad Rossi Ramadhan (250535625057), Ketua Kelompok, Database, Modul Data Barang, Laporan Penjualan, dan Pembelian Barang
- Muhammad Totti Al Fikri (250535630175), Backend
- Refina Aprilia Dewi (250535624776), UI/UX dan Analisis
- Teofilus Aditya Suseno Pandeangan (250535626205), Frontend, Backend, dan QA

## Teknologi
- Framework: Laravel 12, PHP 8.2 ke atas (dikembangkan dengan PHP 8.4 via FlyEnv)
- Database: MariaDB 13.1.1, dengan logika bisnis pada Stored Procedure & Trigger
- Frontend: Blade dan Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas modal, filter, dan tab navigasi via Vanilla JavaScript)
- Lingkungan Pengembangan: Windows dengan FlyEnv

## Struktur Dokumentasi
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima resmi arsitektur teknis, isolasi rute, dan mitigasi merge modul barang, laporan penjualan, & pembelian barang
- `DATABASE.md`: ERD, skema tabel, view, stored procedure, trigger, hak akses, dan catatan prosedur v2
- `database/sql/`: Skrip SQL berurutan 00 sampai 09 (skema inti, tabel baru, view, SP, trigger, dan SP v2)
- `TRACKING.md`: Log pengerjaan berkala dan status tiap tahapan milestone proyek
- `koreksi_copilot.md`: Keputusan teknis final pemilik (K1 sampai K19) dan verifikasi langkah kerja
- `PROJECT_STATUS.md`: Laporan status teknis menyeluruh internal (status branch, file, test suite, dan kesiapan merge)

## Status Fitur: Modul Kelola Data Barang (Admin)
Modul Kelola Data Barang telah selesai 100% dan terisolasi secara mandiri:
- **Tabel & Paginasi**: Pembacaan data riil dari tabel `daftar_item` via Eloquent model `ItemBarang` dengan paginasi 7 item per halaman.
- **Status Stok Otomatis**: Menghitung status stok secara real-time: `Habis` (stok = 0), `Menipis` (stok ≤ batas_minimum), dan `Aman` (stok > batas_minimum).
- **Pencarian, Filter, & Urutan**: Fitur pencarian kode/nama barang (`q`), dropdown filter Jenis unik dari database, dan 3 opsi pengurutan (Terbaru, Nama A-Z, Stok Terkecil) dengan persistensi query string pada paginasi.
- **Pilih Baris & Aksi Dinamis**: Seleksi baris tabel untuk mengaktifkan tombol Ubah Data, Ubah Harga, dan Hapus Data.
- **Operasi Tulis (Stored Procedure)**:
  - **Tambah Barang**: `sp_create_item` (12 input form sesuai Figma, tipe Jasa otomatis stok 999999).
  - **Ubah Data**: `sp_update_item_v2` (tanpa kolom kode, stok, dan harga).
  - **Ubah Harga**: `sp_update_harga` (mendukung teks peringatan jika harga jual < harga pokok sesuai aturan K10).
  - **Hapus Data**: `sp_delete_item_v2` (proteksi integritas transaksi dengan pesan error K13: "Barang tidak bisa dihapus karena sudah pernah dijual.").
- **Jejak Audit (Audit Trail)**: Seluruh operasi tulis otomatis dicatat ke tabel `log_aktivitas` (`ITEM_BARU`, `ITEM_DIUBAH`, `HARGA_DIUBAH`, `ITEM_DIHAPUS`).
- **Kepatuhan UI/UX (K12) & Zoom 100%**: Bebas tag `<svg>`, bebas ikon/grafis/emoji, font minimal 16px, tinggi tombol minimal 44px, serta dialog modal responsif (`max-h-[90vh]` dengan header/footer sticky dan body form scrollable sehingga proporsional pada browser zoom 100%).

## Status Fitur: Modul Laporan Penjualan (Admin)
Modul Laporan Penjualan (sesuai prototype Figma frame P11) telah diimplementasikan:
- **Filter Rentang Waktu Dinamis**:
  - Pilihan cepat: **Hari Ini**, **7 Hari Terakhir**, dan **Bulan Ini**.
  - Pilihan **Rentang Tanggal Kustom**: Input tanggal mulai (`dari`) dan tanggal selesai (`sampai`) dengan validasi format tanggal `Y-m-d` serta pengecekan tanggal awal tidak boleh melewati tanggal akhir.
- **Ringkasan Penjualan (3 Kartu Metrik)**:
  - Menampilkan ringkasan agregat: **Total Penjualan Kotor** (Rp), **Total Transaksi** (transaksi), dan **Laba Bersih Estimasi** (Rp).
- **Riwayat Transaksi & Paginasi**:
  - Menampilkan riwayat transaksi per baris: No, Kode Transaksi, Waktu & Tanggal, Kasir, Item Terjual, Subtotal, dan Status.
  - Paginasi 5 transaksi per halaman dengan navigasi halaman Sebelumnya, Angka Halaman, dan Selanjutnya.
  - Indikator subtotal negatif otomatis ditandai dengan warna merah tegas (`text-red-600`) untuk retur penjualan.
- **Rekap Otomatis Penjualan**:
  - Bagian rekapitulasi terjadwal yang terintegrasi (menampilkan status sinkronisasi, jadwal rekap pukul 21:00 WIB, dan placeholder konfigurasi Telegram bot).
- **Cetak Laporan PDF**:
  - Tombol **Unduh Laporan (PDF)** yang memicu antarmuka cetak bawaan browser (`window.print()`) dengan CSS print-ready.
- **Mock Data Realistis**:
  - Dilengkapi 12 dataset transaksi realistis Toko Firo (ATK, Fotokopi, Jilid, Kertas, dll) yang siap disambungkan ke view/prosedur database riil.

## Status Fitur: Modul Pembelian Barang (Admin)
Modul Pembelian Barang Restock (sesuai prototype Figma frame P21) telah diimplementasikan:
- **Dua Tab Interaktif (Vanilla JavaScript)**: Tab "Pembelian Baru" dan "Riwayat Pembelian" tanpa dependensi library eksternal.
- **Kartu Perlu Segera Restock**: Menampilkan daftar barang dengan stok habis atau menipis berlatar kuning muda dan indikator status merah (3 data contoh: Pensil 2B - Habis, Isi Staples No.10 - Menipis, Kertas HVS A4 - Menipis).
- **Aksi Cepat Restock**: Tombol "Beli" berwarna hijau dengan touch-target minimal 2.75rem.
- **Tabel Riwayat Pembelian**: Menampilkan riwayat transaksi pembelian (4 transaksi contoh lengkap dengan kode pembelian, kuantitas, format nominal Rupiah, dan badge status).
- **Kepatuhan UI/UX (K12) & Skala Global 80%**: Bebas dari ikon/emoji/`<svg`, touch-target minimal 2.75rem, font minimal 1rem, dan skala proporsional Figma 100%.

## Arsitektur Skala Tampilan Global (K12 Adaptif)
Untuk memastikan antarmuka pada zoom browser 100% presisi dengan desain Figma (lebar sidebar ~260px, bukan 325px yang terjadi jika font-size akar tidak disesuaikan):
- Menggunakan partial global terpusat: `resources/views/partials/skala.blade.php`.
- Skala akar halaman ditetapkan sebesar `80%` (`:root { --skala: 80%; } html { font-size: var(--skala); }`).
- Seluruh elemen tetap mematuhi standar touch-target K12: ukuran tombol minimal `2.75rem` (setara 44px pada font basis 16px) dan font minimum `1rem` (`text-base`).
- Diterapkan seragam pada Modul Barang (`admin.barang`), Laporan Penjualan (`admin.laporan.penjualan`), Pembelian Barang (`admin.pembelian`), dan Halaman Demo (`demo.barang`).

## Isolasi Arsitektur & Decoupling Sesi
Untuk mencegah konflik (conflict) merge dengan modul Autentikasi dan 2FA yang sedang dikembangkan oleh rekan tim:
- Rute modul barang didaftarkan mandiri di `routes/admin_barang.php`.
- Rute modul laporan penjualan didaftarkan mandiri di `routes/admin_laporan.php`.
- Rute modul pembelian barang didaftarkan mandiri di `routes/admin_pembelian.php`.
- Seluruh rute dimuat otomatis melalui glob `routes/admin_*.php` pada `bootstrap/app.php` tanpa mengubah file `routes/web.php`.
- Middleware `auth` dan `role:ADMIN` dilepas sementara di level route grup development lokal (dengan komentar pengingat pemulihan `TODO WAJIB`).
- Pada Controller dan View, pembacaan user menggunakan fallback aman null-safe:
  - Controller: `Auth::id() ?? 1` untuk parameter stored procedure jejak audit.
  - View: `Auth::user()?->nama ?? 'Admin / Pemilik'` untuk header sidebar.

## Cara Menjalankan (Lingkungan Lokal)
1. Clone repository dan beralih ke branch kerja:
   ```bash
   git checkout Rossi/Sales_Report_Admins
   ```
2. Instal dependensi PHP:
   ```bash
   composer install
   ```
3. Konfigurasi berkas `.env`:
   - Salin contoh konfigurasi: `cp .env.example .env` (atau `Copy-Item .env.example .env` di PowerShell).
   - Generate application key: `php artisan key:generate`.
   - Sesuaikan kredensial koneksi MariaDB lokal pada `DB_DATABASE=toko_firo`.
4. Eksekusi skrip database MariaDB:
   - Jalankan skrip SQL di `database/sql/` berurutan mulai dari `00_preflight.sql` hingga `09_procedure_ubah_hapus.sql` pada database `toko_firo` dan database pengujian `toko_firo_test`.
5. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
   - Akses Modul Kelola Data Barang: `http://127.0.0.1:8000/admin/barang`
   - Akses Modul Laporan Penjualan: `http://127.0.0.1:8000/admin/laporan-penjualan`
   - Akses Modul Pembelian Barang: `http://127.0.0.1:8000/admin/pembelian`
   - Akses Halaman Demo Prototype: `http://127.0.0.1:8000/demo/barang`

## Pengujian (Testing)
Pengujian fitur dan integritas dijalankan menggunakan PHPUnit / Pest:
```bash
php artisan test
```
Status pengujian saat ini: **46 test (38 lulus, 8 dilewati karena autentikasi dinonaktifkan sementara untuk pengujian independen, 0 gagal, 144 assertions)**.

Filter pengujian per modul:
```bash
php artisan test --filter=BarangCrudTest
php artisan test --filter=BarangDaftarTest
php artisan test --filter=LaporanPenjualanAccessTest
php artisan test --filter=PembelianAdminTest
php artisan test --filter=TampilanSkalaTest
```

## Aturan Keamanan
- Kredensial dan variabel sensitif tidak boleh di-commit ke repositori (wajib disimpan di file `.env`).
- Seluruh form dilindungi dengan token proteksi `@csrf`.
- Form modal menggunakan sanitasi input dan validasi FormRequest yang ketat (`StoreBarangRequest`, `UpdateBarangRequest`, `UpdateHargaBarangRequest`, `DestroyBarangRequest`).
- Proteksi integritas data riwayat transaksi divalidasi ganda di level database (Stored Procedure) dan controller sebelum penghapusan data.

## Strategi Branching
- `main`: Versi rilis produksi yang stabil.
- `staging`: Basis integrasi utama tim sebelum penggabungan ke `main`.
- `Rossi/Manage-Product-Data-Items_Admins`: Branch rilis final modul Kelola Data Barang.
- `Rossi/Sales_Report_Admins`: Branch aktif saat ini untuk modul Laporan Penjualan, Pembelian Barang, dan penyelarasan skala tampilan Figma.
