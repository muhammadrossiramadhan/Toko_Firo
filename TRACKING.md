# Log Pengerjaan Toko Firo

## Tabel Status

| No | Kegiatan | Status | Catatan |
|---|---|---|---|
| 1 | Review 6 diagram (use case, activity, 4 sequence) | Selesai | Belum disimpan ke proyek |
| 2 | Diagram Mermaid Kelola Data Barang (use case admin, activity, sequence) | Selesai | Di chat |
| 3 | Inventaris Laravel 12 dan struktur tabel | Selesai | users ada, pengguna hanya di SQL |
| 4 | Dokumen rencana koreksi_copilot.md | Selesai | Keputusan K1 sampai K16 (lihat bagian Keputusan Pemilik) |
| 5 | Setup FlyEnv: PHP 8.4.26, MariaDB, PATH | Selesai | php -m lengkap |
| 6 | composer install | Selesai | composer.phar sementara sudah dihapus |
| 7 | .env dibuat dari .env.example, key:generate, SESSION_DRIVER diganti file | Selesai | Dilakukan tanpa izin, perlu diperiksa |
| 8 | DemoBarangController dan barang.blade.php (47 data dummy) | Selesai | Belum dicek visual |
| 9 | Route GET /demo/barang | Selesai | Ditandai DEMO di komentar |
| 10 | Verifikasi HTTP 200 halaman 1 dan 7, tidak ada SVG, 5 test lulus | Selesai | tests/Feature/DemoBarangTest.php dibuat |
| 11 | Label tombol Ubah Data dan Hapus Data | Selesai | Diperbaiki di barang.blade.php baris 216 dan 223 |
| 12 | Perbandingan visual dengan prototype & zoom 100% | Selesai | Layout modal flexbox sticky max-h-[90vh], font >= 16px, touch-target >= 44px, tanpa SVG |
| 13 | Model Pengguna, ItemBarang, isolasi rute, CRUD SP | Selesai | Milestone 2 (L1–L16 di koreksi_copilot.md tuntas 100%) |
| 14 | database/sql/09_procedure_ubah_hapus.sql | Selesai | sp_update_item_v2, sp_delete_item_v2 |
| 15 | Handoff dan dokumentasi branch staging | Selesai | HANDOFF_CRUD_TOKO_FIRO.md dibuat |
| 16 | Backend Fondasi Stock-Data Kasir (StokKasirService, StokKasirController, cariAjax, test suite) | Selesai | Branch Refina-Toti/Stock-Data_Kasir, 10 test lulus |

## Daftar File

Hasil pemeriksaan `git status --short` (per commit 0945f61):
- `M .gitignore`: Pembaruan aturan ignore untuk keamanan Git.
- `M README.md`: Dokumentasi utama proyek Toko Firo.
- `M routes/web.php`: Penambahan route demo `GET /demo/barang`.
- `?? .github/`: Template Pull Request `pull_request_template.md`.
- `?? TRACKING.md`: Log pengerjaan dan status tugas.
- `?? app/Http/Controllers/DemoBarangController.php`: Controller demo kelola data barang dengan 47 item dummy statis.
- `?? resources/views/demo/`: View Blade `barang.blade.php` untuk demo antarmuka.
- `?? koreksi_copilot.md`: Dokumen keputusan teknis K1–K16 dan langkah kerja L0–L16.

File tambahan (belum di-commit, tidak akan di-commit):
- `tests/Feature/DemoBarangTest.php`: 5 feature test untuk /demo/barang.
- `database/sql/09_procedure_ubah_hapus.sql`: Prosedur v2 dan ALTER alasan.
- `PROJECT_STATUS.md`: Laporan status internal (tidak untuk commit).


## Keputusan Pemilik (Final)

Keputusan pemilik merujuk pada `koreksi_copilot.md`, K1 sampai K16:

