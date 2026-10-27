-- =============================================================================
-- BASIS DATA: Toko_Firo
-- FILE: 08_tes.sql
-- DESKRIPSI: Skrip pengujian fungsionalitas dan integritas database Toko_Firo
-- PRASYARAT: Jalankan SETELAH proses seed dev (07_seed_dev.sql)
-- CATATAN: Pengujian yang menghasilkan error/SIGNAL dibungkus dalam komentar
--          agar skrip ini dapat dieksekusi secara otomatis (runnable).
--          Hilangkan tanda komentar ('--') pada baris yang ditandai untuk uji manual.
-- =============================================================================

USE `Toko_Firo`;

-- =============================================================================
-- TES 1: Jual melebihi stok → ERROR (Stok barang tidak mencukupi!)
-- Pensil 2B (BRG-008) memiliki stok = 0
-- =============================================================================
-- Langkah 1: Buat header nota penjualan baru
CALL sp_create_penjualan('PJ-TEST-001', 'Test Pelanggan', NULL, 'TUNAI');

-- Langkah 2: Uji tambah item melebihi stok (stok 0, minta 5)
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Stok barang tidak mencukupi!'
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- CALL sp_tambah_item_penjualan('PJ-TEST-001', 'BRG-008', 5);


-- =============================================================================
-- TES 2a: Hapus item dengan riwayat transaksi → ERROR
-- BRG-001 (Pulpen Standard AE7) sudah memiliki riwayat penjualan di PJ-20261003-001
-- =============================================================================
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Item tidak bisa dihapus karena sudah memiliki riwayat transaksi!'
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- CALL sp_delete_item('BRG-001', 1);


-- =============================================================================
-- TES 2b: Hapus item tanpa riwayat transaksi → SUKSES + LOG
-- BRG-004 (Map Plastik Folio) belum memiliki riwayat transaksi pada seed
-- =============================================================================
-- EXPECTED: Berhasil menghapus baris di daftar_item dan mencatat log ITEM_DIHAPUS
CALL sp_delete_item('BRG-004', 1);

-- Verifikasi log audit tercatat
SELECT id_log, waktu, id_pengguna, tipe_aksi, keterangan, data_sebelum
FROM log_aktivitas
WHERE tipe_aksi = 'ITEM_DIHAPUS'
ORDER BY id_log DESC
LIMIT 1;


-- =============================================================================
-- TES 3: Status Stok & Deteksi Item Kritis dari Nota
-- Kondisi pasca seed:
-- BRG-008 (stok 0)  -> HABIS
-- BRG-006 (stok 2 + beli 5 = 7, batas 5) -> NORMAL / sesuai status
-- =============================================================================
-- EXPECTED: Menampilkan status_stok yang sesuai formula bisnis
SELECT kode_item, nama_item, stok, batas_minimum, status_stok
FROM v_stok_item
WHERE kode_item IN ('BRG-008', 'BRG-006', 'BRG-003');

-- EXPECTED: Menampilkan item kritis (HABIS/MENIPIS) yang terdapat pada nota penjualan
CALL sp_item_kritis_dari_nota('PJ-20261003-001');


-- =============================================================================
-- TES 4: Retur Penjualan
-- BRG-001 terjual 3 pcs pada nota PJ-20261003-001 (stok awal 120 -> 117)
-- =============================================================================
-- Kasus A: Retur melebihi jumlah terjual (minta retur 5, terjual 3)
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Jumlah retur melebihi jumlah yang terjual!'
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- CALL sp_retur_penjualan('PJ-20261003-001', 'BRG-001', 5);

-- Kasus B: Retur sah (retur 2 pcs)
-- EXPECTED: Berhasil, stok kembali naik (+2 menjadi 119), subtotal bernilai negatif (-5000.00)
CALL sp_retur_penjualan('PJ-20261003-001', 'BRG-001', 2);

-- Verifikasi kenaikan stok: 120 - 3 + 2 = 119
SELECT kode_item, nama_item, stok
FROM daftar_item
WHERE kode_item = 'BRG-001';

-- Verifikasi transaksi retur dan subtotal bernilai negatif
SELECT id_detail, kode_penjualan, kode_item, retur_penjualan, subtotal
FROM daftar_transaksi
WHERE kode_penjualan = 'PJ-20261003-001'
  AND kode_item = 'BRG-001'
  AND retur_penjualan > 0;


-- =============================================================================
-- TES 5: Retur Pembelian
-- BRG-003 dibeli 10 rim di PB-20261003-001 (stok awal 4 + beli 10 = 14)
-- =============================================================================
-- Kasus A: Retur melebihi jumlah pembelian pada nota (minta 20, dibeli 10)
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Jumlah retur melebihi jumlah yang dibeli!'
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- CALL sp_retur_pembelian('PB-20261003-001', 'BRG-003', 20);

