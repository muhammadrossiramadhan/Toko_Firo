-- ============================================================================
-- Sistem Database Toko_Firo (Toko Alat Tulis & Fotokopi)
-- File 3: 02_tabel_baru.sql
-- Deskripsi: Pembuatan tabel-tabel baru sistem (IF NOT EXISTS) dan penambahan
--            Foreign Key constraints secara idempoten dari File 01.
-- Charset: utf8mb4 | Collation: utf8mb4_0900_ai_ci
-- ============================================================================

USE Toko_Firo;

-- ----------------------------------------------------------------------------
-- 1. Tabel: pengguna
--    Menyimpan akun pengguna aplikasi (Admin & Kasir)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'KASIR') NOT NULL,
    status ENUM('AKTIF', 'NONAKTIF') DEFAULT 'AKTIF',
    telegram_chat_id VARCHAR(50) NULL,
    login_terakhir DATETIME NULL,
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    diubah_pada DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pengguna_role_status (role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 2. Tabel: otp_2fa
--    Menyimpan token One-Time Password untuk autentikasi ganda
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS otp_2fa (
    id_otp INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NOT NULL,
    kode_hash VARCHAR(255) NOT NULL,
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    kadaluarsa_pada DATETIME NOT NULL,
    digunakan TINYINT DEFAULT 0,
    percobaan INT DEFAULT 0,
    INDEX idx_otp_2fa_id_pengguna (id_pengguna),
    CONSTRAINT fk_otp_2fa_id_pengguna FOREIGN KEY (id_pengguna)
        REFERENCES pengguna (id_pengguna) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 3. Tabel: notifikasi_dibaca
--    Pelacakan status keterbacaan notifikasi/log per pengguna (Composite PK)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifikasi_dibaca (
    id_log INT NOT NULL,
    id_pengguna INT NOT NULL,
    dibaca_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_log, id_pengguna),
    INDEX idx_notifikasi_dibaca_id_pengguna (id_pengguna)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 4. Tabel: saldo_harian
--    Pencatatan kas fisik awal harian toko
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS saldo_harian (
    tanggal DATE PRIMARY KEY,
    saldo_awal DECIMAL(15, 2) DEFAULT 0,
    diatur_oleh INT NULL,
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_saldo_harian_diatur_oleh (diatur_oleh),
    CONSTRAINT fk_saldo_harian_diatur_oleh FOREIGN KEY (diatur_oleh)
        REFERENCES pengguna (id_pengguna) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 5. Tabel: riwayat_backup
--    Log pencadangan database manual maupun otomatis terjadwal
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS riwayat_backup (
    id_backup INT AUTO_INCREMENT PRIMARY KEY,
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
    jenis ENUM('MANUAL', 'OTOMATIS') NOT NULL,
    ukuran_kb INT NULL,
    status ENUM('BERHASIL', 'GAGAL') NOT NULL,
    nama_file VARCHAR(255) NULL,
    pesan_error TEXT NULL,
    id_pengguna INT NULL,
    INDEX idx_riwayat_backup_id_pengguna (id_pengguna),
    INDEX idx_riwayat_backup_waktu (waktu),
    CONSTRAINT fk_riwayat_backup_id_pengguna FOREIGN KEY (id_pengguna)
        REFERENCES pengguna (id_pengguna) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 6. Tabel: pengaturan_notifikasi
--    Konfigurasi key-value untuk integrasi bot Telegram / WhatsApp
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pengaturan_notifikasi (
    kunci VARCHAR(50) PRIMARY KEY,
    nilai VARCHAR(255) NULL,
    diubah_pada DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- 7. Tabel: log_notifikasi
--    Riwayat pengiriman notifikasi eksternal
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS log_notifikasi (
    id_notif INT AUTO_INCREMENT PRIMARY KEY,
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
    jenis ENUM('LAPORAN', 'STOK_MINIMUM', 'ITEM_BARU', 'ITEM_DIUBAH', 'BACKUP', 'OTP', 'TES') NOT NULL,
    tujuan ENUM('TELEGRAM', 'WHATSAPP') DEFAULT 'TELEGRAM',
    penerima VARCHAR(100) NULL,
    isi TEXT NULL,
    status ENUM('MENUNGGU', 'TERKIRIM', 'GAGAL') DEFAULT 'MENUNGGU',
    pesan_error TEXT NULL,
    INDEX idx_log_notifikasi_waktu (waktu),
    INDEX idx_log_notifikasi_jenis_status (jenis, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================================
-- Penambahan Foreign Key Constraints (Idempoten)
-- Memeriksa information_schema.TABLE_CONSTRAINTS sebelum menjalankan ALTER TABLE
-- ============================================================================

DELIMITER $$

-- ----------------------------------------------------------------------------
-- Prosedur Pembantu: sp_tambah_foreign_key_jika_belum_ada
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_tambah_foreign_key_jika_belum_ada $$
CREATE PROCEDURE sp_tambah_foreign_key_jika_belum_ada(
    IN p_tabel_asal VARCHAR(64),
    IN p_nama_fk VARCHAR(64),
    IN p_kolom_asal VARCHAR(64),
    IN p_tabel_tujuan VARCHAR(64),
    IN p_kolom_tujuan VARCHAR(64),
    IN p_aksi_on_delete VARCHAR(32)
)
BEGIN
    DECLARE v_fk_ada INT DEFAULT 0;
    DECLARE v_tabel_asal_ada INT DEFAULT 0;
    DECLARE v_tabel_tujuan_ada INT DEFAULT 0;

    -- Periksa ketersediaan kedua tabel di skema Toko_Firo
    SELECT COUNT(*) INTO v_tabel_asal_ada
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = 'Toko_Firo' AND TABLE_NAME = p_tabel_asal;

    SELECT COUNT(*) INTO v_tabel_tujuan_ada
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = 'Toko_Firo' AND TABLE_NAME = p_tabel_tujuan;

    IF v_tabel_asal_ada > 0 AND v_tabel_tujuan_ada > 0 THEN
        -- Periksa apakah constraint foreign key dengan nama ini sudah ada
        SELECT COUNT(*) INTO v_fk_ada
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = 'Toko_Firo'
          AND TABLE_NAME = p_tabel_asal
          AND CONSTRAINT_NAME = p_nama_fk
          AND CONSTRAINT_TYPE = 'FOREIGN KEY';

        IF v_fk_ada = 0 THEN
            SET @sql_fk = CONCAT(
                'ALTER TABLE ', p_tabel_asal,
                ' ADD CONSTRAINT ', p_nama_fk,
                ' FOREIGN KEY (', p_kolom_asal, ')',
                ' REFERENCES ', p_tabel_tujuan, ' (', p_kolom_tujuan, ')',
                ' ON DELETE ', p_aksi_on_delete,
                ' ON UPDATE CASCADE'
            );
            PREPARE stmt_fk FROM @sql_fk;
            EXECUTE stmt_fk;
            DEALLOCATE PREPARE stmt_fk;
            SELECT CONCAT('Constraint ', p_nama_fk, ' berhasil ditambahkan pada tabel ', p_tabel_asal) AS status_operasi;
        ELSE
            SELECT CONCAT('Constraint ', p_nama_fk, ' sudah ada pada tabel ', p_tabel_asal) AS status_operasi;
        END IF;
    ELSE
        SELECT CONCAT('Tabel ', p_tabel_asal, ' atau ', p_tabel_tujuan, ' tidak ditemukan, pembuatan FK dilewati') AS status_operasi;
    END IF;
END $$

-- ----------------------------------------------------------------------------
-- Prosedur Pembantu: sp_selaraskan_dan_tambah_fk_notifikasi_dibaca
-- Menyelaraskan tipe data id_log pada notifikasi_dibaca agar identik dengan
-- log_aktivitas.id_log (baik INT maupun INT UNSIGNED) lalu menambahkan FK
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_selaraskan_dan_tambah_fk_notifikasi_dibaca $$
CREATE PROCEDURE sp_selaraskan_dan_tambah_fk_notifikasi_dibaca()
BEGIN
    DECLARE v_tipe_id_log VARCHAR(64) DEFAULT '';
    DECLARE v_fk_log_ada INT DEFAULT 0;
    DECLARE v_fk_pengguna_ada INT DEFAULT 0;
    DECLARE v_tabel_log_ada INT DEFAULT 0;

    SELECT COUNT(*) INTO v_tabel_log_ada
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = 'Toko_Firo' AND TABLE_NAME = 'log_aktivitas';

    IF v_tabel_log_ada > 0 THEN
        -- Ambil tipe data aktual dari log_aktivitas.id_log
        SELECT COLUMN_TYPE INTO v_tipe_id_log
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = 'Toko_Firo'
          AND TABLE_NAME = 'log_aktivitas'
          AND COLUMN_NAME = 'id_log';

        IF v_tipe_id_log != '' THEN
            SET @sql_mod = CONCAT('ALTER TABLE notifikasi_dibaca MODIFY COLUMN id_log ', v_tipe_id_log, ' NOT NULL');
            PREPARE stmt_mod FROM @sql_mod;
            EXECUTE stmt_mod;
            DEALLOCATE PREPARE stmt_mod;
        END IF;

        -- FK: notifikasi_dibaca.id_log -> log_aktivitas.id_log
        SELECT COUNT(*) INTO v_fk_log_ada
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = 'Toko_Firo'
          AND TABLE_NAME = 'notifikasi_dibaca'
          AND CONSTRAINT_NAME = 'fk_notifikasi_dibaca_id_log'
          AND CONSTRAINT_TYPE = 'FOREIGN KEY';

        IF v_fk_log_ada = 0 THEN
            ALTER TABLE notifikasi_dibaca
                ADD CONSTRAINT fk_notifikasi_dibaca_id_log
                FOREIGN KEY (id_log) REFERENCES log_aktivitas (id_log)
                ON DELETE CASCADE ON UPDATE CASCADE;
            SELECT 'Constraint fk_notifikasi_dibaca_id_log berhasil ditambahkan' AS status_operasi;
        ELSE
            SELECT 'Constraint fk_notifikasi_dibaca_id_log sudah ada' AS status_operasi;
        END IF;
    END IF;

    -- FK: notifikasi_dibaca.id_pengguna -> pengguna.id_pengguna
    SELECT COUNT(*) INTO v_fk_pengguna_ada
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'Toko_Firo'
      AND TABLE_NAME = 'notifikasi_dibaca'
      AND CONSTRAINT_NAME = 'fk_notifikasi_dibaca_id_pengguna'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY';

    IF v_fk_pengguna_ada = 0 THEN
        ALTER TABLE notifikasi_dibaca
            ADD CONSTRAINT fk_notifikasi_dibaca_id_pengguna
            FOREIGN KEY (id_pengguna) REFERENCES pengguna (id_pengguna)
            ON DELETE CASCADE ON UPDATE CASCADE;
        SELECT 'Constraint fk_notifikasi_dibaca_id_pengguna berhasil ditambahkan' AS status_operasi;
    ELSE
        SELECT 'Constraint fk_notifikasi_dibaca_id_pengguna sudah ada' AS status_operasi;
    END IF;
END $$

DELIMITER ;

-- ----------------------------------------------------------------------------
-- Eksekusi Penambahan FK untuk Kolom yang Ditambahkan pada File 01:
-- ----------------------------------------------------------------------------

-- FK untuk daftar_penjualan.id_kasir -> pengguna.id_pengguna
CALL sp_tambah_foreign_key_jika_belum_ada('daftar_penjualan', 'fk_daftar_penjualan_id_kasir', 'id_kasir', 'pengguna', 'id_pengguna', 'SET NULL');

-- FK untuk daftar_pembelian.id_pengguna -> pengguna.id_pengguna
CALL sp_tambah_foreign_key_jika_belum_ada('daftar_pembelian', 'fk_daftar_pembelian_id_pengguna', 'id_pengguna', 'pengguna', 'id_pengguna', 'SET NULL');

-- FK untuk log_aktivitas.id_pengguna -> pengguna.id_pengguna
CALL sp_tambah_foreign_key_jika_belum_ada('log_aktivitas', 'fk_log_aktivitas_id_pengguna', 'id_pengguna', 'pengguna', 'id_pengguna', 'SET NULL');

-- FK untuk notifikasi_dibaca (id_log -> log_aktivitas.id_log & id_pengguna -> pengguna.id_pengguna)
CALL sp_selaraskan_dan_tambah_fk_notifikasi_dibaca();

-- ----------------------------------------------------------------------------
-- Bersihkan Prosedur Pembantu Sementara
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_selaraskan_dan_tambah_fk_notifikasi_dibaca;
DROP PROCEDURE IF EXISTS sp_tambah_foreign_key_jika_belum_ada;
