-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 04:10 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rekappoin`
--

-- --------------------------------------------------------

--
-- Table structure for table `kelompok`
--

CREATE TABLE `kelompok` (
  `id_kelompok` int(11) NOT NULL,
  `nama_kelompok` varchar(255) NOT NULL,
  `epic_kelompok` varchar(255) NOT NULL,
  `isdelete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kelompok`
--

INSERT INTO `kelompok` (`id_kelompok`, `nama_kelompok`, `epic_kelompok`, `isdelete`) VALUES
(4, 'CHILDREN OF CHRIST', 'Epic 1', 0),
(5, 'SPONTAN UHUY', 'Epic 1', 0),
(6, 'GODS PLAN', 'Epic 1', 0),
(7, 'MARVEL', 'Epic 2', 0),
(8, 'PEACEFUL', 'Epic 2', 0),
(9, 'MEWING WITH RIZZ', 'Epic 2', 0),
(10, 'TEAM OF GOD', 'Epic 2', 0),
(11, 'SIGMA', 'Epic 2', 0),
(12, 'HIDUP DALAM KRISTUS', 'Epic 2', 0),
(13, 'LASKAR KRISTUS', 'Epic 2', 0),
(14, 'FIVE TEENS', 'Epic 2', 0),
(16, 'CHEMISTRY', 'Epic 3', 0),
(17, 'SYMPHONY', 'Epic 3', 0),
(18, 'BANTENG MERAH', 'Epic 3', 0),
(19, 'MANUT GUSTI', 'Epic 3', 0),
(20, 'KAKGEM', 'Epic 4', 0),
(21, 'KEMASAGI', 'Epic 4', 0),
(22, 'UNO', 'Epic 5', 0),
(23, 'GODS PLAN', 'Epic 1', 1),
(24, 'Team Of God', 'Epic 2', 1),
(25, 'CHILDREN OF CHRIST', 'Epic 1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `poinrekap`
--

CREATE TABLE `poinrekap` (
  `id_poinrekap` int(11) NOT NULL,
  `id_rekap` int(11) NOT NULL,
  `id_kelompok` int(11) NOT NULL,
  `keaktifan` int(11) NOT NULL,
  `medsos` int(11) NOT NULL,
  `sate` int(11) NOT NULL,
  `jiwa_baru` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `isdelete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `poinrekap`
--

INSERT INTO `poinrekap` (`id_poinrekap`, `id_rekap`, `id_kelompok`, `keaktifan`, `medsos`, `sate`, `jiwa_baru`, `total`, `isdelete`) VALUES
(145, 12, 4, 0, 0, 0, 0, 0, 0),
(146, 12, 5, 0, 0, 0, 0, 0, 0),
(147, 12, 6, 0, 0, 0, 0, 0, 0),
(148, 12, 7, 0, 0, 0, 0, 0, 0),
(149, 12, 8, 0, 0, 0, 0, 0, 0),
(150, 12, 9, 0, 0, 0, 0, 0, 0),
(151, 12, 10, 0, 0, 0, 0, 0, 0),
(152, 12, 11, 0, 0, 0, 0, 0, 0),
(153, 12, 12, 0, 0, 0, 0, 0, 0),
(154, 12, 13, 0, 0, 0, 0, 0, 0),
(155, 12, 14, 0, 0, 0, 0, 0, 0),
(156, 12, 16, 0, 0, 0, 0, 0, 0),
(157, 12, 17, 0, 0, 0, 0, 0, 0),
(158, 12, 18, 0, 0, 0, 0, 0, 0),
(159, 12, 19, 0, 0, 0, 0, 0, 0),
(160, 12, 20, 0, 0, 0, 0, 0, 0),
(161, 12, 21, 0, 0, 0, 0, 0, 0),
(162, 12, 22, 0, 0, 0, 0, 0, 0),
(163, 13, 4, 0, 0, 0, 0, 0, 0),
(164, 13, 5, 0, 0, 0, 0, 0, 0),
(165, 13, 6, 0, 0, 0, 0, 0, 0),
(166, 13, 7, 0, 0, 0, 0, 0, 0),
(167, 13, 8, 0, 0, 0, 0, 0, 0),
(168, 13, 9, 0, 0, 0, 0, 0, 0),
(169, 13, 10, 0, 0, 0, 0, 0, 0),
(170, 13, 11, 0, 0, 0, 0, 0, 0),
(171, 13, 12, 0, 0, 0, 0, 0, 0),
(172, 13, 13, 0, 0, 0, 0, 0, 0),
(173, 13, 14, 0, 0, 0, 0, 0, 0),
(174, 13, 16, 0, 0, 0, 0, 0, 0),
(175, 13, 17, 0, 0, 0, 0, 0, 0),
(176, 13, 18, 0, 0, 0, 0, 0, 0),
(177, 13, 19, 0, 0, 0, 0, 0, 0),
(178, 13, 20, 0, 0, 0, 0, 0, 0),
(179, 13, 21, 0, 0, 0, 0, 0, 0),
(180, 13, 22, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rekap`
--

CREATE TABLE `rekap` (
  `id_rekap` int(11) NOT NULL,
  `nama_rekap` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `isdelete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rekap`
--

INSERT INTO `rekap` (`id_rekap`, `nama_rekap`, `due_date`, `isdelete`) VALUES
(12, '27 Desember - 2 Januari', '2025-01-02', 0),
(13, '3 Januari - 9 Januari', '2025-01-09', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_kelompok` varchar(255) NOT NULL,
  `previlage` varchar(255) NOT NULL,
  `isdelete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_kelompok`, `previlage`, `isdelete`) VALUES
