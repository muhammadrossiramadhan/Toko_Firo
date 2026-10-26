# Changelog - Toko Firo

Seluruh perubahan penting pada proyek Toko Firo dicatat dalam berkas ini.

Format pencatatan mengacu pada prinsip [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased] - Branch `Refina-Toti/Stock-Data_Kasir` (26 Oktober 2026)

### Added
- **Backend Fondasi Modul Stock-Data Kasir**:
  - `app/Services/Kasir/StokKasirService.php`:
    - `getDaftarBarang(array $filters = [], int $perPage = 15)`: Query data barang aktif untuk kasir (`id`, `kode_barang`, `nama_barang`, `barcode`, `harga_jual`, `stok`, `satuan`, `batas_minimum`), filter pencarian (`q` / `keyword`), dan kalkulasi status stok komputasi ('aman', 'menipis', 'habis').
    - `cekKetersediaan(int|string $barangId, int $jumlahDiminta)`: Memeriksa ketersediaan stok dengan return array `['tersedia' => bool, 'sisa_stok' => int, 'barang' => ?Barang]`.
  - `app/Http/Controllers/Kasir/StokKasirController.php`:
    - Menginjeksi `StokKasirService`.
    - `index(Request $request)`: Menampilkan data paginasi stok kasir (kirim ke view `kasir.stok.index` atau respons JSON).
    - `cariAjax(Request $request)`: Endpoint cepat scanner barcode / pencarian modal transaksi kasir (JSON status 200).
  - `tests/Feature/Kasir/StokKasirTest.php`:
    - Uji otentikasi kasir yang belum login dialihkan ke `/login`.
    - Uji pencarian barang berdasarkan nama dan barcode.
    - Uji akurasi komputasi status stok ('aman', 'menipis', 'habis').
    - Uji method `cekKetersediaan` mengembalikan respon boolean yang valid.

### Changed
- **Pendaftaran Rute Kasir (`routes/web.php`)**:
  - Pendaftaran grup rute middleware kasir:
    - `GET /kasir/stok` -> `StokKasirController@index` (nama: `kasir.stok.index`)
    - `GET /kasir/stok/cari` -> `StokKasirController@cariAjax` (nama: `kasir.stok.cari`)

