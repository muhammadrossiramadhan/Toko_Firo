-- ============================================================================
-- Sistem Database Toko_Firo (Toko Alat Tulis & Fotokopi)
-- File 1: 00_preflight.sql
-- Deskripsi: Skrip pemeriksaan pra-penerapan (preflight checks), verifikasi
--            lingkungan MySQL, tabel inti, view, prosedur, trigger, dan zona waktu.
-- Charset: utf8mb4 | Collation: utf8mb4_0900_ai_ci
-- ============================================================================

-- ----------------------------------------------------------------------------
-- PERINTAH CADANGAN (BACKUP) SEBELUM MIGRASI DILAKUKAN:
-- Jalankan perintah berikut pada terminal shell/bash sebelum mengeksekusi migrasi:
--
-- mysqldump -u <nama_pengguna> -p \
--   --host=127.0.0.1 \
--   --port=3306 \
--   --default-character-set=utf8mb4 \
--   --single-transaction \
--   --quick \
--   --routines \
--   --triggers \
--   Toko_Firo > /home/archbox/tugas/Toko_Firo/database/backup_Toko_Firo_$(date +\%Y\%m\%d_\%H\%M\%S).sql
-- ----------------------------------------------------------------------------

-- Pastikan database Toko_Firo tersedia dan dipilih
CREATE DATABASE IF NOT EXISTS Toko_Firo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

USE Toko_Firo;

-- ----------------------------------------------------------------------------
-- 1. Cetak Versi MySQL dan Informasi Server
-- ----------------------------------------------------------------------------
SELECT
    VERSION() AS versi_mysql,
    @@version_comment AS komentar_versi,
    DATABASE() AS database_aktif,
    CURRENT_USER() AS pengguna_koneksi,
    NOW() AS waktu_server;

-- ----------------------------------------------------------------------------
-- 2. Pemeriksaan Keberadaan 5 Tabel Utama
--    (daftar_item, daftar_pembelian, daftar_penjualan, daftar_transaksi, log_aktivitas)
-- ----------------------------------------------------------------------------
SELECT
    daftar_tabel.nama_tabel,
    CASE
        WHEN ist.TABLE_NAME IS NOT NULL THEN 'ADA'
        ELSE 'TIDAK ADA'
    END AS status_tabel,
    COALESCE(ist.ENGINE, '-') AS mesin_penyimpanan,
    COALESCE(ist.TABLE_ROWS, 0) AS estimasi_jumlah_baris,
    COALESCE(ist.TABLE_COLLATION, '-') AS kolasi_tabel
FROM (
    SELECT 'daftar_item' AS nama_tabel
    UNION ALL SELECT 'daftar_pembelian'
    UNION ALL SELECT 'daftar_penjualan'
    UNION ALL SELECT 'daftar_transaksi'
    UNION ALL SELECT 'log_aktivitas'
) AS daftar_tabel
LEFT JOIN information_schema.TABLES ist
    ON ist.TABLE_SCHEMA = 'Toko_Firo'
    AND ist.TABLE_NAME = daftar_tabel.nama_tabel
ORDER BY daftar_tabel.nama_tabel;

-- ----------------------------------------------------------------------------
-- 3. Pemeriksaan Keberadaan View yang Ada
--    (v_laporan_pembelian, v_laporan_penjualan, v_riwayat_item, v_stok_item, v_stok_menipis)
-- ----------------------------------------------------------------------------
SELECT
    daftar_view.nama_view,
    CASE
        WHEN isv.TABLE_NAME IS NOT NULL THEN 'ADA'
        ELSE 'TIDAK ADA'
    END AS status_view,
    COALESCE(isv.CHECK_OPTION, '-') AS opsi_pemeriksaan,
    COALESCE(isv.IS_UPDATABLE, '-') AS dapat_diperbarui
FROM (
    SELECT 'v_laporan_pembelian' AS nama_view
    UNION ALL SELECT 'v_laporan_penjualan'
    UNION ALL SELECT 'v_riwayat_item'
    UNION ALL SELECT 'v_stok_item'
    UNION ALL SELECT 'v_stok_menipis'
) AS daftar_view
LEFT JOIN information_schema.VIEWS isv
    ON isv.TABLE_SCHEMA = 'Toko_Firo'
    AND isv.TABLE_NAME = daftar_view.nama_view
ORDER BY daftar_view.nama_view;

-- ----------------------------------------------------------------------------
-- 4. Pemeriksaan Stored Procedure yang Ada (Seluruh sp_*)
-- ----------------------------------------------------------------------------
SELECT
    ROUTINE_NAME AS nama_prosedur,
    ROUTINE_TYPE AS tipe_rutin,
    DEFINER AS pembuat,
    CREATED AS dibuat_pada,
    LAST_ALTERED AS terakhir_diubah,
    DATA_TYPE AS tipe_kembalian
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = 'Toko_Firo'
  AND ROUTINE_TYPE = 'PROCEDURE'
  AND ROUTINE_NAME LIKE 'sp_%'
ORDER BY ROUTINE_NAME;

-- ----------------------------------------------------------------------------
-- 5. Pemeriksaan Trigger yang Ada pada Tabel daftar_transaksi
-- ----------------------------------------------------------------------------
SELECT
    TRIGGER_NAME AS nama_trigger,
    EVENT_OBJECT_TABLE AS tabel_target,
    EVENT_MANIPULATION AS aksi_event,
    ACTION_TIMING AS waktu_eksekusi,
    ACTION_STATEMENT AS pernyataan_aksi,
    CREATED AS dibuat_pada
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'Toko_Firo'
  AND EVENT_OBJECT_TABLE = 'daftar_transaksi'
ORDER BY TRIGGER_NAME;

-- ----------------------------------------------------------------------------
-- 6. Rekomendasi Pengaturan Zona Waktu (WIB / UTC+07:00)
-- ----------------------------------------------------------------------------
-- Rekomendasi konfigurasi server MySQL untuk zona waktu WIB:
-- SET GLOBAL time_zone = '+07:00';
-- SET time_zone = '+07:00';

SELECT
    @@global.time_zone AS zona_waktu_global,
    @@session.time_zone AS zona_waktu_sesi,
    NOW() AS waktu_lokal_sekarang,
    UTC_TIMESTAMP() AS waktu_utc_sekarang,
    TIMEDIFF(NOW(), UTC_TIMESTAMP()) AS selisih_waktu_saat_ini,
    CASE
        WHEN TIMEDIFF(NOW(), UTC_TIMESTAMP()) = '07:00:00' THEN 'SESUAI (WIB / UTC+07:00)'
        ELSE 'PERINGATAN: Zona waktu belum diatur ke WIB (+07:00). Jalankan: SET GLOBAL time_zone = ''+07:00'';'
    END AS status_evaluasi_wib;
