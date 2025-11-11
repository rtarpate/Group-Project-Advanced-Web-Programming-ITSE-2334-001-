-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 05:36 AM
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
-- Database: `starstarmediareviewdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `newmediarequest`
--

CREATE TABLE `newmediarequest` (
  `requestID#` int(11) NOT NULL,
  `Media Name:` varchar(100) NOT NULL,
  `Media Type:` varchar(100) NOT NULL,
  `Description:` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newmediarequest`
--

INSERT INTO `newmediarequest` (`requestID#`, `Media Name:`, `Media Type:`, `Description:`) VALUES
(1, 'Godzilla', 'movie', 'sample data'),
(2, 'stranger things', 'tv show', 'sample data'),
(3, 'Halo: reach', 'video game', 'sample data');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `newmediarequest`
--
ALTER TABLE `newmediarequest`
  ADD PRIMARY KEY (`requestID#`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `newmediarequest`
--
ALTER TABLE `newmediarequest`
  MODIFY `requestID#` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
