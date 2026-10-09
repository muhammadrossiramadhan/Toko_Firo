-- ============================================================================
-- Sistem Database Toko_Firo (Toko Alat Tulis & Fotokopi)
-- File 2: 01_alter_tabel_inti.sql
-- Deskripsi: Penambahan kolom dan indeks pada tabel-tabel inti secara idempoten
--            (ADD COLUMN / ADD INDEX dengan pemeriksaan information_schema).
-- Charset: utf8mb4 | Collation: utf8mb4_0900_ai_ci
-- ============================================================================

USE Toko_Firo;

-- ----------------------------------------------------------------------------
-- Prosedur Pembantu Idempoten: sp_tambah_kolom_jika_belum_ada
-- Memeriksa keberadaan kolom melalui information_schema.COLUMNS sebelum ALTER
-- ----------------------------------------------------------------------------
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_tambah_kolom_jika_belum_ada $$
CREATE PROCEDURE sp_tambah_kolom_jika_belum_ada(
    IN p_nama_tabel VARCHAR(64),
    IN p_nama_kolom VARCHAR(64),
    IN p_definisi_kolom TEXT
)
BEGIN
    DECLARE v_jumlah_kolom INT DEFAULT 0;

    SELECT COUNT(*) INTO v_jumlah_kolom
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'Toko_Firo'
      AND TABLE_NAME = p_nama_tabel
      AND COLUMN_NAME = p_nama_kolom;

    IF v_jumlah_kolom = 0 THEN
        SET @sql_tambah_kolom = CONCAT('ALTER TABLE ', p_nama_tabel, ' ADD COLUMN ', p_nama_kolom, ' ', p_definisi_kolom);
        PREPARE stmt_kolom FROM @sql_tambah_kolom;
        EXECUTE stmt_kolom;
        DEALLOCATE PREPARE stmt_kolom;
        SELECT CONCAT('Kolom ', p_nama_kolom, ' berhasil ditambahkan pada tabel ', p_nama_tabel) AS status_operasi;
    ELSE
        SELECT CONCAT('Kolom ', p_nama_kolom, ' sudah ada pada tabel ', p_nama_tabel) AS status_operasi;
    END IF;
END $$

-- ----------------------------------------------------------------------------
-- Prosedur Pembantu Idempoten: sp_tambah_indeks_jika_belum_ada
-- Memeriksa keberadaan indeks melalui information_schema.STATISTICS sebelum ALTER
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_tambah_indeks_jika_belum_ada $$
CREATE PROCEDURE sp_tambah_indeks_jika_belum_ada(
    IN p_nama_tabel VARCHAR(64),
    IN p_nama_indeks VARCHAR(64),
    IN p_kolom_target VARCHAR(128)
)
BEGIN
    DECLARE v_jumlah_indeks INT DEFAULT 0;

    SELECT COUNT(*) INTO v_jumlah_indeks
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = 'Toko_Firo'
      AND TABLE_NAME = p_nama_tabel
      AND INDEX_NAME = p_nama_indeks;

    IF v_jumlah_indeks = 0 THEN
        SET @sql_tambah_indeks = CONCAT('ALTER TABLE ', p_nama_tabel, ' ADD INDEX ', p_nama_indeks, ' (', p_kolom_target, ')');
        PREPARE stmt_indeks FROM @sql_tambah_indeks;
        EXECUTE stmt_indeks;
        DEALLOCATE PREPARE stmt_indeks;
        SELECT CONCAT('Indeks ', p_nama_indeks, ' berhasil ditambahkan pada tabel ', p_nama_tabel) AS status_operasi;
    ELSE
        SELECT CONCAT('Indeks ', p_nama_indeks, ' sudah ada pada tabel ', p_nama_tabel) AS status_operasi;
    END IF;
END $$

DELIMITER ;

-- ----------------------------------------------------------------------------
-- 1. Tabel: daftar_item
--    - batas_minimum: ambang batas stok menipis (default 5, >= 0)
--    - dibuat_pada: waktu pembuatan baris data
--    - diubah_pada: waktu modifikasi terakhir baris data
-- ----------------------------------------------------------------------------
CALL sp_tambah_kolom_jika_belum_ada('daftar_item', 'batas_minimum', 'INT NOT NULL DEFAULT 5 CHECK (batas_minimum >= 0)');
CALL sp_tambah_kolom_jika_belum_ada('daftar_item', 'dibuat_pada', 'DATETIME DEFAULT CURRENT_TIMESTAMP');
CALL sp_tambah_kolom_jika_belum_ada('daftar_item', 'diubah_pada', 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');

-- ----------------------------------------------------------------------------
-- 2. Tabel: daftar_penjualan
--    - id_kasir: relasi ke pengguna (FK ditambahkan di 02_tabel_baru.sql)
--    - metode_bayar: pilihan metode pembayaran kasir
-- ----------------------------------------------------------------------------
CALL sp_tambah_kolom_jika_belum_ada('daftar_penjualan', 'id_kasir', 'INT NULL');
CALL sp_tambah_kolom_jika_belum_ada('daftar_penjualan', 'metode_bayar', "ENUM('TUNAI','QRIS') NULL");

-- ----------------------------------------------------------------------------
-- 3. Tabel: daftar_pembelian
--    - id_pengguna: pencatat pembelian stok (FK ditambahkan di 02_tabel_baru.sql)
-- ----------------------------------------------------------------------------
CALL sp_tambah_kolom_jika_belum_ada('daftar_pembelian', 'id_pengguna', 'INT NULL');

-- ----------------------------------------------------------------------------
-- 4. Tabel: log_aktivitas
--    - id_pengguna: pelaku aktivitas (FK ditambahkan di 02_tabel_baru.sql)
--    - data_sebelum: snapshot data sebelum perubahan
--    - data_sesudah: snapshot data setelah perubahan
--    - Indeks pada kolom: waktu, tipe_aksi
-- ----------------------------------------------------------------------------
CALL sp_tambah_kolom_jika_belum_ada('log_aktivitas', 'id_pengguna', 'INT NULL');
CALL sp_tambah_kolom_jika_belum_ada('log_aktivitas', 'data_sebelum', 'TEXT NULL');
CALL sp_tambah_kolom_jika_belum_ada('log_aktivitas', 'data_sesudah', 'TEXT NULL');

CALL sp_tambah_indeks_jika_belum_ada('log_aktivitas', 'idx_log_aktivitas_waktu', 'waktu');
CALL sp_tambah_indeks_jika_belum_ada('log_aktivitas', 'idx_log_aktivitas_tipe_aksi', 'tipe_aksi');

-- ----------------------------------------------------------------------------
-- Bersihkan Prosedur Pembantu Sementara
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_tambah_kolom_jika_belum_ada;
DROP PROCEDURE IF EXISTS sp_tambah_indeks_jika_belum_ada;
