# Toko Firo

Sistem informasi berbasis web untuk pengelolaan data master barang, stok inventori, riwayat transaksi, laporan penjualan, pembelian restock, serta manajemen akun pengguna dan hak akses pada Toko Firo (usaha ATK dan fotokopi).

Dikerjakan untuk mata kuliah Sistem Informasi / Rekayasa Perangkat Lunak, Universitas Negeri Malang, 2026.

## Anggota Kelompok
- **Muhammad Rossi Ramadhan** (250535625057) — Ketua Kelompok, Database, Modul Data Barang, & Modul Kelola Akun Pengguna
- **Muhammad Totti Al Fikri** (250535630175) — Backend Developer
- **Refina Aprilia Dewi** (250535624776) — UI/UX Designer & Analis Sistem
- **Teofilus Aditya Suseno Pandeangan** (250535626205) — Frontend, Backend, & QA Tester

## Teknologi & Lingkungan Pengembangan
- **Framework**: Laravel 12.0
- **Bahasa Pemrograman**: PHP 8.4 (FlyEnv / PHP 8.2+)
- **Database**: MariaDB 13.1.1 (dengan integritas logika pada Stored Procedure, View, & Trigger)
- **Frontend & Styling**: Blade Templates & Tailwind CSS (tanpa Livewire dan tanpa Alpine.js; interaktivitas modal dan filter berbasis Vanilla JavaScript murni)
- **Lingkungan Lokal**: Windows 11 dengan FlyEnv runtime environment

## Struktur Dokumentasi
- `HANDOFF_CRUD_TOKO_FIRO.md`: Panduan serah terima resmi arsitektur teknis, isolasi rute, dan mitigasi merge modul barang
- `DATABASE.md`: ERD, skema tabel, view, stored procedure, trigger, dan hak akses
- `database/sql/`: Skrip SQL berurutan 00 sampai 09 (skema inti, tabel baru, view, SP, trigger, dan SP v2)
- `TRACKING.md`: Log pengerjaan berkala dan status tiap tahapan milestone
- `koreksi_copilot.md`: Keputusan teknis final pemilik (K1 sampai K16) dan verifikasi langkah kerja L0 sampai L16

## Progress Implementasi Modul Antarmuka (Figma)
| Frame | Modul | Branch | Status |
|---|---|---|---|
| P05 | Kasir - Dashboard | Refina-Toti/Dashboard_Kasir | Selesai (Frontend Prototype K12/K18) |
| P06 | Kasir - Transaksi Penjualan | Refina-Toti/Sales-Transaction_Kasir | Selesai (Frontend Prototype K12/K18) |
| P07 | Kasir - Lihat Stok Barang | Refina-Toti/Stock-Data_Kasir | Selesai (Frontend Prototype K12/K18) |
| P08 | Kasir - Saldo Harian | Refina-Toti/Daily-Balance_Kasir | Selesai (Frontend Prototype K12/K18) |

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

## Workflow Pengerjaan (Dari Awal hingga Akhir)
1. **Tahap 1 - Analisis & Perancangan**: Penyusunan diagram Use Case, Activity, Sequence, perancangan skema relasional tabel inventori (`daftar_item`), dan pencatatan audit trail (`log_aktivitas`).
2. **Tahap 2 - Prototype Frontend (Milestone 1)**: Pembuatan antarmuka visual awal (`routes/web.php` route `/demo/barang`) dengan 47 item dummy, paginasi 7 baris, dan penyesuaian label tombol aksi tanpa ikon SVG.
3. **Tahap 3 - Fondasi Database & Stored Procedure (L0 - L5)**: Penerapan skrip SQL `00` s.d. `09` pada MariaDB `toko_firo` dan `toko_firo_test`. Pembangunan model `Pengguna`, `RoleMiddleware`, dan pengujian DDL.
4. **Tahap 4 - Isolasi Arsitektur Modul Barang (L6 - L7)**:
   - Pembuatan rute mandiri `routes/admin_barang.php` yang dimuat via `bootstrap/app.php` tanpa menyentuh `routes/web.php`.
   - Pembuatan model baru `app/Models/ItemBarang.php` untuk membaca tabel `daftar_item` tanpa menimpa `app/Models/DaftarItem.php` milik rekan tim.
   - Pembuatan `app/Http/Controllers/Admin/BarangController.php` dan pemindahan view ke `resources/views/admin/barang/`.
