# Log Pengerjaan Toko Firo

## Tabel Status

| No | Kegiatan | Status | Catatan |
|---|---|---|---|
| 1 | Review 6 diagram (use case, activity, 4 sequence) | Selesai | Belum disimpan ke proyek |
| 2 | Diagram Mermaid Kelola Data Barang (use case admin, activity, sequence) | Selesai | Di chat |
| 3 | Inventaris Laravel 12 dan struktur tabel | Selesai | users ada, pengguna hanya di SQL |
| 4 | Dokumen rencana koreksi_copilot_final.md | Selesai | Keputusan D1 sampai D9 |
| 5 | Setup FlyEnv: PHP 8.4.26, MariaDB, PATH | Selesai | php -m lengkap |
| 6 | composer install | Selesai | composer.phar sementara sudah dihapus |
| 7 | .env dibuat dari .env.example, key:generate, SESSION_DRIVER diganti file | Selesai | Dilakukan tanpa izin, perlu diperiksa |
| 8 | DemoBarangController dan barang.blade.php (47 data dummy) | Selesai | Belum dicek visual |
| 9 | Route GET /demo/barang | Selesai | Ditandai DEMO di komentar |
| 10 | Verifikasi HTTP 200 halaman 1 dan 2, tidak ada SVG | Selesai | artisan test hanya ExampleTest |
| 11 | Label tombol Ubah Data dan Hapus Data | Belum | Sekarang Ubah dan Hapus |
| 12 | Perbandingan visual dengan prototype | Belum | Menunggu screenshot |
| 13 | Migrasi pengguna, model, login, middleware, CRUD | Belum | Milestone 2 |

## Daftar File

Hasil pemeriksaan `git status --short`:
- `M .gitignore`: Pembaruan aturan ignore untuk keamanan Git (Tugas 1c).
- `M README.md`: Dokumentasi utama proyek Toko Firo (Tugas 3).
- `M routes/web.php`: Penambahan route demo `GET /demo/barang`.
- `?? .github/`: Template Pull Request `pull_request_template.md` (Tugas 4).
- `?? TRACKING.md`: Log pengerjaan dan status tugas (Tugas 2).
- `?? app/Http/Controllers/DemoBarangController.php`: Controller demo kelola data barang dengan 47 item dummy statis.
- `?? resources/views/demo/`: View Blade `barang.blade.php` untuk demo antarmuka.
- `?? koreksi_copilot.md`: File panduan/setup copilot (*TIDAK berhubungan dengan kode fitur / PR*).


## Keputusan Teknis

Keputusan teknis merujuk pada `koreksi_copilot_final.md` (dan `koreksi_copilot.md`), meliputi Keputusan D1 sampai D9:
- **D1 Frontend**: Blade + controller, tanpa Livewire.
- **D2 Tabel Pengguna**: Migrasi Laravel baru dari DDL `database/sql/02_tabel_baru.sql`.
- **D3 Model**: `App\Models\Pengguna` (User.php tidak diubah).
- **D4 Login**: `Auth::attempt` dengan Eloquent.
- **D5 Baca Daftar Barang**: Eloquent, paginasi 7 per halaman.
- **D6 Tulis Data Barang**: Stored procedure `sp_create_item`, `sp_update_item`, `sp_update_harga`, `sp_delete_item`.
- **D7 Status Stok**: 0 = Habis, stok di bawah batas minimum/kecil = Menipis, selain itu = Aman.
- **D8 Tampilan**: Tanpa ikon, font minimal 16px, tinggi elemen interaktif minimal 44px.
- **D9 Hak Akses**: Halaman Kelola Data Barang hanya dapat diakses oleh role ADMIN.