-- Kasus B: Retur melebihi stok fisik yang tersedia di toko
-- Misal stok toko saat ini 14, jika diminta retur 15:
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Stok tidak cukup untuk retur pembelian!'
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- CALL sp_retur_pembelian('PB-20261003-001', 'BRG-003', 15);


-- =============================================================================
-- TES 6: Alur Autentikasi OTP 2FA
-- Pengguna id = 1 (Junaidi)
-- =============================================================================
-- Langkah 1: Buat kode OTP baru
CALL sp_buat_otp(1, 'hash_benar_123');

-- Langkah 2: Verifikasi dengan kode hash yang salah
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Kode tidak valid' (jumlah percobaan bertambah)
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- CALL sp_verifikasi_otp(1, 'hash_salah');

-- Langkah 3: Buat OTP baru dan verifikasi dengan hash yang benar
-- EXPECTED: Sukses, mengembalikan kolom role ('ADMIN') dan menandai OTP digunakan
CALL sp_buat_otp(1, 'hash_benar_456');
CALL sp_verifikasi_otp(1, 'hash_benar_456');

-- Catatan skenario batas:
-- - OTP kedaluwarsa (> 5 menit): memicu SIGNAL 'Kode kedaluwarsa'
-- - Percobaan salah > 5 kali: memicu SIGNAL 'Terlalu banyak percobaan'


-- =============================================================================
-- TES 7: Rekap Saldo Harian (Jangkar Saldo & Filter Kasir)
-- Jangkar saldo awal 2026-10-03 = Rp 500.000,00
-- =============================================================================
-- Kasus A: Rekap toko keseluruhan (id_kasir = NULL)
-- EXPECTED: saldo_awal = 500000, pemasukan dari PJ-20261003-001,
--           pengeluaran dari PB-20261003-001, saldo_akhir, jumlah_nota
CALL sp_get_saldo_harian('2026-10-03', NULL);

-- Kasus B: Rekap per kasir Rina (id_kasir = 2)
-- EXPECTED: Hanya menampilkan pemasukan & nota milik Rina; saldo_awal & saldo_akhir = NULL
CALL sp_get_saldo_harian('2026-10-03', 2);


-- =============================================================================
-- TES 8: Proteksi Immutability Log Aktivitas (Trigger UPDATE & DELETE)
-- =============================================================================
-- Kasus A: Mencegah UPDATE pada log_aktivitas
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Log aktivitas tidak boleh diubah atau dihapus!'
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- UPDATE log_aktivitas SET keterangan = 'manipulasi log' WHERE id_log = 1;

-- Kasus B: Mencegah DELETE pada log_aktivitas
-- EXPECTED: ERROR SIGNAL SQLSTATE '45000' - 'Log aktivitas tidak boleh diubah atau dihapus!'
-- [UJI ERROR - BUKA KOMENTAR UNTUK UJI MANUAL]
-- DELETE FROM log_aktivitas WHERE id_log = 1;


-- =============================================================================
-- TES 9: Hak Akses Pengguna firo_katalog (Uji Manual Koneksi Terpisah)
-- Pengujian dilakukan dengan masuk sebagai user: 'firo_katalog'@'localhost'
-- =============================================================================
-- EXPECTED BERHASIL:
-- SELECT * FROM v_katalog_publik;

-- EXPECTED GAGAL (Access denied):
-- SELECT * FROM daftar_item;
-- SELECT * FROM daftar_penjualan;


-- =============================================================================
-- TES 10: Audit Log Perubahan Harga & Status Notifikasi Dibaca
-- Item BRG-005 (Stapler HD-10) diubah harganya oleh Admin (id_pengguna = 1)
-- =============================================================================
-- Langkah 1: Ubah harga barang
CALL sp_update_harga('BRG-005', 13000.00, 16000.00, 1);

-- Langkah 2: Verifikasi pencatatan log HARGA_DIUBAH di v_info_barang
-- EXPECTED: Menampilkan data_sebelum dan data_sesudah berisi riwayat harga lama dan baru
SELECT * FROM v_info_barang WHERE tipe_aksi = 'HARGA_DIUBAH' ORDER BY id_log DESC LIMIT 1;

-- Langkah 3: Periksa daftar info barang sebelum ditandai dibaca
-- EXPECTED: kolom sudah_dibaca = 0
CALL sp_get_info_barang(1, NULL, 20, 0);

-- Langkah 4: Tandai seluruh info barang telah dibaca oleh pengguna 1
CALL sp_tandai_dibaca(1, NULL);

-- Langkah 5: Periksa kembali daftar info barang setelah ditandai dibaca
-- EXPECTED: kolom sudah_dibaca = 1
CALL sp_get_info_barang(1, NULL, 20, 0);