(3, 'Nancy Stefany', '$2y$10$2CI4uIS2hpo0BiPgFzSwKO0hIsccNdEkGbbe9Wo8DGAvQaL1AoWc.', 'CHILDREN OF CHRIST', 'admin', 0),
(4, 'Jessica Aurelia', '$2y$10$YVYIMq6OQAo51gg3YMq7Ruf.vd.AxBlY97ud0Xm2RSrFZeDW0fatG', 'SPONTAN UHUY', 'admin', 0),
(5, 'Chakrabwana Wibawa⁩', '$2y$10$M4Liv77tWrB/YEzNofxSTuGtZACKjYrRqLNJVCC2HVu8jUULqG6gu', 'GODS PLAN', 'user', 0),
(6, 'Riky Satya', '$2y$10$1LcvOyOhedC.pmndVJ//Uu2XJRiMx2zNspjqmCaJ.Q9b.u6uA4ylC', 'MARVEL', 'user', 0),
(7, 'Louise Fievel', '$2y$10$aWeelHM8WLstNwUREFprreOXa/SE5WK0bSSO1wsJDFjbP9FhWZp2O', 'PEACEFUL', 'user', 0),
(8, 'Richelle Audrine', '$2y$10$QR.xWUv.F1LIJYnMxk3sbeNvva3TMTBQlVhRExlC5i9OjJiUp/vHO', 'MEWING WITH RIZZ', 'user', 0),
(9, 'David Liem', '$2y$10$vNjiFqq5pnL7BPntQW8YoeNFroEqcjQwy7YYUuvMMbANCX8Ivznxm', 'TEAM OF GOD', 'admin', 0),
(10, 'Reyhan Mahardika', '$2y$10$YJD3G9kKTIit0SfeweQkVO6kilW2QXKZGA5MIErzDqGUSMkNceNMS', 'SIGMA', 'user', 0),
(11, 'Joenathan Noel H', '$2y$10$u/h2cJRF2FCRlBZQLQDZb.ZfkZhkgrFd2q8/2hakydZA5/GCScJ2u', 'HIDUP DALAM KRISTUS', 'user', 0),
(12, 'Rara Arvina', '$2y$10$33tubhaUkGgfT9oPwjWrPOmu19pd93oiIUpsNjk4oQi3TxqIGXWZ.', 'LASKAR KRISTUS', 'user', 0),
(13, 'Aurell Otiva', '$2y$10$KvIVTPFyY9VrSaJBVDEvt.lJVVUs5BedWARKRsBIWsBh5dNKn9udW', 'FIVE TEENS', 'user', 0),
(14, 'Paula Yuni', '$2y$10$Ct72EwJs5Q.hGorPaFJeiOPYv4P.V0E8Y3tSAG3Y2yUY05G8iRqxu', 'CHEMISTRY', 'user', 0),
(15, 'Gabriela Jasver', '$2y$10$wzHd4JznOUQKkPcbqYg0vOP0a3XSbc4l8yhF2/tMI.r7NtvKsKmwy', 'SYMPHONY', 'user', 0),
(16, 'Hanugra Christmanuel', '$2y$10$sC4OC7kjrkupvlx16B2mmuvXAb.DYo.icc.0.f3Ok2GjvJdUsrumq', 'BANTENG MERAH', 'admin', 0),
(17, 'Davino Mikha', '$2y$10$cWpSHDUd.2DNqGqECp5hSOarHiAp6gXmZkXfQjbYuk6yXSnFxSTCu', 'BANTENG MERAH', 'user', 0),
(18, 'Roby Tegar Sugandhi', '$2y$10$0CH1OwlCJ6OcKSd7.kaF1egSY5Q1L6k6cfQe66PlWJkIaaGYCIz62', 'MANUT GUSTI', 'user', 0),
(19, 'Ariel Setiawan', '$2y$10$o6FyQFiZpHCDyRqz04X43OmF7juftXzGTFU80H1N1sY9/p3cV9X1O', 'MANUT GUSTI', 'user', 0),
(20, 'Emilia Atika', '$2y$10$fWlRDQ5bh4iy6C9br1A8FegMbpvifT/gMeT3Qezl8HCzzYpXwp0pa', 'KAKGEM', 'user', 0),
(21, 'Otniel', '$2y$10$wZIa25DqgcKpPiT.CEFw6.0qbbqeIFcP89YohH1H/7S8U7D4yOreC', 'KEMASAGI', 'user', 0),
(22, 'Ale Kennard', '$2y$10$.dIrlyMIhkkomQN.Y.uvEO/EY2EtBR5yQMoAR8oAO2kfXHbaTpdsa', 'UNO', 'medsos', 0),
(23, 'Nancy Stefani', '$2y$10$/EAlygkA/VV7NKVMZZbZTOVU2WOL17SwP2Kc6NPWIbPc/NyytVAc.', 'CHILDREN OF CHRIST', 'admin', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kelompok`
--
ALTER TABLE `kelompok`
  ADD PRIMARY KEY (`id_kelompok`);

--
-- Indexes for table `poinrekap`
--
ALTER TABLE `poinrekap`
  ADD PRIMARY KEY (`id_poinrekap`);

--
-- Indexes for table `rekap`
--
ALTER TABLE `rekap`
  ADD PRIMARY KEY (`id_rekap`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kelompok`
--
ALTER TABLE `kelompok`
  MODIFY `id_kelompok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `poinrekap`
--
ALTER TABLE `poinrekap`
  MODIFY `id_poinrekap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `rekap`
--
ALTER TABLE `rekap`
  MODIFY `id_rekap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
