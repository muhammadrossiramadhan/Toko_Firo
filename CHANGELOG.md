# Changelog - Toko Firo

Seluruh perubahan penting pada proyek Toko Firo dicatat dalam berkas ini.

Format pencatatan mengacu pada prinsip [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased] - Branch `Refina-Toti/Sales-Transaction_Kasir` (26 Oktober 2026)

### Added
- **Backend Mesin Transaksi POS Kasir (Sales Transaction)**:
  - `app/Http/Requests/Kasir/SimpanTransaksiRequest.php`: Form Request dengan validasi ketat array items belanja, pengecekan eksistensi item di master barang, qty bilangan bulat positif, validasi uang bayar non-negatif, diskon numerik non-negatif, dan pesan kesalahan berbahasa Indonesia.
  - `app/Services/Kasir/TransaksiKasirService.php`:
    - `generateNomorNota()`: Generator nomor nota transaksi harian otomatis dan berurutan (`TRX-YYYYMMDD-XXXX`).
    - `prosesTransaksi(array $dataTransaksi, int $kasirId)`: Eksekusi transaksi atomik dalam `DB::transaction()` ber-kunci pesimistik (`lockForUpdate()`), validasi kecukupan stok riil, validasi kecukupan pembayaran, pengurangan stok otomatis di `daftar_item`, pencatatan header di `daftar_penjualan`, pencatatan detail di `daftar_transaksi`, pencatatan jejak audit di `log_aktivitas`, serta pemicuan hook peringatan stok menipis via `BatasStokService`.
    - `getDetailTransaksi(string $nomorNota)`: Pengambilan data transaksi beserta relasi detail barang dan informasi kasir.
  - `app/Http/Controllers/Kasir/TransaksiKasirController.php`:
    - Injeksi `TransaksiKasirService`.
    - `index()`: Mengarahkan ke antarmuka POS kasir (dengan fallback dataset prototype).
    - `simpan()`: Menyimpan transaksi, mengembalikan JSON 201 dengan `nomor_nota`, `kembalian`, dan `url_cetak`, serta menangani penolakan 422 untuk stok tidak mencukupi atau uang pembayaran kurang.
    - `detail()`: Menyajikan data detail transaksi via JSON/View.
    - `cetakStruk()`: Menampilkan resi/struk transaksi siap cetak yang patuh standar K12 (bebas SVG/emoji) dan K18.
  - `tests/Feature/Kasir/TransaksiKasirTest.php`:
    - Uji otentikasi kasir saat menyimpan transaksi.
    - Uji transaksi berhasil, pengurangan stok otomatis, penyimpanan header & detail transaksi, dan akurasi kembalian.
    - Uji penolakan transaksi jika kuantitas melebihi stok (rollback penuh).
    - Uji penolakan transaksi jika uang bayar kurang dari total belanja.
    - Uji generator nomor nota harian berurutan.

### Changed
- **Pendaftaran Rute Transaksi Kasir (`routes/web.php`)**:
  - Pendaftaran grup rute middleware kasir:
    - `GET /kasir/transaksi` -> `TransaksiKasirController@index` (nama: `kasir.transaksi.index`)
    - `POST /kasir/transaksi/simpan` -> `TransaksiKasirController@simpan` (nama: `kasir.transaksi.simpan`)
    - `GET /kasir/transaksi/{nota}/detail` -> `TransaksiKasirController@detail` (nama: `kasir.transaksi.detail`)
    - `GET /kasir/transaksi/{nota}/cetak` -> `TransaksiKasirController@cetakStruk` (nama: `kasir.transaksi.cetak`)

