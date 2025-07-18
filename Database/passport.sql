-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 05:30 PM
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
-- Database: `kola`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_type`
--

CREATE TABLE `contact_type` (
  `cont_id` int(11) NOT NULL,
  `contType` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_type`
--

INSERT INTO `contact_type` (`cont_id`, `contType`, `created_at`) VALUES
(1, 'Phone', '2025-04-29 21:37:12'),
(2, 'WhatsApp', '2025-04-29 22:16:24'),
(3, 'Imo', '2025-04-29 22:17:34'),
(4, 'Telegram', '2025-04-29 22:17:40'),
(5, 'Viber', '2025-04-29 22:53:53');

-- --------------------------------------------------------

--
-- Table structure for table `pass_info`
--

CREATE TABLE `pass_info` (
  `pass_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `passNo` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `doi` date NOT NULL,
  `doe` date NOT NULL,
  `passPhoto` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `pStatus` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `pro_id` int(11) NOT NULL,
  `name` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `dob` date NOT NULL,
  `nationality` varchar(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pNumber` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `cont_id` int(11) NOT NULL,
  `proPhoto` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `status` enum('active','inactive','','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `username` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(500) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `password`, `creation_date`, `last_updated`) VALUES
(1, 'Syed Muztaba Rafid', 'Syed.shuvon@gmail.com', '$2y$10$kbCzjJC0iXG6.s7WA5B07eDkR22yc9dP4LwnGNYY3ALAKK5.BzIWu', '2025-04-22 12:37:02', '2025-04-24 22:12:47');

-- --------------------------------------------------------

--
-- Table structure for table `visa_info`
--

CREATE TABLE `visa_info` (
  `visa_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `pass_id` int(11) NOT NULL,
  `visaNo` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `visaYear` int(11) NOT NULL,
  `visaCate` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `vDoi` date NOT NULL,
  `vDoe` date NOT NULL,
  `visaImage` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `vStatus` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_type`
--
ALTER TABLE `contact_type`
  ADD PRIMARY KEY (`cont_id`);

--
-- Indexes for table `pass_info`
--
ALTER TABLE `pass_info`
  ADD PRIMARY KEY (`pass_id`),
  ADD KEY `FK_profile_id` (`pro_id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`pro_id`),
  ADD KEY `FK_contType` (`cont_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visa_info`
--
ALTER TABLE `visa_info`
  ADD PRIMARY KEY (`visa_id`),
  ADD KEY `FK_passport` (`pass_id`),
  ADD KEY `FK_proVisa` (`pro_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_type`
--
ALTER TABLE `contact_type`
  MODIFY `cont_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pass_info`
--
ALTER TABLE `pass_info`
  MODIFY `pass_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `pro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visa_info`
--
ALTER TABLE `visa_info`
  MODIFY `visa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pass_info`
--
ALTER TABLE `pass_info`
  ADD CONSTRAINT `FK_profile_id` FOREIGN KEY (`pro_id`) REFERENCES `profile` (`pro_id`) ON UPDATE CASCADE;

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `FK_contType` FOREIGN KEY (`cont_id`) REFERENCES `contact_type` (`cont_id`) ON UPDATE CASCADE;

--
-- Constraints for table `visa_info`
--
ALTER TABLE `visa_info`
  ADD CONSTRAINT `FK_passport` FOREIGN KEY (`pass_id`) REFERENCES `pass_info` (`pass_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_proVisa` FOREIGN KEY (`pro_id`) REFERENCES `profile` (`pro_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
