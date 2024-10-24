-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Okt 2024 pada 08.33
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `poltek_jambi_docs`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `upload_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `category` enum('Kategori 1','Kategori 2','Kategori 3') NOT NULL,
  `status` enum('Draft','Approve','Rejected') NOT NULL DEFAULT 'Draft',
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `documents`
--

INSERT INTO `documents` (`id`, `year`, `title`, `creator_id`, `description`, `upload_date`, `last_modified`, `category`, `status`, `file_path`) VALUES
(1, 2024, 'Percobaan', 2, 'mencoba', '2024-10-11 10:50:44', '2024-10-15 22:57:20', 'Kategori 1', 'Approve', 'uploads/670e784404cb9_Mockup.pdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birth_date` varchar(11) NOT NULL,
  `nidn` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('User','Admin','KETUA LP3M') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `birth_date`, `nidn`, `username`, `password`, `role`) VALUES
(1, 'Kepala LP3M', 'kepalalp3m@gmail.com', '10/11/1993', '03', 'superadmin', '$2y$10$QICpZTYI3UXBI8PBLtFWTet.dU3/s6TqGdCxyP8UwV9TGmxoLMqie', 'KETUA LP3M'),
(2, 'Pak Tanto', 'tanto@gmail.com', '08/29/1990', '02', 'tanto', '$2y$10$oMVjVXEuPN3/QD/RSCSSj.MEaIb0SI2CYRnBNYZW9zi1wcRC0DJy.', 'User'),
(3, 'Pak Rei', 'rei@gmail.com', '08/29/1980', '01', 'admin', '$2y$10$wQDn5r3cFVtktET4OGO3c.7z..i7hNozvPqnT0VmoeUMvLkUZ2zoe', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `nidn` (`nidn`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
