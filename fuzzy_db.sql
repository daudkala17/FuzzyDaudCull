-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 07:40 AM
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
-- Database: `fuzzy_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `fuzzifikasi`
--

CREATE TABLE `fuzzifikasi` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `test_tulis` float DEFAULT NULL,
  `test_keterampilan` float DEFAULT NULL,
  `wawancara` float DEFAULT NULL,
  `test_kesehatan` float DEFAULT NULL,
  `nilai_pakar` float DEFAULT NULL,
  `hasil_sistem` float DEFAULT NULL,
  `tanggal_input` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuzzifikasi`
--

INSERT INTO `fuzzifikasi` (`id`, `nama`, `test_tulis`, `test_keterampilan`, `wawancara`, `test_kesehatan`, `nilai_pakar`, `hasil_sistem`, `tanggal_input`) VALUES
(2048, 'A', 74, 75, 80, 70, 75, 73, '2025-06-19 01:46:59'),
(2049, 'B', 56, 65, 75, 75, 70, 47, '2025-06-19 01:47:27'),
(2050, 'C', 65, 63, 70, 80, 71, 55, '2025-06-19 01:47:44'),
(2051, 'D', 45, 55, 90, 80, 73, 48, '2025-06-19 01:47:59'),
(2052, 'E', 60, 64, 80, 80, 74, 49, '2025-06-19 01:48:24'),
(2053, 'F', 58, 55, 90, 40, 62, 53, '2025-06-19 01:48:43'),
(2054, 'G', 75, 75, 60, 60, 65, 50, '2025-06-19 01:49:00'),
(2055, 'H', 72, 75, 60, 60, 65, 52, '2025-06-19 01:49:32'),
(2056, 'I', 71, 75, 75, 70, 73, 68, '2025-06-19 01:49:52'),
(2057, 'J', 100, 80, 75, 80, 82, 75, '2025-06-19 01:50:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fuzzifikasi`
--
ALTER TABLE `fuzzifikasi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fuzzifikasi`
--
ALTER TABLE `fuzzifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2060;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
