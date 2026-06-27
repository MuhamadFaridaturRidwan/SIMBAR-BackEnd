-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 07:27 AM
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
-- Database: `db_inventori`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `supplier` varchar(100) DEFAULT 'PT Logistik Internal',
  `kategori` varchar(50) NOT NULL,
  `lokasi` varchar(100) DEFAULT '-',
  `stok_saat_ini` int(11) DEFAULT 0,
  `stok_min` int(11) DEFAULT 5,
  `harga_satuan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `kode_barang`, `nama_barang`, `supplier`, `kategori`, `lokasi`, `stok_saat_ini`, `stok_min`, `harga_satuan`) VALUES
(1, 'SAF-007', 'Safety Vest Reflektif', 'PT Safety Indo', 'Safety', 'Gudang C - Storage', 25, 5, 65000),
(2, 'STR-003', 'Stretch Film 50cm', 'PT Packaging Berkah', 'Packaging', 'Gudang B - Equipment', 0, 3, 95000),
(3, 'BOX-002', 'Kardus Besar 60x40x40', 'PT Kardus Box', 'Packaging', 'Gudang A', 5, 10, 75000),
(4, 'LBL-005', 'Label Barcode Roll', 'PT Print Maju', 'Office Supplies', 'Gudang B', 2, 5, 45000),
(5, 'BAT-004', 'Forklift Battery 48V', 'PT Battery Solutions', 'Equipment', 'Gudang C - Storage', 12, 5, 12000000),
(6, 'HPT-006', 'Hand Pallet Truck', 'PT Logistics Tools', 'Equipment', 'Gudang B - Equipment', 8, 3, 8000000),
(7, 'PLT-001', 'Pallet Kayu Standar', 'PT Logistik Internal', 'Equipment', 'Gudang B', 100, 10, 125000),
(8, 'HLM-0112', 'Helm Fanzhi Terkuat', 'PT FARID SEJAHTERA', 'Safety', 'Gudang A Rak-2', 1, 5, 499999);

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id_keluar` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tanggal_keluar` datetime NOT NULL,
  `jumlah` int(11) NOT NULL,
  `referensi` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `oleh` varchar(50) DEFAULT 'Admin User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang_keluar`
--

INSERT INTO `barang_keluar` (`id_keluar`, `id_barang`, `tanggal_keluar`, `jumlah`, `referensi`, `catatan`, `oleh`) VALUES
(1, 3, '2026-04-22 14:15:00', 50, 'DO-2026-045', 'Pengiriman ke customer PT ABC', 'Admin User'),
(2, 8, '2026-04-28 15:00:00', 4, 'INV-2026-188', 'Manonjaya', 'Admin User'),
(3, 8, '2026-04-28 15:02:00', 5, 'INV-2026-188', 'Pengiriman ke Manonjaya', 'Admin User');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id_masuk` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `jumlah` int(11) NOT NULL,
  `referensi` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `oleh` varchar(50) DEFAULT 'Admin User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang_masuk`
--

INSERT INTO `barang_masuk` (`id_masuk`, `id_barang`, `tanggal_masuk`, `jumlah`, `referensi`, `catatan`, `oleh`) VALUES
(1, 7, '2026-04-20 10:00:00', 100, 'PO-2026-001', 'Pembelian rutin bulanan', 'Admin User'),
(2, 4, '2026-04-21 11:30:00', 200, 'PO-2026-002', 'Stock replenishment', 'Admin User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id_keluar`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id_masuk`),
  ADD KEY `id_barang` (`id_barang`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id_masuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
