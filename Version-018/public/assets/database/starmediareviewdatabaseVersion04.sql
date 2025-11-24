-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 09:25 PM
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
-- Database: `starmediareviewdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `admintable`
--

CREATE TABLE `admintable` (
  `AdminID#` int(11) NOT NULL,
  `AdminName` varchar(150) NOT NULL,
  `AdminPassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admintable`
--

INSERT INTO `admintable` (`AdminID#`, `AdminName`, `AdminPassword`) VALUES
(1, 'Ashton Pate', 'Password001'),
(2, 'Gary Hembree', 'Password002'),
(3, 'Gary Hembree', 'Password002');

-- --------------------------------------------------------

--
-- Table structure for table `meidatable`
--

CREATE TABLE `meidatable` (
  `Media ID#` int(11) NOT NULL,
  `Name` varchar(150) NOT NULL,
  `Year of Release` date NOT NULL,
  `Director` varchar(150) NOT NULL,
  `Genre` varchar(150) NOT NULL,
  `Type:` varchar(100) NOT NULL,
  `Content Rating` varchar(50) NOT NULL,
  `Media Image` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meidatable`
--

INSERT INTO `meidatable` (`Media ID#`, `Name`, `Year of Release`, `Director`, `Genre`, `Type:`, `Content Rating`, `Media Image`) VALUES
(1, 'Star Wars: Episode VII - The Force Awakens', '2015-12-14', 'J.J. Abrams', 'Sci-fi', 'Action Adventure', 'PG-13', 'sample data'),
(2, ' Avengers: Endgame', '2019-04-18', 'Anthony Russo, Joe Russo', 'Action Adventure', 'Movie', 'PG-13', 'sample data'),
(3, 'Spider-man no way home', '2025-12-13', 'Jon watts', 'Superhero Action Adventure', 'Movie', 'PG-13', 'Sample Data'),
(4, 'Avatar', '2009-12-16', 'James Cameron', 'Sample Data', 'Action Adventure', 'PG-13', 'Sample Data'),
(5, 'Top Gun Maverick', '2022-05-24', 'Joseph Kosinski', 'Action Epic', 'Movie', 'PG-13', 'Sample Data'),
(6, 'Avengers Infinity War', '2018-04-25', 'Anthony Russo, Joe Russo', 'Action Adventure', 'Movie', 'PG-13', 'Sample Data'),
(7, 'Titanic', '1997-12-14', 'James Cameron', 'Disaster Epic', 'Movie', 'PG-13', 'Sample Data'),
(8, 'The Avengers', '2012-04-11', 'Joss Whedon', 'Action Alien-Invasion', 'Movie', 'PG-13', 'Sample Data'),
(9, 'The Dark Knight', '2008-07-14', 'Christopher Nolan', 'Action Epic', 'Movie', 'PG-13', 'Sample Data'),
(10, 'The Matrix', '1999-03-24', 'Lilly Wachowski, Lana Wachowski', 'Action Cyberpunk', 'Movie', 'R', 'Sample Data');

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

-- --------------------------------------------------------

--
-- Table structure for table `reviewstable`
--

CREATE TABLE `reviewstable` (
  `Media ID#` int(11) NOT NULL,
  `Average Rating` decimal(50,0) NOT NULL,
  `Total Ratings` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviewstable`
--

INSERT INTO `reviewstable` (`Media ID#`, `Average Rating`, `Total Ratings`) VALUES
(3, 3, 175),
(8, 4, 203);

-- --------------------------------------------------------

--
-- Table structure for table `userreviews`
--

CREATE TABLE `userreviews` (
  `Media ID#` int(11) NOT NULL,
  `Review ID#` int(11) NOT NULL,
  `Rating` int(11) NOT NULL,
  `Review Text` text NOT NULL,
  `Review Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userreviews`
--

INSERT INTO `userreviews` (`Media ID#`, `Review ID#`, `Rating`, `Review Text`, `Review Date`) VALUES
(4, 1, 3, 'Was a good movie.', '2025-10-28 19:07:41'),
(2, 2, 3, 'Really good movie', '2025-10-28 19:09:55'),
(6, 3, 3, 'Best movie ever!', '2025-10-28 19:09:55'),
(3, 4, 2, 'Good movie but could be better.', '2025-10-28 19:10:28'),
(1, 5, 1, 'Good movie but needed some work to be better.', '2025-10-28 19:10:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admintable`
--
ALTER TABLE `admintable`
  ADD PRIMARY KEY (`AdminID#`);

--
-- Indexes for table `meidatable`
--
ALTER TABLE `meidatable`
  ADD PRIMARY KEY (`Media ID#`);

--
-- Indexes for table `newmediarequest`
--
ALTER TABLE `newmediarequest`
  ADD PRIMARY KEY (`requestID#`);

--
-- Indexes for table `reviewstable`
--
ALTER TABLE `reviewstable`
  ADD KEY `Media ID#` (`Media ID#`);

--
-- Indexes for table `userreviews`
--
ALTER TABLE `userreviews`
  ADD PRIMARY KEY (`Review ID#`),
  ADD KEY `Media ID#` (`Media ID#`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admintable`
--
ALTER TABLE `admintable`
  MODIFY `AdminID#` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `meidatable`
--
ALTER TABLE `meidatable`
  MODIFY `Media ID#` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `newmediarequest`
--
ALTER TABLE `newmediarequest`
  MODIFY `requestID#` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `userreviews`
--
ALTER TABLE `userreviews`
  MODIFY `Review ID#` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviewstable`
--
ALTER TABLE `reviewstable`
  ADD CONSTRAINT `reviewstable_ibfk_1` FOREIGN KEY (`Media ID#`) REFERENCES `meidatable` (`Media ID#`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userreviews`
--
ALTER TABLE `userreviews`
  ADD CONSTRAINT `userreviews_ibfk_1` FOREIGN KEY (`Media ID#`) REFERENCES `meidatable` (`Media ID#`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
