-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 11:52 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `menu` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `menu`, `harga`, `kategori`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'Es Coklat', 8000, 'minuman', 'es coklat.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(2, 'Es Jeruk', 8000, 'minuman', 'es jeruk.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(3, 'Es Teh', 5000, 'minuman', 'es teh.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(4, 'Tea Jus', 3000, 'minuman', 'teajus.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(5, 'Jas Jus', 3000, 'minuman', 'jasjus.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(6, 'Cireng Kuah Seblak', 12000, 'makanan', 'cireng kuah seblak.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(7, 'Seblak Enoki', 13000, 'makanan', 'seblak enoki.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(8, 'Seblak Tulang', 15000, 'makanan', 'seblak tulang.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(9, 'Seblak Ceker', 15000, 'makanan', 'seblak ceker.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(10, 'Seblak Seafood', 17000, 'makanan', 'seblak seafood.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(11, 'Seblak Original', 5000, 'makanan', 'seblak original.jpg', '2025-09-28 23:32:48', '2025-09-28 23:32:48'),
(12, 'daun', 5000, '', '1762361431_daun.jpg', '2025-11-05 23:50:31', '2025-11-05 23:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `menu_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `menu_name`, `is_active`) VALUES
(1, 'Transaksi Baru', 1),
(2, 'Data Produk', 1),
(3, 'Laporan Penjualan', 1),
(4, 'Karyawan', 1),
(5, 'Pengaturan', 1),
(6, 'Bantuan', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rinci_jual`
--

CREATE TABLE `rinci_jual` (
  `id_rinci_jual` int(11) NOT NULL,
  `nomor_faktur` varchar(50) NOT NULL,
  `kode_produk` varchar(50) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `harga_modal` decimal(10,2) NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `untung` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rinci_jual`
--

INSERT INTO `rinci_jual` (`id_rinci_jual`, `nomor_faktur`, `kode_produk`, `nama_produk`, `harga_modal`, `harga_jual`, `qty`, `total_harga`, `untung`) VALUES
(12, 'INV20251022121921932', '2', 'jus jeruk', 0.00, 6000.00, 1, 6000.00, 6000.00),
(13, 'INV20251022121921932', '3', 'seblak ', 0.00, 11000.00, 1, 11000.00, 11000.00),
(14, 'INV20251022131956362', '1', 'mie ayam', 0.00, 11500.00, 1, 11500.00, 11500.00),
(15, 'INV20251022131956362', '2', 'jus jeruk', 0.00, 6000.00, 3, 18000.00, 18000.00),
(16, 'INV20251022131956362', '3', 'seblak ', 0.00, 11000.00, 1, 11000.00, 11000.00),
(17, 'INV20251022141054272', '2', 'jus jeruk', 0.00, 6000.00, 1, 6000.00, 6000.00),
(18, 'INV20251022141054272', '4', 'jus durian', 0.00, 12500.00, 1, 12500.00, 12500.00),
(19, 'INV20251022141054272', '5', 'kebab', 0.00, 8000.00, 1, 8000.00, 8000.00),
(20, 'INV20251022141054272', '6', 'takoyaki', 0.00, 6000.00, 1, 6000.00, 6000.00),
(21, 'INV20251023000836734', '4', 'jus durian', 0.00, 12500.00, 1, 12500.00, 12500.00),
(22, 'INV20251023000836734', '6', 'takoyaki', 0.00, 6000.00, 1, 6000.00, 6000.00),
(23, 'INV20251023001121871', '2', 'jus jeruk', 0.00, 6000.00, 1, 6000.00, 6000.00),
(24, 'INV20251023001121871', '5', 'kebab', 0.00, 8000.00, 1, 8000.00, 8000.00),
(25, 'INV20251105173325805', '3', 'Es Teh', 0.00, 5000.00, 1, 5000.00, 5000.00),
(26, 'INV20251105173325805', '6', 'Cireng Kuah Seblak', 0.00, 12000.00, 1, 12000.00, 12000.00),
(28, 'FAK20251105173325806', 'P007', 'Seblak Enoki', 0.00, 13000.00, 1, 13000.00, 13000.00),
(29, 'INV20251111102633909', '6', 'Cireng Kuah Seblak', 0.00, 12000.00, 1, 12000.00, 12000.00),
(30, 'INV20251111111904890', '3', 'Es Teh', 0.00, 5000.00, 2, 10000.00, 10000.00),
(31, 'INV20251111112320532', '2', 'Es Jeruk', 0.00, 8000.00, 1, 8000.00, 8000.00),
(32, 'INV20251111112320532', '11', 'Seblak Original', 0.00, 5000.00, 1, 5000.00, 5000.00),
(35, 'INV20251111113115172', '11', 'Seblak Original', 0.00, 5000.00, 1, 5000.00, 5000.00),
(38, 'INV20251112203310329', '3', 'Es Teh', 0.00, 5000.00, 1, 5000.00, 5000.00),
(39, 'INV20251112203310329', '11', 'Seblak Original', 0.00, 5000.00, 1, 5000.00, 5000.00),
(40, 'INV20251112204140843', '4', 'Tea Jus', 0.00, 3000.00, 1, 3000.00, 3000.00),
(41, 'INV20251112204140843', '9', 'Seblak Ceker', 0.00, 15000.00, 1, 15000.00, 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tb_jual`
--

CREATE TABLE `tb_jual` (
  `id_jual` int(11) NOT NULL,
  `nomor_faktur` varchar(50) NOT NULL,
  `tanggal_beli` datetime NOT NULL DEFAULT current_timestamp(),
  `total_belanja` int(11) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `kembalian` int(11) NOT NULL,
  `metode_bayar` varchar(20) NOT NULL DEFAULT 'Tunai',
  `ewallet` varchar(50) DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_jual`
--

INSERT INTO `tb_jual` (`id_jual`, `nomor_faktur`, `tanggal_beli`, `total_belanja`, `total_bayar`, `kembalian`, `metode_bayar`, `ewallet`) VALUES
(17, 'INV20251022121921932', '2025-10-22 17:19:21', 17000, 20000, 3000, 'Tunai', '-'),
(18, 'INV20251022131956362', '2025-10-22 18:19:56', 40500, 50000, 9500, 'Tunai', '-'),
(19, 'INV20251022141054272', '2025-10-22 19:10:54', 32500, 50000, 17500, 'Tunai', '-'),
(20, 'INV20251023000836734', '2025-10-23 05:08:36', 18500, 20000, 1500, 'Tunai', '-'),
(21, 'INV20251023001121871', '2025-10-23 05:11:21', 14000, 15000, 1000, 'Tunai', '-'),
(22, 'INV20251105173325805', '2025-11-05 23:33:25', 17000, 18000, 1000, 'Tunai', '-'),
(23, 'FAK20251105173325806', '2025-11-09 22:43:45', 12000, 12000, 0, 'Tunai', '-'),
(26, 'INV20251111102633909', '2025-11-11 16:26:33', 12000, 20000, 8000, 'Tunai', '-'),
(27, 'INV20251111111904890', '2025-11-11 17:19:04', 10000, 10000, 0, 'E-Wallet', 'Dana'),
(28, 'INV20251111112320532', '2025-11-11 17:23:20', 13000, 20000, 7000, 'E-Wallet', 'Dana'),
(29, 'FAK20251111112320533', '2025-11-11 17:29:58', 20000, 20000, 0, 'Tunai', '-'),
(30, 'INV20251111113115172', '2025-11-11 17:31:15', 5000, 10000, 5000, 'E-Wallet', 'Dana'),
(31, 'FAK20251111113115173', '2025-11-13 02:10:42', 20000, 20000, 0, 'Tunai', '-'),
(32, 'INV20251112203310329', '2025-11-13 02:33:10', 10000, 20000, 10000, 'Tunai', ''),
(33, 'INV20251112204140843', '2025-11-13 02:41:40', 18000, 20000, 2000, 'E-Wallet', 'Dana');

-- --------------------------------------------------------

--
-- Table structure for table `tb_produk`
--

CREATE TABLE `tb_produk` (
  `id_produk` int(11) NOT NULL,
  `kode_produk` varchar(50) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` enum('makanan','minuman','cemilan') NOT NULL,
  `harga_beli` decimal(10,0) NOT NULL,
  `harga_jual` decimal(10,0) NOT NULL,
  `stok` int(11) NOT NULL,
  `satuan` enum('pcs','paket') NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_produk`
--

INSERT INTO `tb_produk` (`id_produk`, `kode_produk`, `nama_produk`, `kategori`, `harga_beli`, `harga_jual`, `stok`, `satuan`, `foto`) VALUES
(1, 'P001', 'Es Coklat', 'minuman', 6000, 8000, 100, 'pcs', 'es coklat.jpg'),
(2, 'P002', 'Es Jeruk', 'minuman', 6000, 8000, 100, 'pcs', 'es jeruk.jpg'),
(3, 'P003', 'Es Teh', 'minuman', 3000, 5000, 100, 'pcs', 'es teh.jpg'),
(4, 'P004', 'Tea Jus', 'minuman', 2000, 3000, 100, 'pcs', 'teajus.jpg'),
(5, 'P005', 'Jas Jus', 'minuman', 2000, 3000, 100, 'pcs', 'jasjus.jpg'),
(6, 'P006', 'Cireng Kuah Seblak', 'makanan', 10000, 12000, 100, 'pcs', 'cireng kuah seblak.jpg'),
(7, 'P007', 'Seblak Enoki', 'makanan', 11000, 13000, 100, 'pcs', 'seblak enoki.jpg'),
(8, 'P008', 'Seblak Tulang', 'makanan', 13000, 15000, 100, 'pcs', 'seblak tulang.jpg'),
(9, 'P009', 'Seblak Ceker', 'makanan', 13000, 15000, 100, 'pcs', 'seblak ceker.jpg'),
(10, 'P010', 'Seblak Seafood', 'makanan', 15000, 17000, 100, 'pcs', 'seblak seafood.jpg'),
(11, 'P011', 'Seblak Original', 'makanan', 3000, 5000, 100, 'pcs', 'seblak original.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'khalwa29@gmail.com', 'awaaaaa@gmail.com', '$2y$10$rt1UZR38JJv32Qb7PVLVZeN2emaCot2RwyUxCfgZlTKdQJZzEC2oi', '2025-10-21 10:55:02'),
(2, 'awa', 'awaaaaaa@gmail.com', '$2y$10$EAo48Qw.1XARh2iQhsRJ3uNBSy0jpqcsN/gzNEWHiC6Fa89M5h8nG', '2025-10-21 11:14:01'),
(3, 'khalwaa', 'inikhalwaa@gmail.com', '$2y$10$6WAihuKuL/aa7DmA9uRfGOaGYamwXf17zQ.pY.b3cxocI9vvUtAfa', '2025-10-21 11:18:35'),
(4, 'awa', '29khalwaaa@gmail.com', '$2y$10$fil9aWzYIIxKu6eO.45WK.94jE8vawfSXR2tTp.zYCCEOJEHbk6/G', '2025-10-21 11:31:06'),
(5, 'khalwa', '123khalwa@gmail.com', '$2y$10$cVWFt63XWWcmEiFe6AciJ.JJunhmOJKeFeIZNARxwqns.HN6FcVvy', '2025-10-21 11:42:09'),
(6, 'khalwa', 'khalwa@gmail.com', '$2y$10$GEjp3yVRdNYkNfpGk2kXkeDwTI2/ZvbVXMd4oBmMjOnqdxTotgQLi', '2025-10-21 11:50:48'),
(7, 'khalwa', 'sitikhalwarakhmawati@gmail.com', '$2y$10$W5B.W0XfiErEOLWDicHd0.EsR1rhCUgu84GZ0PbAQ8dHpGoqALcjm', '2025-10-21 11:56:28'),
(8, 'awa', 'awa@gmail.com', '$2y$10$Qxc46BWqCU69q7qd4a67oudNa15VkMGgtRtk2ZAY85E3.DD3MpjrK', '2025-10-21 11:58:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rinci_jual`
--
ALTER TABLE `rinci_jual`
  ADD PRIMARY KEY (`id_rinci_jual`),
  ADD KEY `nomor_faktur` (`nomor_faktur`);

--
-- Indexes for table `tb_jual`
--
ALTER TABLE `tb_jual`
  ADD PRIMARY KEY (`id_jual`),
  ADD UNIQUE KEY `nomor_faktur` (`nomor_faktur`);

--
-- Indexes for table `tb_produk`
--
ALTER TABLE `tb_produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rinci_jual`
--
ALTER TABLE `rinci_jual`
  MODIFY `id_rinci_jual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tb_jual`
--
ALTER TABLE `tb_jual`
  MODIFY `id_jual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tb_produk`
--
ALTER TABLE `tb_produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rinci_jual`
--
ALTER TABLE `rinci_jual`
  ADD CONSTRAINT `rinci_jual_ibfk_1` FOREIGN KEY (`nomor_faktur`) REFERENCES `tb_jual` (`nomor_faktur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
