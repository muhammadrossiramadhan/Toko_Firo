-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 08, 2026 at 11:06 PM
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
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('ADMIN','KASIR') NOT NULL,
  `status` enum('AKTIF','NONAKTIF') DEFAULT 'AKTIF',
  `telegram_chat_id` varchar(50) DEFAULT NULL,
  `login_terakhir` datetime DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `username`, `password_hash`, `role`, `status`, `telegram_chat_id`, `login_terakhir`, `dibuat_pada`, `diubah_pada`) VALUES
(1, 'Rossi', 'admin', '$2y$12$BSX/FWAh2gBzDI1yrN56K.eywMd5GgjANAcJ7UN4VN1Su6fBssqhK', 'ADMIN', 'AKTIF', NULL, '2026-10-19 02:36:47', '2026-10-16 15:56:54', '2026-10-28 04:09:26'),
(2, 'Junaidi', 'junaidi', '$2y$12$V5PmkqQ6kbflTJXPnF7PNuD8bP3a4rMQLoAVCWI07RAlKp7kyxdPi', 'ADMIN', 'NONAKTIF', NULL, NULL, '2026-10-20 08:40:12', '2026-11-08 21:53:07'),
(3, 'Rina', 'rina', '$2y$12$9ryKLpywqdK.bRgqfNdT4.FKw0/oMcZYrwO2V8A245FSaC.3Ku5Z6', 'KASIR', 'AKTIF', NULL, NULL, '2026-10-20 08:40:12', '2026-10-27 01:52:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_pengguna_role_status` (`role`,`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
