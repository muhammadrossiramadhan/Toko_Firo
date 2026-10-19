# Log Pengerjaan Toko Firo

## Tabel Status

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

## Daftar File yang Dibuat / Dimodifikasi

### Modul Kelola Data Barang
- `routes/admin_barang.php`: Grup rute mandiri modul data barang (prefix `admin/barang`).
- `app/Http/Controllers/Admin/BarangController.php`: Controller Eloquent & Stored Procedure (index, store, update, updateHarga, destroy).
- `app/Models/ItemBarang.php`: Model pembaca tabel `daftar_item` dengan accessor `status_stok`.
- `app/Models/Pengguna.php`: Model pengguna dengan hash password khusus.
- `resources/views/admin/barang/index.blade.php`: Halaman utama inventori barang (tabel, search, filter, urut).
- `resources/views/admin/barang/modals.blade.php`: Modal dialog responsive sticky zoom 100% (Tambah, Ubah Data, Ubah Harga, Hapus).
- `tests/Feature/BarangCrudTest.php`: Uji fitur operasi tulis stored procedure & jejak audit.
- `tests/Feature/BarangDaftarTest.php`: Uji pembacaan daftar, pencarian, filter, dan paginasi 7 baris.
- `tests/Feature/BarangAdminAccessTest.php`: Uji otorisasi hak akses dan proteksi SVG.

### Modul Laporan Penjualan (Figma P11)
- `routes/admin_laporan.php`: Rute mandiri `GET /admin/laporan-penjualan` (`admin.laporan.penjualan`).
- `app/Http/Controllers/Admin/LaporanPenjualanController.php`: Controller laporan penjualan (filter rentang, validasi tanggal Y-m-d, 12 dataset transaksi realistis, paginasi 5 item per halaman).
- `resources/views/admin/laporan/penjualan.blade.php`: Tampilan laporan penjualan (3 kartu metrik ringkasan, riwayat transaksi tabel, badge retur subtotal merah, print PDF).
- `resources/views/admin/laporan/pagination.blade.php`: Komponen navigasi paginasi kustom untuk laporan penjualan.
- `tests/Feature/LaporanPenjualanAccessTest.php`: 8 pengujian akses, render elemen, filter tanggal, dan proteksi K12.

### Modul Pembelian Barang (Restock - Figma P21)
- `routes/admin_pembelian.php`: Rute mandiri `GET /admin/pembelian` (`admin.pembelian`).
- `app/Http/Controllers/Admin/PembelianController.php`: Controller pembelian barang (data contoh 3 item restock dan 4 riwayat transaksi pembelian).
- `resources/views/admin/pembelian/index.blade.php`: Tampilan pembelian barang sesuai Figma P21 (Tab Pembelian Baru & Riwayat Pembelian, kartu Perlu Segera Restock, baris kuning status merah, tombol Beli hijau).
- `tests/Feature/PembelianAdminTest.php`: 6 pengujian otorisasi, render konten restock, bebas `<svg`, partial skala, dan menu aktif.

### Skala Tampilan Global
- `resources/views/partials/skala.blade.php`: Partial `:root { --skala: 80%; } html { font-size: var(--skala); }` untuk keseragaman ukuran Figma 100% di semua halaman.
- `tests/Feature/TampilanSkalaTest.php`: Uji integrasi keberadaan partial skala pada halaman demo, barang, dan laporan penjualan.

## Keputusan Pemilik (Final)

Keputusan pemilik merujuk pada `koreksi_copilot.md`, K1 sampai K19:

