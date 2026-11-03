-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2026 at 04:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_desinfektan`
--

-- --------------------------------------------------------

--
-- Table structure for table `aktivitas`
--

CREATE TABLE `aktivitas` (
  `id` bigint(20) NOT NULL,
  `jenis` enum('info','success','trigger','danger') DEFAULT 'info',
  `pesan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `aktivitas`
--

INSERT INTO `aktivitas` (`id`, `jenis`, `pesan`, `created_at`) VALUES
(1, 'trigger', 'Penyemprotan otomatis berhasil. Jarak objek: 10 cm.', '2026-11-03 03:06:59'),
(2, 'success', 'Penyemprotan manual berhasil dijalankan.', '2026-11-03 03:35:52'),
(3, 'success', 'Penyemprotan manual berhasil dijalankan.', '2026-11-03 03:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `batas_deteksi_cm` int(11) DEFAULT 15,
  `durasi_semprot_ms` int(11) DEFAULT 1500,
  `jeda_ms` int(11) DEFAULT 3000,
  `level_cairan` decimal(5,2) DEFAULT 78.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `batas_deteksi_cm`, `durasi_semprot_ms`, `jeda_ms`, `level_cairan`, `updated_at`) VALUES
(1, 15, 1500, 3000, 75.00, '2026-11-03 03:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `penyemprotan`
--

CREATE TABLE `penyemprotan` (
  `id` bigint(20) NOT NULL,
  `mode` enum('otomatis','manual') NOT NULL,
  `jarak_cm` decimal(8,2) DEFAULT NULL,
  `durasi_ms` int(11) NOT NULL,
  `status` enum('berhasil','gagal') DEFAULT 'berhasil',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penyemprotan`
--

INSERT INTO `penyemprotan` (`id`, `mode`, `jarak_cm`, `durasi_ms`, `status`, `created_at`) VALUES
(1, 'otomatis', 10.00, 1500, 'berhasil', '2026-11-03 03:06:58'),
(2, 'manual', 0.00, 1500, 'berhasil', '2026-11-03 03:35:52'),
(3, 'manual', 0.00, 1500, 'berhasil', '2026-11-03 03:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `sensor_data`
--

CREATE TABLE `sensor_data` (
  `id` bigint(20) NOT NULL,
  `jarak_cm` decimal(8,2) NOT NULL,
  `objek_terdeteksi` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sensor_data`
--

INSERT INTO `sensor_data` (`id`, `jarak_cm`, `objek_terdeteksi`, `created_at`) VALUES
(1, 10.00, 1, '2026-11-03 03:06:58'),
(2, 15.00, 1, '2026-11-03 03:35:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$Fqu9TirvblqVXvcTcS4AOu53K38ZPZnwdaTx5bJsbGZAljAHWnARi', 'admin', '2026-11-03 03:11:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penyemprotan`
--
ALTER TABLE `penyemprotan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sensor_data`
--
ALTER TABLE `sensor_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aktivitas`
--
ALTER TABLE `aktivitas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penyemprotan`
--
ALTER TABLE `penyemprotan`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sensor_data`
--
ALTER TABLE `sensor_data`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
