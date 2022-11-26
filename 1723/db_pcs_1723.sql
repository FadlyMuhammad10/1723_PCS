-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Nov 2022 pada 04.43
-- Versi server: 10.4.21-MariaDB
-- Versi PHP: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pcs_1723`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `nama`) VALUES
(43, 'dondon@amikom.ac.id', '827ccb0eea8a706c4c34a16891f84e7b', 'donni'),
(44, 'dondon@amikom.ac.id', '827ccb0eea8a706c4c34a16891f84e7b', 'donni'),
(45, 'halo@a.com', '827ccb0eea8a706c4c34a16891f84e7b', 'donni'),
(46, 'halo@a.com', '827ccb0eea8a706c4c34a16891f84e7b', 'donni'),
(47, 'dodi@amikom.ac.id', '827ccb0eea8a706c4c34a16891f84e7b', 'Dodi'),
(48, 'dodi@', '827ccb0eea8a706c4c34a16891f84e7b', 'Dodi'),
(49, 'don49@amikom.ac.id', '827ccb0eea8a706c4c34a16891f84e7b', 'Dodi'),
(57, 'xyz5swsw@amikom.ac.id', 'ae8b5aa26a3ae31612eec1d1f6ffbce9', 'sww'),
(59, 'csxyz5@amikom.ac.id', 'e6eeed7f31a3d075dc454d4a6a80b381', 'XYZcs'),
(60, 'csxyz5@amikom.ac.id', 'e6eeed7f31a3d075dc454d4a6a80b381', 'XYZcs');

-- --------------------------------------------------------

--
-- Struktur dari tabel `item_transaksi`
--

CREATE TABLE `item_transaksi` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_saat_transaksi` int(11) NOT NULL,
  `sub_total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `item_transaksi`
--

INSERT INTO `item_transaksi` (`id`, `transaksi_id`, `produk_id`, `qty`, `harga_saat_transaksi`, `sub_total`) VALUES
(38, 9, 12, 2, 10000, 20000),
(43, 33, 12, 6, 50000, 300000),
(44, 33, 12, 6, 50000, 300000),
(45, 33, 12, 6, 50000, 300000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `admin_id`, `nama`, `harga`, `stok`) VALUES
(12, 43, 'Bakso', 10000, 52),
(25, 60, 'ayam tiren', 15000, 94);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `admin_id`, `tanggal`, `total`) VALUES
(9, 43, '2021-10-28 07:45:36', 10000),
(26, 60, '2022-11-21 00:58:14', 20000),
(33, 43, '2022-11-26 04:32:25', 50000),
(34, 43, '2022-10-26 04:32:35', 500000);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `item_transaksi`
--
ALTER TABLE `item_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_transaksi_produk_id` (`produk_id`),
  ADD KEY `fk_transaksi_id` (`transaksi_id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_id` (`admin_id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transaksi_admin_id` (`admin_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT untuk tabel `item_transaksi`
--
ALTER TABLE `item_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `item_transaksi`
--
ALTER TABLE `item_transaksi`
  ADD CONSTRAINT `fk_item_transaksi_produk_id` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`),
  ADD CONSTRAINT `fk_transaksi_id` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`);

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
