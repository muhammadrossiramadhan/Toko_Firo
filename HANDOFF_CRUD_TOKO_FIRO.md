# HANDOFF RESMI: MODUL KELOLA DATA BARANG (TOKO FIRO)
Tanggal Pembaruan: 18 Oktober 2026
Penanggung Jawab: Muhammad Rossi Ramadhan (Ketua / Database & Modul Barang)
Branch Aktif: Rossi/Manage-Product-Data-Items_Admins
Target Basis PR: staging

## 1. PANDUAN UNTUK AI / DEVELOPER KOLABORATOR
- JANGAN mengubah atau merusak logika MVC Modul Barang yang sudah selesai (Model ItemBarang, BarangController, View admin/barang, modals.blade.php, routes/admin_barang.php).
- Modul ini sudah 100% terisolasi agar TIDAK BENTROK dengan modul autentikasi & 2FA milik rekan tim (Adit).
- Tampilan antarmuka patuh aturan K12: bebas tag <svg>, bebas simbol/ikon emoji, font minimal 16px, tinggi tombol minimal 44px, dan proporsional pada browser zoom 100% (menggunakan container max-h-[90vh], header/footer sticky flex-shrink-0, dan body scrollable).

## 2. ARSITEKTUR TEKNIS & DATABASE
- Lingkungan: Laravel 12, PHP 8.4 (FlyEnv), MariaDB 13.1.1, Windows PowerShell, Blade + Tailwind CSS.
- Model Baca: `app/Models/ItemBarang.php` mengarah ke tabel `daftar_item` (PK `kode_item` non-incrementing string, timestamps `dibuat_pada` dan `diubah_pada`, accessor `status_stok`: Habis jika stok 0, Menipis jika stok <= batas_minimum, Aman jika selainnya).
- Operasi Tulis (Stored Procedure MariaDB):
  * Tambah: `sp_create_item` (13 parameter). Tipe JASA otomatis stok 999999.
  * Ubah Data: `sp_update_item_v2` (10 parameter, tanpa kolom stok & harga).
  * Ubah Harga: `sp_update_harga` (4 parameter, mendukung peringatan jika harga jual < harga pokok).
  * Hapus: `sp_delete_item_v2` (dilarang hapus jika ada relasi transaksi, menghasilkan pesan K13: "Barang tidak bisa dihapus karena sudah pernah dijual.").
- Audit Trail: Seluruh aksi tulis otomatis dicatat oleh trigger/stored procedure ke tabel `log_aktivitas` (ITEM_BARU, ITEM_DIUBAH, HARGA_DIUBAH, ITEM_DIHAPUS).
- Fallback ID Pengguna: Pada `BarangController.php`, pembacaan ID pengguna menggunakan fallback `Auth::id() ?? 1` agar stored procedure tetap mencatat jejak audit saat auth decoupled.

## 3. STRUKTUR FILE MODUL BARANG
- Route: `routes/admin_barang.php` (dimuat via `bootstrap/app.php` dalam grup `then:`).
- Controller: `app/Http/Controllers/Admin/BarangController.php` (menangani index dengan search q, filter jenis, urutan, paginasi 7, serta method store, update, updateHarga, destroy).
- View: `resources/views/admin/barang/index.blade.php` dan `resources/views/admin/barang/modals.blade.php`.
- Test: `tests/Feature/BarangCrudTest.php` dan `tests/Feature/BarangDaftarTest.php`.

## 4. STATUS PENGERJAAN
- L0 s.d. L5 (Auth & Setup Skema): Selesai.
- L6 (Route & Kerangka Prototype): Selesai.
- L7 (Eloquent Daftar & Paginasi 7): Selesai.
- L8 (Pencarian, Filter Jenis, Urutan): Selesai.
- L9 (Status Stok Habis/Menipis/Aman): Selesai.
- L10 (Pilih Baris & Aktivasi Tombol Aksi): Selesai.
- L11 s.d. L14 (CRUD Stored Procedure Tambah, Ubah, Ubah Harga, Hapus): Selesai.
- L15 (Test Suite CRUD & Audit Trail): Selesai (31 assertions lulus di test suite utama).
- L16 (QA & Kepatuhan Figma/Zoom 100%): Selesai.

## 5. REKOMENDASI SAAT MERGE KE STAGING
- File milik modul barang tidak menyentuh `routes/web.php` milik Adit sehingga minim konflik.
- Jika ada konflik pada `config/auth.php` saat merge ke `staging`, pertahankan konfigurasi provider autentikasi tim bersama.