- **K1 Skema**: Sumber kebenaran adalah file SQL 01 sampai 05 dan 09, bukan migrasi Laravel. Tabel `pengguna` dibuat oleh `02_tabel_baru.sql`. JANGAN membuat migrasi Laravel untuk tabel `pengguna`.
- **K2 Model**: `App\Models\Pengguna` (tabel `pengguna`, primary key `id_pengguna`, kolom `password_hash`). `User.php` tidak diubah.
- **K3 Login**: username dan password, `Auth::attempt`, hanya status AKTIF.
- **K4 Baca daftar barang**: Eloquent, paginasi 7 per halaman.
- **K5 Tulis data**: Stored procedure. Tambah: `sp_create_item`. Ubah data: `sp_update_item_v2`. Ubah harga: `sp_update_harga`. Hapus: `sp_delete_item_v2`.
- **K6 Akses**: Kelola Data Barang HANYA untuk ADMIN. KASIR mendapat 403.
- **K7 Jejak audit**: Setiap tambah, ubah, harga, dan hapus tercatat di `log_aktivitas` (pelaku, waktu, data sebelum dan sesudah, alasan opsional).
- **K8 Kode barang**: Bebas (angka atau huruf), tidak kosong, maks 20 karakter, tidak boleh kembar. Pesan kembar: "Kode barang sudah dipakai."
- **K9 Stok awal**: 0 sampai 1000. Tipe Jasa dikecualikan (database memberi 999999). Form ubah data TIDAK punya kolom stok.
- **K10 Harga**: Form punya Harga Pokok dan Harga Jual, diketik manual. Jika Harga Jual < Harga Pokok, tampilkan peringatan teks. Data tetap boleh disimpan.
- **K11 Batas Minimum**: Ada di form tambah (default 5). Status stok: Habis (stok=0), Menipis (stok<=batas_minimum), Aman (selainnya).
- **K12 Tampilan**: Tanpa ikon, tanpa emoji. Font minimal 16px, tombol minimal 44px.
- **K13 Pesan error hapus**: "Barang tidak bisa dihapus karena sudah pernah dijual." (diterjemahkan di controller dari pesan database).
- **K14 Hapus**: Hanya untuk barang salah input. Barang bertransaksi tidak bisa dihapus.
- **K15 Jejak audit terbaca manusia**: Jenis aksi, kode dan nama barang, pelaku, waktu.
- **K16 Form**: Urutan dan label sesuai Figma. Jenis dan Satuan pakai datalist karena VARCHAR bebas.


## Catatan Database

- **Skema aktif**: `Toko_Firo` (MariaDB via FlyEnv).
- **Koneksi saat ini**: `.env` mengarah ke SQLite untuk testing (`DB_DATABASE=:memory:`). MariaDB dipakai untuk production/dev manual.
- **Urutan eksekusi SQL** (dikerjakan pemilik, bukan Antigravity):
  1. `00_preflight.sql` — cek awal
  2. `01_alter_tabel_inti.sql` — tambah kolom ke tabel inti
  3. `02_tabel_baru.sql` — buat tabel pengguna, otp_2fa, dan lainnya
  4. `03_view.sql` — buat view
  5. `04_procedure.sql` — buat stored procedure
  6. `05_trigger.sql` — buat trigger
  7. `06_hak_akses.sql` — butuh akun root, jalankan terpisah
  8. `07_seed_dev.sql` — data awal dev (jangan di produksi)
  9. `09_procedure_ubah_hapus.sql` — sp_update_item_v2, sp_delete_item_v2
- **Nilai `tipe_aksi` yang dipakai** di `log_aktivitas` (kolom VARCHAR(50)):
  - `ITEM_BARU` — sp_create_item
  - `ITEM_DIUBAH` — sp_update_item, sp_update_item_v2
  - `HARGA_DIUBAH` — sp_update_harga
  - `ITEM_DIHAPUS` — sp_delete_item, sp_delete_item_v2
- **Catatan Fitur**: Fitur alasan dibatalkan atas keputusan pemilik.
- **TODO database**: `v_laporan_penjualan` dirujuk di `sp_get_laporan_penjualan` tetapi belum didefinisikan di file SQL mana pun.