- **K1 Skema**: Sumber kebenaran adalah file SQL 01 sampai 05 dan 09, bukan migrasi Laravel. Tabel `pengguna` dibuat oleh `02_tabel_baru.sql`. JANGAN membuat migrasi Laravel untuk tabel `pengguna`.
- **K2 Model**: `App\Models\Pengguna` (tabel `pengguna`, primary key `id_pengguna`, kolom `password_hash`). `User.php` tidak diubah.
- **K3 Login**: username dan password, `Auth::attempt`, hanya status AKTIF.
- **K4 Baca daftar barang**: Eloquent, paginasi 7 per halaman.
- **K5 Tulis data**: Stored procedure. Tambah: `sp_create_item`. Ubah data: `sp_update_item_v2`. Ubah harga: `sp_update_harga`. Hapus: `sp_delete_item_v2`.
- **K6 Akses**: Kelola Data Barang, Laporan Penjualan, & Pembelian Barang HANYA untuk ADMIN. KASIR mendapat 403.
- **K7 Jejak audit**: Setiap tambah, ubah, harga, dan hapus tercatat di `log_aktivitas` (pelaku, waktu, data sebelum dan sesudah, alasan opsional).
- **K8 Kode barang**: Bebas (angka atau huruf), tidak kosong, maks 20 karakter, tidak boleh kembar. Pesan kembar: "Kode barang sudah dipakai."
- **K9 Stok awal**: 0 sampai 1000. Tipe Jasa dikecualikan (database memberi 999999). Form ubah data TIDAK punya kolom stok.
- **K10 Harga**: Form punya Harga Pokok dan Harga Jual, diketik manual. Jika Harga Jual < Harga Pokok, tampilkan peringatan teks. Data tetap boleh disimpan.
- **K11 Batas Minimum**: Ada di form tambah (default 5). Status stok: Habis (stok=0), Menipis (stok<=batas_minimum), Aman (selainnya).
- **K12 Tampilan**: Tanpa ikon, tanpa emoji. Font minimal 16px, tombol minimal 44px (touch-target 2.75rem).
- **K13 Pesan error hapus**: "Barang tidak bisa dihapus karena sudah pernah dijual." (diterjemahkan di controller dari pesan database).
- **K14 Hapus**: Hanya untuk barang salah input. Barang bertransaksi tidak bisa dihapus.
- **K15 Jejak audit terbaca manusia**: Jenis aksi, kode dan nama barang, pelaku, waktu.
- **K16 Form**: Urutan dan label sesuai Figma. Jenis dan Satuan pakai datalist karena VARCHAR bebas.
- **K17 Laporan Penjualan (Figma P11)**: Paginasi 5 item, 3 kartu metrik ringkasan, riwayat transaksi dengan indikator subtotal negatif merah, filter rentang waktu (hari ini, 7 hari, bulan ini, kustom), cetak PDF window.print().
- **K18 Standardisasi Skala 80%**: Partial global `resources/views/partials/skala.blade.php` agar tampilan pada zoom browser 100% presisi dengan Figma 100% (lebar sidebar ~260px).
- **K19 Pembelian Barang (Restock - Figma P21)**: Dua tab interaktif via Vanilla JavaScript ("Pembelian Baru" dan "Riwayat Pembelian"), kartu restock dengan 3 item contoh status Habis/Menipis berlatar kuning muda dan teks merah, tombol Beli hijau (modal belum ada di Figma), tabel riwayat pembelian (desain belum ada di Figma).

## Catatan Database & Stored Procedure

- **Skema aktif**: `toko_firo` (MariaDB via FlyEnv).
- **Urutan eksekusi SQL**:
  1. `00_preflight.sql` — cek awal
  2. `01_alter_tabel_inti.sql` — tambah kolom ke tabel inti
  3. `02_tabel_baru.sql` — buat tabel pengguna, otp_2fa, dan lainnya
  4. `03_view.sql` — buat view
  5. `04_procedure.sql` — buat stored procedure
  6. `05_trigger.sql` — buat trigger
  7. `06_hak_akses.sql` — butuh akun root, jalankan terpisah
  8. `07_seed_dev.sql` — data awal dev
  9. `09_procedure_ubah_hapus.sql` — `sp_update_item_v2` dan `sp_delete_item_v2`
- **TODO Database & Rilis**:
  - Penambahan modal dialog pembelian barang baru jika desain detail Figma sudah tersedia.
  - Sambungkan method controller laporan dan pembelian ke stored procedure database riil pada tahap integrasi backend.
  - Pulihkan middleware `auth` dan `role:ADMIN` pada seluruh rute mandiri sebelum merge ke `staging`.