5. **Tahap 5 - Implementasi CRUD Lengkap & Jejak Audit (L8 - L15)**: Integrasi form modal dengan stored procedure MariaDB, penanganan error riwayat transaksi, aktivasi filter/urutan, dan pengujian feature test hingga seluruh assertions lulus.
6. **Tahap 6 - QA, Zoom 100%, & Decoupling Auth (L16)**:
   - Penataan ulang CSS modal Tailwind agar proporsional pada browser zoom 100% tanpa terpotong.
   - Pelepasan sementara ketergantungan middleware auth pada rute barang lokal untuk mencegah konflik dengan modul Auth/2FA rekan tim saat merge ke `staging`.
   - Pembaruan dokumen serah terima dan pergantian nama branch kerja resmi menjadi `Rossi/Manage-Product-Data-Items_Admins`.

## Cara Menjalankan (Lingkungan Lokal)
1. Clone repository dan beralih ke branch kerja:
   ```bash
   git checkout Rossi/User-Settings
   ```

2. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`):**
   - Salin file konfigurasi:
     ```bash
     Copy-Item .env.example .env
     ```
   - Generate Application Key:
     ```bash
     php artisan key:generate
     ```
   - Sesuaikan konfigurasi database MariaDB pada `.env` (`DB_DATABASE=toko_firo`).

4. **Eksekusi Skrip Database MariaDB:**
   - Jalankan skrip SQL di folder `database/sql/` berurutan dari `00_preflight.sql` sampai `09_procedure_ubah_hapus.sql` pada database `toko_firo` dan `toko_firo_test`.

5. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Akses modul melalui peramban web:
   - **Kelola Akun Pengguna**: `http://127.0.0.1:8000/admin/pengguna`
   - **Kelola Data Barang**: `http://127.0.0.1:8000/admin/barang`
   - **Riwayat Transaksi**: `http://127.0.0.1:8000/admin/barang/riwayat`
   - **Pembelian Barang (Restock)**: `http://127.0.0.1:8000/admin/pembelian`
   - **Laporan Penjualan**: `http://127.0.0.1:8000/admin/laporan-penjualan`

---

## Pengujian Otomatis (Testing)
Pengujian modul dijalankan menggunakan PHPUnit / Pest dengan trait `DatabaseTransactions` untuk menjaga integritas data:

```bash
# Jalankan seluruh test suite
php artisan test

# Pengujian khusus modul Kelola Akun Pengguna
php artisan test --filter=PenggunaAdminTest

# Pengujian modul terkait lainnya
php artisan test --filter=BarangDaftarTest
php artisan test --filter=BarangRiwayatTest
php artisan test --filter=PembelianAdminTest
```

---

## Aturan Keamanan & Integritas Data
- Kredensial sensitif database dan API dilarang keras di-commit ke repositori (terlindungi di `.gitignore` melalui `.env`).
- Seluruh formulir pengiriman data dilindungi token proteksi `@csrf`.
- Form modal menggunakan validasi FormRequest ketat (`StoreBarangRequest`, `UpdateBarangRequest`, dll).
- Penghapusan data barang salah input dilindungi validasi ganda di level database (Stored Procedure `sp_delete_item_v2`) untuk mencegah terhapusnya barang yang memiliki riwayat transaksi penjualan.
- Setiap aktivitas penambahan, pengubahan, perubahan harga, dan penghapusan data otomatis tercatat ke dalam tabel audit trail `log_aktivitas`.

---

## Strategi Branching Git
- **`main`**: Versi rilis produksi yang stabil dan siap guna.
- **`staging`**: Basis cabang integrasi bersama seluruh tim sebelum rilis ke `main`.
- **`Rossi/User-Settings`**: Cabang kerja resmi untuk modul Kelola Akun Pengguna dan penyelarasan pengaturan sistem.
- **`Rossi/Manage-Product-Data-Items_Admins`**: Cabang kerja resmi untuk modul Kelola Data Barang dan Riwayat Transaksi.
```
