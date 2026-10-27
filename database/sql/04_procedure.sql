-- =============================================================================
-- Database: Toko_Firo
-- Berkas: 04_procedure.sql
-- Deskripsi: Definisi stored procedures untuk sistem Toko Firo (ATK & Fotokopi)
-- Karakteristik: Idempoten (DROP PROCEDURE IF EXISTS sebelum CREATE),
--                InnoDB, utf8mb4_0900_ai_ci, DELIMITER $$
-- =============================================================================

USE `Toko_Firo`;

-- =============================================================================
-- BAGIAN 1: PROSEDUR YANG DIPERBARUI (REPLACED PROCEDURES)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. sp_create_item
-- Deskripsi: Menambahkan item barang/jasa baru ke daftar_item dan mencatat log.
-- Aturan:
--   - Jika kode sudah ada -> SIGNAL 'Kode item sudah dipakai!'
--   - Jika p_tipe='JASA' dan (p_stok IS NULL atau 0) -> set stok = 999999
--   - Log ke log_aktivitas dengan tipe_aksi 'ITEM_BARU'
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_create_item`;
DELIMITER $$
CREATE PROCEDURE `sp_create_item`(
    IN `p_kode` VARCHAR(20),
    IN `p_nama` VARCHAR(100),
    IN `p_jenis` VARCHAR(50),
    IN `p_merek` VARCHAR(50),
    IN `p_rak` VARCHAR(20),
    IN `p_satuan` VARCHAR(20),
    IN `p_h_pokok` DECIMAL(15,2),
    IN `p_h_jual` DECIMAL(15,2),
    IN `p_stok` INT,
    IN `p_tipe` VARCHAR(20),
    IN `p_ket` TEXT,
    IN `p_batas` INT,
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_stok INT;
    DECLARE v_batas INT;

    IF EXISTS (SELECT 1 FROM `daftar_item` WHERE `kode_item` = p_kode) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode item sudah dipakai!';
    END IF;

    SET v_stok = p_stok;
    IF p_tipe = 'JASA' AND (v_stok IS NULL OR v_stok = 0) THEN
        SET v_stok = 999999;
    END IF;

    SET v_batas = COALESCE(p_batas, 5);

    INSERT INTO `daftar_item` (
        `kode_item`, `nama_item`, `jenis`, `merek`, `rak`, `tipe_item`,
        `satuan`, `harga_pokok`, `harga_jual`, `stok`, `keterangan`,
        `batas_minimum`, `dibuat_pada`, `diubah_pada`
    ) VALUES (
        p_kode, p_nama, p_jenis, p_merek, p_rak, p_tipe,
        p_satuan, p_h_pokok, p_h_jual, v_stok, p_ket,
        v_batas, NOW(), NOW()
    );

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Item baru: ', p_kode, ' - ', p_nama),
        'ITEM_BARU',
        p_id_pengguna,
        NULL,
        NULL
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 2. sp_update_item
-- Deskripsi: Memperbarui data umum item (nama, jenis, merek) dan mencatat log.
-- Logika: Menyimpan data lama dan mencatat JSON perubahan ke data_sebelum/sesudah.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_update_item`;
DELIMITER $$
CREATE PROCEDURE `sp_update_item`(
    IN `p_kode` VARCHAR(20),
    IN `p_nama` VARCHAR(100),
    IN `p_jenis` VARCHAR(50),
    IN `p_merek` VARCHAR(50),
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_old_nama VARCHAR(100);
    DECLARE v_old_jenis VARCHAR(50);
    DECLARE v_old_merek VARCHAR(50);

    SELECT `nama_item`, `jenis`, `merek`
    INTO v_old_nama, v_old_jenis, v_old_merek
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_old_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    UPDATE `daftar_item`
    SET `nama_item` = p_nama,
        `jenis` = p_jenis,
        `merek` = p_merek,
        `diubah_pada` = NOW()
    WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Item diubah: ', p_kode, ' - ', p_nama),
        'ITEM_DIUBAH',
        p_id_pengguna,
        JSON_OBJECT('nama', v_old_nama, 'jenis', v_old_jenis, 'merek', v_old_merek),
        JSON_OBJECT('nama', p_nama, 'jenis', p_jenis, 'merek', p_merek)
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 3. sp_update_harga
-- Deskripsi: Memperbarui harga pokok dan harga jual item serta mencatat log perubahan.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_update_harga`;
DELIMITER $$
CREATE PROCEDURE `sp_update_harga`(
    IN `p_kode` VARCHAR(20),
    IN `p_h_pokok` DECIMAL(15,2),
    IN `p_h_jual` DECIMAL(15,2),
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_old_h_pokok DECIMAL(15,2);
    DECLARE v_old_h_jual DECIMAL(15,2);
    DECLARE v_nama VARCHAR(100);

    SELECT `harga_pokok`, `harga_jual`, `nama_item`
    INTO v_old_h_pokok, v_old_h_jual, v_nama
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_old_h_pokok IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    UPDATE `daftar_item`
    SET `harga_pokok` = p_h_pokok,
        `harga_jual` = p_h_jual,
        `diubah_pada` = NOW()
    WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Ubah harga: ', p_kode, ' - ', v_nama),
        'HARGA_DIUBAH',
        p_id_pengguna,
        JSON_OBJECT('harga_pokok', v_old_h_pokok, 'harga_jual', v_old_h_jual),
        JSON_OBJECT('harga_pokok', p_h_pokok, 'harga_jual', p_h_jual)
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 4. sp_delete_item
-- Deskripsi: Menghapus item dari daftar_item jika belum memiliki riwayat transaksi.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_delete_item`;
DELIMITER $$
CREATE PROCEDURE `sp_delete_item`(
    IN `p_kode` VARCHAR(20),
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_nama VARCHAR(100);
    DECLARE v_jenis VARCHAR(50);
    DECLARE v_merek VARCHAR(50);
    DECLARE v_stok INT;
    DECLARE v_h_pokok DECIMAL(15,2);
    DECLARE v_h_jual DECIMAL(15,2);

    IF EXISTS (SELECT 1 FROM `daftar_transaksi` WHERE `kode_item` = p_kode) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak bisa dihapus karena sudah memiliki riwayat transaksi!';
    END IF;

    SELECT `nama_item`, `jenis`, `merek`, `stok`, `harga_pokok`, `harga_jual`
    INTO v_nama, v_jenis, v_merek, v_stok, v_h_pokok, v_h_jual
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    DELETE FROM `daftar_item` WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Hapus item: ', p_kode, ' - ', v_nama),
        'ITEM_DIHAPUS',
        p_id_pengguna,
        JSON_OBJECT(
            'kode_item', p_kode,
            'nama_item', v_nama,
            'jenis', v_jenis,
            'merek', v_merek,
            'stok', v_stok,
            'harga_pokok', v_h_pokok,
            'harga_jual', v_h_jual
        ),
        NULL
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 5. sp_create_penjualan
-- Deskripsi: Membuat header transaksi penjualan baru.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_create_penjualan`;
DELIMITER $$
CREATE PROCEDURE `sp_create_penjualan`(
    IN `p_kode` VARCHAR(20),
    IN `p_cust` VARCHAR(100),
    IN `p_id_kasir` INT,
    IN `p_metode` ENUM('TUNAI','QRIS')
)
BEGIN
    DECLARE v_cust VARCHAR(100);

    SET v_cust = TRIM(p_cust);
    IF v_cust IS NULL OR v_cust = '' THEN
        SET v_cust = 'Umum';
    END IF;

    INSERT INTO `daftar_penjualan` (
        `kode_transaksi`,
        `nama_pelanggan`,
        `tanggal_transaksi`,
        `total_bayar`,
        `id_kasir`,
        `metode_bayar`
    ) VALUES (
        p_kode,
        v_cust,
        CURDATE(),
        0.00,
        p_id_kasir,
        p_metode
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 6. sp_create_pembelian
-- Deskripsi: Membuat header transaksi pembelian baru ke supplier.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_create_pembelian`;
DELIMITER $$
CREATE PROCEDURE `sp_create_pembelian`(
    IN `p_kode` VARCHAR(20),
    IN `p_supp` VARCHAR(100),
    IN `p_id_pengguna` INT
)
BEGIN
    IF p_supp IS NULL OR TRIM(p_supp) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Nama supplier wajib diisi!';
    END IF;

    INSERT INTO `daftar_pembelian` (
        `kode_transaksi`,
        `nama_supplier`,
        `tanggal_transaksi`,
        `total_bayar`,
        `id_pengguna`
    ) VALUES (
        p_kode,
        TRIM(p_supp),
        CURDATE(),
        0.00,
        p_id_pengguna
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 7. sp_retur_penjualan
-- Deskripsi: Mencatat pengembalian barang dari pelanggan atas nota penjualan.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_retur_penjualan`;
DELIMITER $$
CREATE PROCEDURE `sp_retur_penjualan`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    DECLARE v_total_sold INT DEFAULT 0;
    DECLARE v_already_returned INT DEFAULT 0;

    IF p_qty IS NULL OR p_qty <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur harus lebih dari 0!';
    END IF;

    SELECT
        COALESCE(SUM(`item_keluar`), 0),
        COALESCE(SUM(`retur_penjualan`), 0)
    INTO v_total_sold, v_already_returned
    FROM `daftar_transaksi`
    WHERE `kode_penjualan` = p_nota AND `kode_item` = p_item;

    IF (p_qty + v_already_returned) > v_total_sold THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur melebihi jumlah yang terjual!';
    END IF;

    INSERT INTO `daftar_transaksi` (
        `kode_penjualan`, `kode_item`, `retur_penjualan`
    ) VALUES (
        p_nota, p_item, p_qty
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 8. sp_retur_pembelian
-- Deskripsi: Mencatat retur barang ke supplier atas nota pembelian.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_retur_pembelian`;
DELIMITER $$
CREATE PROCEDURE `sp_retur_pembelian`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    DECLARE v_total_bought INT DEFAULT 0;
    DECLARE v_already_returned INT DEFAULT 0;
    DECLARE v_stok INT;

    IF p_qty IS NULL OR p_qty <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur harus lebih dari 0!';
    END IF;

    SELECT
        COALESCE(SUM(`item_masuk`), 0),
        COALESCE(SUM(`retur_pembelian`), 0)
    INTO v_total_bought, v_already_returned
    FROM `daftar_transaksi`
    WHERE `kode_pembelian` = p_nota AND `kode_item` = p_item;

    IF (p_qty + v_already_returned) > v_total_bought THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Jumlah retur melebihi jumlah yang dibeli!';
    END IF;

    SELECT `stok` INTO v_stok
    FROM `daftar_item`
    WHERE `kode_item` = p_item;

    IF v_stok IS NULL OR v_stok < p_qty THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak cukup untuk retur pembelian!';
    END IF;

    INSERT INTO `daftar_transaksi` (
        `kode_pembelian`, `kode_item`, `retur_pembelian`
    ) VALUES (
        p_nota, p_item, p_qty
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 9. sp_get_laporan_penjualan
-- Deskripsi: Menampilkan laporan penjualan berdasarkan rentang tanggal dan kasir.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_laporan_penjualan`;
DELIMITER $$
CREATE PROCEDURE `sp_get_laporan_penjualan`(
    IN `start_d` DATE,
    IN `end_d` DATE,
    IN `p_id_kasir` INT
)
BEGIN
    IF p_id_kasir IS NOT NULL THEN
        SELECT lp.*
        FROM `v_laporan_penjualan` lp
        JOIN `daftar_penjualan` dp ON lp.`kode_transaksi` = dp.`kode_transaksi`
        WHERE lp.`tanggal_transaksi` BETWEEN start_d AND end_d
          AND dp.`id_kasir` = p_id_kasir;
    ELSE
        SELECT *
        FROM `v_laporan_penjualan`
        WHERE `tanggal_transaksi` BETWEEN start_d AND end_d;
    END IF;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 10. sp_cari_item
-- Deskripsi: Mencari barang berdasarkan kata kunci nama atau kode item.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_cari_item`;
DELIMITER $$
CREATE PROCEDURE `sp_cari_item`(
    IN `p_keyword` VARCHAR(100)
)
BEGIN
    SELECT *
    FROM `v_stok_item`
    WHERE `nama_item` LIKE CONCAT('%', p_keyword, '%')
       OR `kode_item` = p_keyword;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 11. sp_tambah_item_penjualan
-- Deskripsi: Menambahkan item keluar ke dalam transaksi penjualan.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tambah_item_penjualan`;
DELIMITER $$
CREATE PROCEDURE `sp_tambah_item_penjualan`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    INSERT INTO `daftar_transaksi` (`kode_penjualan`, `kode_item`, `item_keluar`)
    VALUES (p_nota, p_item, p_qty);
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 12. sp_tambah_item_pembelian
-- Deskripsi: Menambahkan item masuk ke dalam transaksi pembelian.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tambah_item_pembelian`;
DELIMITER $$
CREATE PROCEDURE `sp_tambah_item_pembelian`(
    IN `p_nota` VARCHAR(20),
    IN `p_item` VARCHAR(20),
    IN `p_qty` INT
)
BEGIN
    INSERT INTO `daftar_transaksi` (`kode_pembelian`, `kode_item`, `item_masuk`)
    VALUES (p_nota, p_item, p_qty);
END$$
DELIMITER ;


-- =============================================================================
-- BAGIAN 2: PROSEDUR BARU (NEW PROCEDURES)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- AUTHENTICATION
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 13. sp_get_pengguna_login
-- Deskripsi: Mengambil data kredensial pengguna yang berstatus aktif.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_pengguna_login`;
DELIMITER $$
CREATE PROCEDURE `sp_get_pengguna_login`(
    IN `p_username` VARCHAR(50)
)
BEGIN
    SELECT `id_pengguna`, `nama`, `username`, `password_hash`, `role`
    FROM `pengguna`
    WHERE `username` = p_username AND `status` = 'AKTIF';
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 14. sp_buat_otp
-- Deskripsi: Membatalkan OTP lama dan membuat kode OTP baru dengan masa berlaku 5 menit.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_buat_otp`;
DELIMITER $$
CREATE PROCEDURE `sp_buat_otp`(
    IN `p_id_pengguna` INT,
    IN `p_kode_hash` VARCHAR(255)
)
BEGIN
    UPDATE `otp_2fa`
    SET `digunakan` = 1
    WHERE `id_pengguna` = p_id_pengguna AND `digunakan` = 0;

    INSERT INTO `otp_2fa` (
        `id_pengguna`,
        `kode_hash`,
        `dibuat_pada`,
        `kadaluarsa_pada`,
        `digunakan`,
        `percobaan`
    ) VALUES (
        p_id_pengguna,
        p_kode_hash,
        NOW(),
        NOW() + INTERVAL 5 MINUTE,
        0,
        0
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 15. sp_verifikasi_otp
-- Deskripsi: Verifikasi kecocokan OTP, kedaluwarsa, dan batas percobaan (maksimal 5 kali).
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_verifikasi_otp`;
DELIMITER $$
CREATE PROCEDURE `sp_verifikasi_otp`(
    IN `p_id_pengguna` INT,
    IN `p_kode_hash` VARCHAR(255)
)
BEGIN
    DECLARE v_id_otp INT;
    DECLARE v_kadaluarsa DATETIME;
    DECLARE v_percobaan INT;
    DECLARE v_kode_hash VARCHAR(255);

    SELECT `id_otp`, `kadaluarsa_pada`, `percobaan`, `kode_hash`
    INTO v_id_otp, v_kadaluarsa, v_percobaan, v_kode_hash
    FROM `otp_2fa`
    WHERE `id_pengguna` = p_id_pengguna AND `digunakan` = 0
    ORDER BY `dibuat_pada` DESC, `id_otp` DESC
    LIMIT 1;

    IF v_id_otp IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode tidak valid';
    END IF;

    IF v_kadaluarsa < NOW() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode kedaluwarsa';
    END IF;

    IF v_percobaan >= 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Terlalu banyak percobaan';
    END IF;

    IF v_kode_hash != p_kode_hash THEN
        UPDATE `otp_2fa`
        SET `percobaan` = `percobaan` + 1
        WHERE `id_otp` = v_id_otp;

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kode tidak valid';
    END IF;

    UPDATE `otp_2fa`
    SET `digunakan` = 1
    WHERE `id_otp` = v_id_otp;

    SELECT `role`
    FROM `pengguna`
    WHERE `id_pengguna` = p_id_pengguna;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 16. sp_catat_login
-- Deskripsi: Memperbarui waktu login terakhir pengguna dan mencatat log aktivitas LOGIN.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_catat_login`;
DELIMITER $$
CREATE PROCEDURE `sp_catat_login`(
    IN `p_id_pengguna` INT
)
BEGIN
    DECLARE v_username VARCHAR(50);

    SELECT `username` INTO v_username
    FROM `pengguna`
    WHERE `id_pengguna` = p_id_pengguna;

    UPDATE `pengguna`
    SET `login_terakhir` = NOW()
    WHERE `id_pengguna` = p_id_pengguna;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`
    ) VALUES (
        NOW(),
        CONCAT('Login: ', COALESCE(v_username, '')),
        'LOGIN',
        p_id_pengguna
    );
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- MANAJEMEN PENGGUNA (ACCOUNT MANAGEMENT)
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 17. sp_create_pengguna
-- Deskripsi: Menambahkan pengguna baru ke sistem dan mencatat log PENGGUNA_BARU.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_create_pengguna`;
DELIMITER $$
CREATE PROCEDURE `sp_create_pengguna`(
    IN `p_nama` VARCHAR(100),
    IN `p_username` VARCHAR(50),
    IN `p_password_hash` VARCHAR(255),
    IN `p_role` ENUM('ADMIN','KASIR'),
    IN `p_pelaku` INT
)
BEGIN
    IF EXISTS (SELECT 1 FROM `pengguna` WHERE `username` = p_username) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Username sudah dipakai!';
    END IF;

    INSERT INTO `pengguna` (
        `nama`, `username`, `password_hash`, `role`, `status`, `dibuat_pada`, `diubah_pada`
    ) VALUES (
        p_nama, p_username, p_password_hash, p_role, 'AKTIF', NOW(), NOW()
    );

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`
    ) VALUES (
        NOW(),
        CONCAT('Pengguna baru: ', p_username),
        'PENGGUNA_BARU',
        p_pelaku
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 18. sp_update_pengguna
-- Deskripsi: Memperbarui profil pengguna (nama, username, role) dan mencatat log.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_update_pengguna`;
DELIMITER $$
CREATE PROCEDURE `sp_update_pengguna`(
    IN `p_id` INT,
    IN `p_nama` VARCHAR(100),
    IN `p_username` VARCHAR(50),
    IN `p_role` ENUM('ADMIN','KASIR'),
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_old_nama VARCHAR(100);
    DECLARE v_old_username VARCHAR(50);
    DECLARE v_old_role VARCHAR(20);

    SELECT `nama`, `username`, `role`
    INTO v_old_nama, v_old_username, v_old_role
    FROM `pengguna`
    WHERE `id_pengguna` = p_id;

    IF v_old_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!';
    END IF;

    IF EXISTS (SELECT 1 FROM `pengguna` WHERE `username` = p_username AND `id_pengguna` != p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Username sudah dipakai!';
    END IF;

    UPDATE `pengguna`
    SET `nama` = p_nama,
        `username` = p_username,
        `role` = p_role,
        `diubah_pada` = NOW()
    WHERE `id_pengguna` = p_id;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Update pengguna: ', p_username),
        'PENGGUNA_DIUBAH',
        p_pelaku,
        JSON_OBJECT('nama', v_old_nama, 'username', v_old_username, 'role', v_old_role),
        JSON_OBJECT('nama', p_nama, 'username', p_username, 'role', p_role)
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 19. sp_set_status_pengguna
-- Deskripsi: Mengubah status pengguna (AKTIF/NONAKTIF) dan mencatat log STATUS_PENGGUNA.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_set_status_pengguna`;
DELIMITER $$
CREATE PROCEDURE `sp_set_status_pengguna`(
    IN `p_id` INT,
    IN `p_status` ENUM('AKTIF','NONAKTIF'),
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_old_status VARCHAR(20);
    DECLARE v_username VARCHAR(50);

    SELECT `status`, `username`
    INTO v_old_status, v_username
    FROM `pengguna`
    WHERE `id_pengguna` = p_id;

    IF v_username IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!';
    END IF;

    UPDATE `pengguna`
    SET `status` = p_status,
        `diubah_pada` = NOW()
    WHERE `id_pengguna` = p_id;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Ubah status pengguna: ', v_username, ' menjadi ', p_status),
        'STATUS_PENGGUNA',
        p_pelaku,
        JSON_OBJECT('status', v_old_status),
        JSON_OBJECT('status', p_status)
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 20. sp_reset_password
-- Deskripsi: Mengatur ulang hash password pengguna dan mencatat log RESET_PASSWORD.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_reset_password`;
DELIMITER $$
CREATE PROCEDURE `sp_reset_password`(
    IN `p_id` INT,
    IN `p_password_hash` VARCHAR(255),
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_username VARCHAR(50);

    SELECT `username` INTO v_username
    FROM `pengguna`
    WHERE `id_pengguna` = p_id;

    IF v_username IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!';
    END IF;

    UPDATE `pengguna`
    SET `password_hash` = p_password_hash,
        `diubah_pada` = NOW()
    WHERE `id_pengguna` = p_id;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`
    ) VALUES (
        NOW(),
        CONCAT('Reset password pengguna: ', v_username),
        'RESET_PASSWORD',
        p_pelaku
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 21. sp_delete_pengguna
-- Deskripsi: Menghapus akun pengguna jika tidak memiliki transaksi dan bukan akun sendiri.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_delete_pengguna`;
DELIMITER $$
CREATE PROCEDURE `sp_delete_pengguna`(
    IN `p_id` INT,
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_username VARCHAR(50);
    DECLARE v_nama VARCHAR(100);

    IF p_id = p_pelaku THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tidak bisa menghapus akun sendiri!';
    END IF;

    IF EXISTS (SELECT 1 FROM `daftar_penjualan` WHERE `id_kasir` = p_id)
       OR EXISTS (SELECT 1 FROM `daftar_pembelian` WHERE `id_pengguna` = p_id) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Akun tidak bisa dihapus karena sudah memiliki transaksi! Nonaktifkan akun.';
    END IF;

    SELECT `username`, `nama` INTO v_username, v_nama
    FROM `pengguna`
    WHERE `id_pengguna` = p_id;

    IF v_username IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pengguna tidak ditemukan!';
    END IF;

    DELETE FROM `pengguna` WHERE `id_pengguna` = p_id;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Hapus pengguna: ', v_username),
        'PENGGUNA_DIHAPUS',
        p_pelaku,
        JSON_OBJECT('id_pengguna', p_id, 'username', v_username, 'nama', v_nama),
        NULL
    );
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- MANAJEMEN STOK (STOCK MANAGEMENT)
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 22. sp_set_batas_minimum
-- Deskripsi: Mengatur batas minimum stok untuk suatu item dan mencatat log BATAS_STOK.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_set_batas_minimum`;
DELIMITER $$
CREATE PROCEDURE `sp_set_batas_minimum`(
    IN `p_kode` VARCHAR(20),
    IN `p_nilai` INT,
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_old_batas INT;
    DECLARE v_nama VARCHAR(100);

    IF p_nilai < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Batas minimum tidak boleh negatif!';
    END IF;

    SELECT `batas_minimum`, `nama_item`
    INTO v_old_batas, v_nama
    FROM `daftar_item`
    WHERE `kode_item` = p_kode;

    IF v_nama IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Item tidak ditemukan!';
    END IF;

    UPDATE `daftar_item`
    SET `batas_minimum` = p_nilai,
        `diubah_pada` = NOW()
    WHERE `kode_item` = p_kode;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Set batas minimum item: ', p_kode, ' menjadi ', p_nilai),
        'BATAS_STOK',
        p_pelaku,
        JSON_OBJECT('batas_minimum', v_old_batas),
        JSON_OBJECT('batas_minimum', p_nilai)
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 23. sp_set_batas_minimum_semua
-- Deskripsi: Mengatur batas minimum stok untuk semua item non-jasa secara massal.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_set_batas_minimum_semua`;
DELIMITER $$
CREATE PROCEDURE `sp_set_batas_minimum_semua`(
    IN `p_nilai` INT,
    IN `p_pelaku` INT
)
BEGIN
    IF p_nilai < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Batas minimum tidak boleh negatif!';
    END IF;

    UPDATE `daftar_item`
    SET `batas_minimum` = p_nilai,
        `diubah_pada` = NOW()
    WHERE `tipe_item` != 'JASA' OR `tipe_item` IS NULL;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Set batas minimum semua item menjadi ', p_nilai),
        'BATAS_STOK',
        p_pelaku,
        NULL,
        JSON_OBJECT('batas_minimum_semua', p_nilai)
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 24. sp_item_kritis_dari_nota
-- Deskripsi: Menampilkan item dari nota tertentu yang stoknya <= batas_minimum.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_item_kritis_dari_nota`;
DELIMITER $$
CREATE PROCEDURE `sp_item_kritis_dari_nota`(
    IN `p_kode_nota` VARCHAR(20)
)
BEGIN
    SELECT DISTINCT
        di.`kode_item`,
        di.`nama_item`,
        di.`stok`,
        di.`batas_minimum`,
        CASE
            WHEN di.`stok` <= 0 THEN 'HABIS'
            ELSE 'MENIPIS'
        END AS `status_stok`
    FROM `daftar_transaksi` dt
    JOIN `daftar_item` di ON dt.`kode_item` = di.`kode_item`
    WHERE (dt.`kode_penjualan` = p_kode_nota OR dt.`kode_pembelian` = p_kode_nota)
      AND di.`stok` <= di.`batas_minimum`;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- KELOLA SALDO HARIAN
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 25. sp_set_saldo_awal
-- Deskripsi: Menyimpan atau memperbarui saldo awal pada tanggal tertentu.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_set_saldo_awal`;
DELIMITER $$
CREATE PROCEDURE `sp_set_saldo_awal`(
    IN `p_tanggal` DATE,
    IN `p_nilai` DECIMAL(15,2),
    IN `p_pelaku` INT
)
BEGIN
    INSERT INTO `saldo_harian` (
        `tanggal`, `saldo_awal`, `diatur_oleh`, `dibuat_pada`
    ) VALUES (
        p_tanggal, p_nilai, p_pelaku, NOW()
    ) ON DUPLICATE KEY UPDATE
        `saldo_awal` = p_nilai,
        `diatur_oleh` = p_pelaku;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Atur saldo awal tanggal ', p_tanggal, ' sebesar ', p_nilai),
        'SALDO_DIATUR',
        p_pelaku,
        NULL,
        JSON_OBJECT('tanggal', p_tanggal, 'saldo_awal', p_nilai)
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 26. sp_get_saldo_harian
-- Deskripsi: Menghitung posisi saldo hari tertentu berdasarkan tanggal jangkar
--            terdekat ditambah akumulasi transaksi sampai hari tersebut.
--            Jika p_id_kasir diisi, hanya menampilkan pemasukan kasir tersebut.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_saldo_harian`;
DELIMITER $$
CREATE PROCEDURE `sp_get_saldo_harian`(
    IN `p_tanggal` DATE,
    IN `p_id_kasir` INT
)
BEGIN
    DECLARE v_tgl_jangkar DATE;
    DECLARE v_saldo_awal DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pemasukan_sebelumnya DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pengeluaran_sebelumnya DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_saldo_awal_hari DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pemasukan_hari DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_pengeluaran_hari DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_saldo_akhir DECIMAL(15,2) DEFAULT 0.00;
    DECLARE v_jumlah_nota INT DEFAULT 0;

    -- Cari tanggal jangkar saldo_harian terdekat <= p_tanggal
    SELECT MAX(`tanggal`) INTO v_tgl_jangkar
    FROM `saldo_harian`
    WHERE `tanggal` <= p_tanggal;

    IF v_tgl_jangkar IS NULL THEN
        SET v_saldo_awal = 0.00;
        SET v_tgl_jangkar = '1970-01-01';
    ELSE
        SELECT `saldo_awal` INTO v_saldo_awal
        FROM `saldo_harian`
        WHERE `tanggal` = v_tgl_jangkar;
    END IF;

    -- Hitung akumulasi pemasukan dan pengeluaran dari v_tgl_jangkar hingga p_tanggal - 1 hari
    IF v_tgl_jangkar < p_tanggal THEN
        SELECT COALESCE(SUM(`total_bayar`), 0.00) INTO v_pemasukan_sebelumnya
        FROM `daftar_penjualan`
        WHERE `tanggal_transaksi` >= v_tgl_jangkar
          AND `tanggal_transaksi` < p_tanggal;

        SELECT COALESCE(SUM(`total_bayar`), 0.00) INTO v_pengeluaran_sebelumnya
        FROM `daftar_pembelian`
        WHERE `tanggal_transaksi` >= v_tgl_jangkar
          AND `tanggal_transaksi` < p_tanggal;
    END IF;

    SET v_saldo_awal_hari = v_saldo_awal + v_pemasukan_sebelumnya - v_pengeluaran_sebelumnya;

    -- Jika filter kasir aktif: tampilkan hanya pemasukan dan jumlah nota kasir tersebut
    IF p_id_kasir IS NOT NULL THEN
        SELECT
            COALESCE(SUM(`total_bayar`), 0.00),
            COUNT(*)
        INTO v_pemasukan_hari, v_jumlah_nota
        FROM `daftar_penjualan`
        WHERE `tanggal_transaksi` = p_tanggal
          AND `id_kasir` = p_id_kasir;

        SELECT
            NULL AS `saldo_awal_hari`,
            v_pemasukan_hari AS `pemasukan`,
            NULL AS `pengeluaran`,
            NULL AS `saldo_akhir`,
            v_jumlah_nota AS `jumlah_nota`;
    ELSE
        SELECT
            COALESCE(SUM(`total_bayar`), 0.00),
            COUNT(*)
        INTO v_pemasukan_hari, v_jumlah_nota
        FROM `daftar_penjualan`
        WHERE `tanggal_transaksi` = p_tanggal;

        SELECT COALESCE(SUM(`total_bayar`), 0.00) INTO v_pengeluaran_hari
        FROM `daftar_pembelian`
        WHERE `tanggal_transaksi` = p_tanggal;

        SET v_saldo_akhir = v_saldo_awal_hari + v_pemasukan_hari - v_pengeluaran_hari;

        SELECT
            v_saldo_awal_hari AS `saldo_awal_hari`,
            v_pemasukan_hari AS `pemasukan`,
            v_pengeluaran_hari AS `pengeluaran`,
            v_saldo_akhir AS `saldo_akhir`,
            v_jumlah_nota AS `jumlah_nota`;
    END IF;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- LAPORAN DAN STATISTIK
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 27. sp_ringkasan_dashboard
-- Deskripsi: Menghasilkan KPI dashboard: total penjualan, jumlah nota,
--            jumlah item kritis, dan timestamp backup berhasil terakhir.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_ringkasan_dashboard`;
DELIMITER $$
CREATE PROCEDURE `sp_ringkasan_dashboard`(
    IN `p_tanggal` DATE
)
BEGIN
    SELECT
        (SELECT COALESCE(SUM(`total_bayar`), 0.00)
         FROM `daftar_penjualan`
         WHERE `tanggal_transaksi` = p_tanggal) AS `total_penjualan`,

        (SELECT COUNT(*)
         FROM `daftar_penjualan`
         WHERE `tanggal_transaksi` = p_tanggal) AS `jumlah_nota`,

        (SELECT COUNT(*)
         FROM `daftar_item`
         WHERE `stok` <= `batas_minimum`
           AND (`tipe_item` != 'JASA' OR `tipe_item` IS NULL)) AS `jumlah_item_kritis`,

        (SELECT MAX(`waktu`)
         FROM `riwayat_backup`
         WHERE `status` = 'BERHASIL') AS `backup_terakhir`;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 28. sp_barang_terlaris
-- Deskripsi: Menampilkan barang terlaris dalam rentang tanggal tertentu dengan limit.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_barang_terlaris`;
DELIMITER $$
CREATE PROCEDURE `sp_barang_terlaris`(
    IN `p_start` DATE,
    IN `p_end` DATE,
    IN `p_limit` INT
)
BEGIN
    DECLARE v_limit INT;
    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 10, p_limit);

    SELECT
        di.`kode_item`,
        di.`nama_item`,
        SUM(COALESCE(dt.`item_keluar`, 0) - COALESCE(dt.`retur_penjualan`, 0)) AS `total_terjual`
    FROM `daftar_transaksi` dt
    JOIN `daftar_item` di USING (`kode_item`)
    WHERE dt.`kode_penjualan` IS NOT NULL
      AND dt.`tanggal_log` >= p_start
      AND dt.`tanggal_log` < p_end + INTERVAL 1 DAY
    GROUP BY di.`kode_item`, di.`nama_item`
    ORDER BY `total_terjual` DESC
    LIMIT v_limit;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- INFORMASI DAN LOG AKTIVITAS
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 29. sp_get_info_barang
-- Deskripsi: Menampilkan info log barang disertai status sudah dibaca per pengguna.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_info_barang`;
DELIMITER $$
CREATE PROCEDURE `sp_get_info_barang`(
    IN `p_id_pengguna` INT,
    IN `p_tipe` VARCHAR(50),
    IN `p_limit` INT,
    IN `p_offset` INT
)
BEGIN
    DECLARE v_limit INT;
    DECLARE v_offset INT;

    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 20, p_limit);
    SET v_offset = IF(p_offset IS NULL OR p_offset < 0, 0, p_offset);

    SELECT
        vib.*,
        IF(nd.`id_log` IS NOT NULL, 1, 0) AS `sudah_dibaca`
    FROM `v_info_barang` vib
    LEFT JOIN `notifikasi_dibaca` nd
      ON nd.`id_log` = vib.`id_log`
     AND nd.`id_pengguna` = p_id_pengguna
    WHERE (p_tipe IS NULL OR vib.`tipe_aksi` = p_tipe)
    ORDER BY vib.`waktu` DESC
    LIMIT v_limit OFFSET v_offset;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 30. sp_tandai_dibaca
-- Deskripsi: Menandai notifikasi info barang telah dibaca oleh pengguna.
--            Jika p_id_log NULL, menandai semua yang belum dibaca.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_tandai_dibaca`;
DELIMITER $$
CREATE PROCEDURE `sp_tandai_dibaca`(
    IN `p_id_pengguna` INT,
    IN `p_id_log` INT
)
BEGIN
    IF p_id_log IS NULL THEN
        INSERT INTO `notifikasi_dibaca` (`id_log`, `id_pengguna`, `dibaca_pada`)
        SELECT `id_log`, p_id_pengguna, NOW()
        FROM `v_info_barang`
        WHERE `id_log` NOT IN (
            SELECT `id_log`
            FROM `notifikasi_dibaca`
            WHERE `id_pengguna` = p_id_pengguna
        );
    ELSE
        INSERT IGNORE INTO `notifikasi_dibaca` (`id_log`, `id_pengguna`, `dibaca_pada`)
        VALUES (p_id_log, p_id_pengguna, NOW());
    END IF;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 31. sp_get_log
-- Deskripsi: Mengambil riwayat log aktivitas dengan filter tipe, rentang tanggal,
--            kata kunci keterangan, serta paginasi.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_log`;
DELIMITER $$
CREATE PROCEDURE `sp_get_log`(
    IN `p_tipe` VARCHAR(50),
    IN `p_start` DATE,
    IN `p_end` DATE,
    IN `p_keyword` VARCHAR(100),
    IN `p_limit` INT,
    IN `p_offset` INT
)
BEGIN
    DECLARE v_limit INT;
    DECLARE v_offset INT;

    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 50, p_limit);
    SET v_offset = IF(p_offset IS NULL OR p_offset < 0, 0, p_offset);

    SELECT
        la.*,
        p.`nama` AS `nama_pengguna`
    FROM `log_aktivitas` la
    LEFT JOIN `pengguna` p ON la.`id_pengguna` = p.`id_pengguna`
    WHERE (p_tipe IS NULL OR la.`tipe_aksi` = p_tipe)
      AND (p_start IS NULL OR la.`waktu` >= p_start)
      AND (p_end IS NULL OR la.`waktu` < p_end + INTERVAL 1 DAY)
      AND (p_keyword IS NULL OR la.`keterangan` LIKE CONCAT('%', p_keyword, '%'))
    ORDER BY la.`waktu` DESC
    LIMIT v_limit OFFSET v_offset;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- BACKUP, NOTIFIKASI & PENGATURAN
-- -----------------------------------------------------------------------------

-- -----------------------------------------------------------------------------
-- 32. sp_catat_backup
-- Deskripsi: Mencatat riwayat pencadangan database ke riwayat_backup dan log_aktivitas.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_catat_backup`;
DELIMITER $$
CREATE PROCEDURE `sp_catat_backup`(
    IN `p_jenis` VARCHAR(50),
    IN `p_ukuran_kb` INT,
    IN `p_status` VARCHAR(50),
    IN `p_nama_file` VARCHAR(255),
    IN `p_pesan_error` TEXT,
    IN `p_pelaku` INT
)
BEGIN
    INSERT INTO `riwayat_backup` (
        `waktu`, `jenis`, `ukuran_kb`, `status`, `nama_file`, `pesan_error`, `id_pengguna`
    ) VALUES (
        NOW(), p_jenis, p_ukuran_kb, p_status, p_nama_file, p_pesan_error, p_pelaku
    );

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Backup database: ', p_nama_file, ' (', p_status, ')'),
        'BACKUP',
        p_pelaku,
        NULL,
        JSON_OBJECT(
            'jenis', p_jenis,
            'ukuran_kb', p_ukuran_kb,
            'status', p_status,
            'nama_file', p_nama_file
        )
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 33. sp_catat_notifikasi
-- Deskripsi: Menambahkan catatan pengiriman notifikasi ke log_notifikasi.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_catat_notifikasi`;
DELIMITER $$
CREATE PROCEDURE `sp_catat_notifikasi`(
    IN `p_jenis` VARCHAR(50),
    IN `p_tujuan` VARCHAR(50),
    IN `p_penerima` VARCHAR(100),
    IN `p_isi` TEXT
)
BEGIN
    DECLARE v_tujuan VARCHAR(50);

    SET v_tujuan = COALESCE(NULLIF(TRIM(p_tujuan), ''), 'TELEGRAM');

    INSERT INTO `log_notifikasi` (
        `waktu`, `jenis`, `tujuan`, `penerima`, `isi`, `status`
    ) VALUES (
        NOW(), p_jenis, v_tujuan, p_penerima, p_isi, 'MENUNGGU'
    );
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 34. sp_update_status_notifikasi
-- Deskripsi: Memperbarui status notifikasi (misal: TERKIRIM, GAGAL) beserta pesan error.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_update_status_notifikasi`;
DELIMITER $$
CREATE PROCEDURE `sp_update_status_notifikasi`(
    IN `p_id` INT,
    IN `p_status` VARCHAR(50),
    IN `p_pesan_error` TEXT
)
BEGIN
    UPDATE `log_notifikasi`
    SET `status` = p_status,
        `pesan_error` = p_pesan_error
    WHERE `id_notif` = p_id;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 35. sp_get_log_notifikasi
-- Deskripsi: Mengambil riwayat log notifikasi dengan opsi filter jenis dan paginasi.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_log_notifikasi`;
DELIMITER $$
CREATE PROCEDURE `sp_get_log_notifikasi`(
    IN `p_jenis` VARCHAR(50),
    IN `p_limit` INT,
    IN `p_offset` INT
)
BEGIN
    DECLARE v_limit INT;
    DECLARE v_offset INT;

    SET v_limit = IF(p_limit IS NULL OR p_limit <= 0, 50, p_limit);
    SET v_offset = IF(p_offset IS NULL OR p_offset < 0, 0, p_offset);

    SELECT *
    FROM `log_notifikasi`
    WHERE (p_jenis IS NULL OR `jenis` = p_jenis)
    ORDER BY `waktu` DESC
    LIMIT v_limit OFFSET v_offset;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 36. sp_get_setting
-- Deskripsi: Mengambil konfigurasi sistem berdasarkan kunci pengaturan.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_setting`;
DELIMITER $$
CREATE PROCEDURE `sp_get_setting`(
    IN `p_kunci` VARCHAR(50)
)
BEGIN
    SELECT *
    FROM `pengaturan_notifikasi`
    WHERE `kunci` = p_kunci;
END$$
DELIMITER ;

-- -----------------------------------------------------------------------------
-- 37. sp_set_setting
-- Deskripsi: Menyimpan atau memperbarui nilai pengaturan serta mencatat log perubahan.
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_set_setting`;
DELIMITER $$
CREATE PROCEDURE `sp_set_setting`(
    IN `p_kunci` VARCHAR(50),
    IN `p_nilai` VARCHAR(255),
    IN `p_pelaku` INT
)
BEGIN
    DECLARE v_old_nilai VARCHAR(255);

    SELECT `nilai` INTO v_old_nilai
    FROM `pengaturan_notifikasi`
    WHERE `kunci` = p_kunci;

    INSERT INTO `pengaturan_notifikasi` (`kunci`, `nilai`)
    VALUES (p_kunci, p_nilai)
    ON DUPLICATE KEY UPDATE `nilai` = p_nilai;

    INSERT INTO `log_aktivitas` (
        `waktu`, `keterangan`, `tipe_aksi`, `id_pengguna`, `data_sebelum`, `data_sesudah`
    ) VALUES (
        NOW(),
        CONCAT('Ubah pengaturan: ', p_kunci),
        'SETTING_DIUBAH',
        p_pelaku,
        IF(v_old_nilai IS NOT NULL, JSON_OBJECT('nilai', v_old_nilai), NULL),
        JSON_OBJECT('nilai', p_nilai)
    );
END$$
DELIMITER ;
