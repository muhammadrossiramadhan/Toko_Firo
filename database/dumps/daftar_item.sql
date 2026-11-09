-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 08, 2026 at 10:02 PM
-- Server version: 13.1.1-MariaDB-log
-- PHP Version: 8.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_firo`
--

-- --------------------------------------------------------

--
-- Table structure for table `daftar_item`
--

CREATE TABLE `daftar_item` (
  `kode_item` varchar(20) NOT NULL,
  `nama_item` varchar(100) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `merek` varchar(50) DEFAULT NULL,
  `rak` varchar(20) DEFAULT NULL,
  `tipe_item` varchar(20) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `harga_pokok` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_jual` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `batas_minimum` int(11) NOT NULL DEFAULT 5 CHECK (`batas_minimum` >= 0),
  `link_shopee` varchar(255) DEFAULT NULL,
  `deskripsi_produk` text DEFAULT NULL,
  `gambar_url` varchar(255) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daftar_item`
--

INSERT INTO `daftar_item` (`kode_item`, `nama_item`, `jenis`, `merek`, `rak`, `tipe_item`, `satuan`, `harga_pokok`, `harga_jual`, `stok`, `keterangan`, `foto`, `batas_minimum`, `link_shopee`, `deskripsi_produk`, `gambar_url`, `dibuat_pada`, `diubah_pada`, `deleted_at`) VALUES
('BRG-001', 'Buku Tulis Okey 32 Halaman Sampul Coklat', 'Alat Tulis', 'Okey', 'A1', 'BARANG', 'pcs', 2000.00, 4000.00, 120, 'Buku Tulis Okey 32 Halaman hadir dengan sampul cokelat polos dan tampilan minimalis yang mendukung suasana belajar tenang.', NULL, 5, 'https://shopee.co.id/-PCS-Buku-Tulis-Okey-32-Halaman-Sampul-Coklat-i.105190766.50619319560?extraParams=%7B%22display_model_id%22%3A278022993601%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81zth-mud29156f37pe2', 'Buku Tulis Okey 32 Halaman hadir dengan sampul cokelat polos dan tampilan minimalis yang mendukung suasana belajar tenang.', 'https://down-id.img.susercontent.com/file/id-11134207-81zth-mud29156f37pe2@resize_w900_nl.webp', '2026-10-20 08:40:12', '2026-11-05 22:41:29', NULL),
('BRG-002', 'Map Plastik Bussines Snelhecter', 'Map', 'Kiky', 'A2', 'BARANG', 'PAK', 35000.00, 37000.00, 59, 'harga diatas merupakan harga PACK isi 12 pcs map', NULL, 5, 'https://shopee.co.id/-PACK-Map-Plastik-Bussines-Snelhecter-i.105190766.47421030512?extraParams=%7B%22display_model_id%22%3A273063677668%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134253-81zth-mv0abhpbh7np43', 'harga diatas merupakan harga PACK isi 12 pcs map', 'https://down-id.img.susercontent.com/file/id-11134253-81zth-mv0abhpbh7np43@resize_w900_nl.webp', '2026-10-20 08:40:12', '2026-11-05 22:42:17', NULL),
('BRG-003', 'Buku Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko', 'Buku', 'Ideals Children\'s Book', 'B1', 'BARANG', 'pcs', 13000.00, 15000.00, 4, 'Ringkas, rapi, dan mudah dibawa—Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko siap menemani belajar kapan saja. Tampilan bergambar membantu memperjelas arti, sementara layout yang jelas membuat pencarian kata terasa lebih nyaman.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- Bergambar: ilustrasi membantu pemahaman arti kata.\r\n\r\n- Grammar & Conversation: dilengkapi materi grammar dan percakapan.\r\n\r\n- Cara membaca: dilengkapi cara membaca Inggris-Indonesia original.\r\n\r\n\r\n\r\nInformasi Produk\r\n\r\n- Penulis: Drs. P. Handoko\r\n\r\n- Penerbit: Lingkar Media\r\n\r\n- Jumlah Halaman: 128 hlm\r\n\r\n- Ukuran: 15 x 24 cm\r\n\r\n- Kertas: berwarna\r\n\r\n- Cover: Soft Cover\r\n\r\n- Berat: 200 gr\r\n\r\n- ISBN: 979-9994-339-6\r\n\r\n- Asal Produk: Indonesia\r\n\r\n- Impor/Lokal: Lokal', NULL, 5, 'https://shopee.co.id/Buku-Kamus-Ideal-Inggris-Indonesia-Bergambar-Drs.-P.-Handoko-i.105190766.54719275039?extraParams=%7B%22display_model_id%22%3A361615357532%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81ztf-mubm296rjtok6b', 'Ringkas, rapi, dan mudah dibawa—Kamus Ideal Inggris-Indonesia Bergambar Drs. P. Handoko siap menemani belajar kapan saja. Tampilan bergambar membantu memperjelas arti, sementara layout yang jelas membuat pencarian kata terasa lebih nyaman.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- Bergambar: ilustrasi membantu pemahaman arti kata.\r\n\r\n- Grammar & Conversation: dilengkapi materi grammar dan percakapan.\r\n\r\n- Cara membaca: dilengkapi cara membaca Inggris-Indonesia original.\r\n\r\n\r\n\r\nInformasi Produk\r\n\r\n- Penulis: Drs. P. Handoko\r\n\r\n- Penerbit: Lingkar Media\r\n\r\n- Jumlah Halaman: 128 hlm\r\n\r\n- Ukuran: 15 x 24 cm\r\n\r\n- Kertas: berwarna\r\n\r\n- Cover: Soft Cover\r\n\r\n- Berat: 200 gr\r\n\r\n- ISBN: 979-9994-339-6\r\n\r\n- Asal Produk: Indonesia\r\n\r\n- Impor/Lokal: Lokal', 'https://down-id.img.susercontent.com/file/id-11134207-81ztf-mubm296rjtok6b@resize_w900_nl.webp', '2026-10-20 08:40:12', '2026-11-05 22:43:31', NULL),
('BRG-004', 'Spidol Snowman 12 Warna PW-12A', 'Alat Tulis', 'SNOWMAN', 'B2', 'BARANG', 'pcs', 16000.00, 18000.00, 40, 'Buat halaman kosong jadi penuh warna dengan Snowman Spidol 12 Warna PW-12A. Tinta cepat kering, tidak tembus kertas, dan ujung presisi—teman setia untuk mewarnai, menggambar, hingga catatan harian anak dan dewasa.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- 12 warna cerah yang merata, pas untuk kreativitas tanpa batas.\r\n\r\n- Tinta berbasis air: aman dan tidak berbau menyengat.\r\n\r\n- Ujung runcing presisi (fine tip) untuk garis rapi dan detail halus.\r\n\r\n- Nyaman digenggam, tutup rapat agar tinta tidak cepat kering.\r\n\r\n\r\n\r\nCocok Untuk\r\n\r\n- Mewarnai, menggambar, dan membuat catatan.\r\n\r\n- Tugas sekolah, hiasan tulisan, hingga kebutuhan kreatif sehari-hari.\r\n\r\n\r\n\r\nIsi & Ukuran\r\n\r\n- Jumlah isi: 1 set = 12 batang (12 warna lengkap).\r\n\r\n- Ukuran: Panjang ±14,5 cm, diameter ±1 cm.', NULL, 5, 'https://shopee.co.id/Spidol-Snowman-12-Warna-PW-12A-i.105190766.48519781419?extraParams=%7B%22display_model_id%22%3A366651586658%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134201-81ztc-mus5b9x95gjo03', 'Buat halaman kosong jadi penuh warna dengan Snowman Spidol 12 Warna PW-12A. Tinta cepat kering, tidak tembus kertas, dan ujung presisi—teman setia untuk mewarnai, menggambar, hingga catatan harian anak dan dewasa.\r\n\r\n\r\n\r\nKeunggulan Utama\r\n\r\n- 12 warna cerah yang merata, pas untuk kreativitas tanpa batas.\r\n\r\n- Tinta berbasis air: aman dan tidak berbau menyengat.\r\n\r\n- Ujung runcing presisi (fine tip) untuk garis rapi dan detail halus.\r\n\r\n- Nyaman digenggam, tutup rapat agar tinta tidak cepat kering.\r\n\r\n\r\n\r\nCocok Untuk\r\n\r\n- Mewarnai, menggambar, dan membuat catatan.\r\n\r\n- Tugas sekolah, hiasan tulisan, hingga kebutuhan kreatif sehari-hari.\r\n\r\n\r\n\r\nIsi & Ukuran\r\n\r\n- Jumlah isi: 1 set = 12 batang (12 warna lengkap).\r\n\r\n- Ukuran: Panjang ±14,5 cm, diameter ±1 cm.', 'https://down-id.img.susercontent.com/file/id-11134201-81ztc-mus5b9x95gjo03@resize_w900_nl.webp', '2026-10-20 08:40:12', '2026-11-05 22:41:03', NULL),
('BRG-005', 'Lem Tetes Korea G Lem Besi C Cair - Rekat Kuat, Serbaguna, Mudah Digunakan', 'Alat Tulis', '-', 'B3', 'BARANG', 'pcs', 12000.00, 15000.00, 8, 'Lem Tetes Korea G Lem Besi C Cair adalah solusi ideal untuk berbagai kebutuhan perekat. Dengan daya rekat yang kuat, lem ini cocok untuk merekatkan berbagai jenis barang. Mudah digunakan, lem ini hadir dalam bentuk tetes yang praktis dan dapat digunakan untuk berbagai aplikasi.\r\n\r\n\r\n\r\nKeunggulan Produk:\r\n\r\n- Daya rekat tinggi\r\n\r\n- Mudah digunakan\r\n\r\n- Serbaguna untuk berbagai keperluan\r\n\r\n\r\n\r\nInformasi Tambahan:\r\n\r\nLem ini tersedia dalam kemasan yang praktis dan siap digunakan kapan saja Anda membutuhkannya. Cocok untuk berbagai kebutuhan rumah tangga dan industri. Dengan Lem Tetes Korea G Lem Besi C Cair, pekerjaan perekat Anda menjadi lebih mudah dan efisien.', NULL, 5, 'https://shopee.co.id/Lem-Tetes-Korea-G-Lem-Besi-C-Cair-Rekat-Kuat-Serbaguna-Mudah-Digunakan-i.105190766.52269718084?extraParams=%7B%22display_model_id%22%3A421647759871%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81ztf-mum4q8nun18gf2', 'Lem Tetes Korea G Lem Besi C Cair adalah solusi ideal untuk berbagai kebutuhan perekat. Dengan daya rekat yang kuat, lem ini cocok untuk merekatkan berbagai jenis barang. Mudah digunakan, lem ini hadir dalam bentuk tetes yang praktis dan dapat digunakan untuk berbagai aplikasi.\r\n\r\n\r\n\r\nKeunggulan Produk:\r\n\r\n- Daya rekat tinggi\r\n\r\n- Mudah digunakan\r\n\r\n- Serbaguna untuk berbagai keperluan\r\n\r\n\r\n\r\nInformasi Tambahan:\r\n\r\nLem ini tersedia dalam kemasan yang praktis dan siap digunakan kapan saja Anda membutuhkannya. Cocok untuk berbagai kebutuhan rumah tangga dan industri. Dengan Lem Tetes Korea G Lem Besi C Cair, pekerjaan perekat Anda menjadi lebih mudah dan efisien.', 'https://down-id.img.susercontent.com/file/id-11134207-81ztf-mum4q8nun18gf2@resize_w900_nl.webp', '2026-10-20 08:40:12', '2026-11-05 22:44:18', NULL),
('BRG-006', 'Buku Kas Kwarto Soft Cover Kiky 4 Warna, 78 Lembar', 'Buku', 'Kiky', 'B3', 'BARANG', 'pcs', 10000.00, 12000.00, 2, 'Buku kas kwarto Kiky dengan soft cover untuk kebutuhan pencatatan yang rapi.\r\n\r\n\r\n\r\nDetail Produk\r\n\r\n- Varian warna: biru, merah, cokelat, dan oranye\r\n\r\n- Isi: 78 lembar', NULL, 5, 'https://shopee.co.id/Buku-Kas-Kwarto-Soft-Cover-Kiky-4-Warna-78-Lembar-i.105190766.27297022939?extraParams=%7B%22display_model_id%22%3A346688507723%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81zto-mv0defmngv0i42', 'Buku kas kwarto Kiky dengan soft cover untuk kebutuhan pencatatan yang rapi.\r\n\r\n\r\n\r\nDetail Produk\r\n\r\n- Varian warna: biru, merah, cokelat, dan oranye\r\n\r\n- Isi: 78 lembar', 'https://down-id.img.susercontent.com/file/id-11134207-81zto-mv0defmngv0i42@resize_w900_nl.webp', '2026-10-20 08:40:12', '2026-11-05 22:45:34', NULL),
('BRG-007', 'Buku Tulis Polos Tanpa Garis 38 lembar', 'Buku', 'Best One', 'A1', 'BARANG', 'pcs', 2500.00, 3500.00, 25, 'Ringkas, fungsional, dan rapi untuk catatan harian. Buku tulis polos tanpa garis ini siap menemani rutinitas belajar dan kerja—membuat setiap ide mengalir lancar tanpa ribet.\r\n\r\n\r\n\r\nKeunggulan Utama:\r\n\r\n- Kertas 75 gsm yang berkualitas, tidak tembus tinta saat ditulis.\r\n\r\n- Desain polos tanpa garis, nyaman untuk berbagai jenis catatan.\r\n\r\n- Isi 38 lembar per buku.\r\n\r\n\r\n\r\nCatatan Pembelian:\r\n\r\n- Sampul random sesuai stok yang tersedia.\r\n\r\n- Jika ingin motif tertentu, silakan chat terlebih dahulu.\r\n\r\n\r\n\r\nDetail Produk:\r\n\r\n- Merek: Best One\r\n\r\n- Bahan kertas: 75 gsm', NULL, 5, 'https://shopee.co.id/Buku-Tulis-Polos-Tanpa-Garis-38-lembar-i.105190766.58169363821?extraParams=%7B%22display_model_id%22%3A316623626915%2C%22model_selection_logic%22%3A3%7D&image_ids=id-11134207-81ztc-muea2k5nrjt029', 'Ringkas, fungsional, dan rapi untuk catatan harian. Buku tulis polos tanpa garis ini siap menemani rutinitas belajar dan kerja—membuat setiap ide mengalir lancar tanpa ribet.\r\n\r\n\r\n\r\nKeunggulan Utama:\r\n\r\n- Kertas 75 gsm yang berkualitas, tidak tembus tinta saat ditulis.\r\n\r\n- Desain polos tanpa garis, nyaman untuk berbagai jenis catatan.\r\n\r\n- Isi 38 lembar per buku.\r\n\r\n\r\n\r\nCatatan Pembelian:\r\n\r\n- Sampul random sesuai stok yang tersedia.\r\n\r\n- Jika ingin motif tertentu, silakan chat terlebih dahulu.\r\n\r\n\r\n\r\nDetail Produk:\r\n\r\n- Merek: Best One\r\n\r\n- Bahan kertas: 75 gsm', 'https://down-id.img.susercontent.com/file/id-11134207-81ztc-muea2k5nrjt029@resize_w900_nl.webp', '2026-10-20 08:40:12', '2026-11-05 22:46:17', NULL),
('BRG-009', 'Lakban Bening', 'Perlengkapan Kantor', 'Daimaru', 'B2', NULL, 'pcs', 6000.00, 8000.00, 11, NULL, NULL, 5, NULL, NULL, NULL, '2026-10-20 08:40:12', '2026-11-08 23:31:59', '2026-11-08 16:31:59'),
('BRG-010', 'Spidol Snowman Hitam', 'Alat Tulis', 'Snowman', 'A3', NULL, 'pcs', 5500.00, 7500.00, 18, NULL, NULL, 5, NULL, NULL, NULL, '2026-10-20 08:40:12', '2026-11-08 23:32:15', '2026-11-08 16:32:15'),
('BRG-011', 'Amplop Coklat', 'Kertas', '-', 'B1', NULL, 'pcs', 600.00, 1000.00, 199, NULL, NULL, 5, NULL, NULL, NULL, '2026-10-20 08:40:12', '2026-11-08 23:32:05', '2026-11-08 16:32:05'),
('BRG-012', 'Jasa Fotocopy A4 (per lembar)', 'Jasa Fotocopy', '-', '-', 'JASA', 'lembar', 150.00, 300.00, 999999, NULL, '/images/barang/BRG-012.jpg', 5, NULL, NULL, '/images/barang/BRG-012.jpg', '2026-10-20 08:40:12', '2026-11-08 21:58:05', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daftar_item`
--
ALTER TABLE `daftar_item`
  ADD PRIMARY KEY (`kode_item`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
