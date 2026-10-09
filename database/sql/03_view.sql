-- =============================================================================
-- Database: Toko_Firo
-- Berkas: 03_view.sql
-- Deskripsi: Definisi view untuk sistem Toko Firo (ATK & Fotokopi)
-- Karakteristik: Idempoten (CREATE OR REPLACE VIEW), utf8mb4_0900_ai_ci
-- =============================================================================

USE `Toko_Firo`;

-- -----------------------------------------------------------------------------
-- 1. View: v_stok_item
-- Deskripsi: Menampilkan informasi stok barang lengkap dengan margin keuntungan,
--            status ketersediaan stok, dan atribut tambahan di bagian akhir.
-- Logika status_stok:
--   stok <= 0             -> 'HABIS'
--   stok <= batas_minimum -> 'MENIPIS'
--   stok <= 15            -> 'NORMAL'
--   lainnya               -> 'AMAN'
-- -----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `v_stok_item` AS
SELECT
    `kode_item`,
    `nama_item`,
    `stok`,
    `satuan`,
    `harga_pokok`,
    `harga_jual`,
    (`harga_jual` - `harga_pokok`) AS `margin_untung`,
    CASE
        WHEN `stok` <= 0 THEN 'HABIS'
        WHEN `stok` <= COALESCE(`batas_minimum`, 5) THEN 'MENIPIS'
        WHEN `stok` <= 15 THEN 'NORMAL'
        ELSE 'AMAN'
    END AS `status_stok`,
    `jenis`,
    `merek`,
    `rak`,
    `tipe_item`,
    `batas_minimum`
FROM `daftar_item`;

-- -----------------------------------------------------------------------------
-- 2. View: v_stok_menipis
-- Deskripsi: Menampilkan daftar item yang stoknya sudah habis atau menipis
--            sebagai peringatan untuk pengadaan / restock barang.
-- -----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `v_stok_menipis` AS
SELECT *
FROM `v_stok_item`
WHERE `status_stok` IN ('HABIS', 'MENIPIS');

-- -----------------------------------------------------------------------------
-- 3. View: v_katalog_publik (SQL SECURITY DEFINER)
-- Deskripsi: Katalog barang untuk konsumsi publik/pelanggan.
--            Menyembunyikan harga_pokok, margin_untung, rak, dan batas_minimum.
-- Logika JASA:
--   - stok_tampil: NULL untuk tipe_item 'JASA', selain itu menampilkan nilai stok
--   - status_stok: 'TERSEDIA' untuk tipe_item 'JASA', selain itu mengikuti aturan v_stok_item
-- -----------------------------------------------------------------------------
CREATE OR REPLACE SQL SECURITY DEFINER VIEW `v_katalog_publik` AS
SELECT
    `kode_item`,
    `nama_item`,
    `jenis`,
    `merek`,
    `satuan`,
    `harga_jual`,
    CASE
        WHEN `tipe_item` = 'JASA' THEN NULL
        ELSE `stok`
    END AS `stok_tampil`,
    CASE
        WHEN `tipe_item` = 'JASA' THEN 'TERSEDIA'
        WHEN `stok` <= 0 THEN 'HABIS'
        WHEN `stok` <= COALESCE(`batas_minimum`, 5) THEN 'MENIPIS'
        WHEN `stok` <= 15 THEN 'NORMAL'
        ELSE 'AMAN'
    END AS `status_stok`,
    `keterangan`
FROM `daftar_item`;

-- -----------------------------------------------------------------------------
-- 4. View: v_info_barang
-- Deskripsi: Menampilkan riwayat perubahan data dan harga barang dari log aktivitas.
-- -----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `v_info_barang` AS
SELECT
    `id_log`,
    `waktu`,
    `tipe_aksi`,
    `keterangan`,
    `data_sebelum`,
    `data_sesudah`,
    `id_pengguna`
FROM `log_aktivitas`
WHERE `tipe_aksi` IN ('ITEM_BARU', 'ITEM_DIUBAH', 'HARGA_DIUBAH');

-- -----------------------------------------------------------------------------
-- 5. View: v_barang_terlaris
-- Deskripsi: Menampilkan peringkat item berdasarkan total terjual bersih
--            (item keluar dikurangi retur penjualan) untuk transaksi penjualan.
-- -----------------------------------------------------------------------------
CREATE OR REPLACE VIEW `v_barang_terlaris` AS
SELECT
    di.`kode_item`,
    di.`nama_item`,
    SUM(COALESCE(dt.`item_keluar`, 0) - COALESCE(dt.`retur_penjualan`, 0)) AS `total_terjual`
FROM `daftar_transaksi` dt
JOIN `daftar_item` di ON dt.`kode_item` = di.`kode_item`
WHERE dt.`kode_penjualan` IS NOT NULL
GROUP BY di.`kode_item`, di.`nama_item`
ORDER BY `total_terjual` DESC;
