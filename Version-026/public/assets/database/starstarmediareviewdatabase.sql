-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 09:47 AM
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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(150) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_password`) VALUES
(1, 'Ashton Pate', 'Password001'),
(2, 'Gary Hembree', 'Password002');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `media_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `release_date` date NOT NULL,
  `director` varchar(255) NOT NULL,
  `genre` varchar(255) NOT NULL,
  `media_type_id` int(11) NOT NULL,
  `content_rating` varchar(50) NOT NULL,
  `image_path` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`media_id`, `title`, `release_date`, `director`, `genre`, `media_type_id`, `content_rating`, `image_path`) VALUES
(1, 'Star Wars: Episode VII - The Force Awakens', '2015-12-14', 'J.J. Abrams', 'Sci-Fi, Action', 1, 'PG-13', '1.jpg'),
(2, 'Avengers: Endgame', '2019-04-18', 'Anthony Russo, Joe Russo', 'Action, Adventure', 1, 'PG-13', '2.jpg'),
(3, 'Spider-Man: No Way Home', '2025-12-13', 'Jon Watts', 'Superhero, Action, Adventure', 1, 'PG-13', '3.jpg'),
(4, 'Avatar', '2009-12-16', 'James Cameron', 'Action, Adventure', 1, 'PG-13', '4.jpg'),
(5, 'Top Gun: Maverick', '2022-05-24', 'Joseph Kosinski', 'Action, Epic', 1, 'PG-13', '5.jpg'),
(6, 'Avengers: Infinity War', '2018-04-25', 'Anthony Russo, Joe Russo', 'Action, Adventure', 1, 'PG-13', '6.jpg'),
(7, 'Titanic', '1997-12-14', 'James Cameron', 'Disaster, Epic', 1, 'PG-13', '7.jpg'),
(8, 'The Avengers', '2012-04-11', 'Joss Whedon', 'Action, Alien Invasion', 1, 'PG-13', '8.jpg'),
(9, 'The Dark Knight', '2008-07-14', 'Christopher Nolan', 'Action, Epic', 1, 'PG-13', '9.jpg'),
(10, 'The Matrix', '1999-03-24', 'Lilly Wachowski, Lana Wachowski', 'Action, Cyberpunk', 1, 'R', '10.jpg'),
(11, 'Breaking Bad', '2008-01-20', 'Michelle MacLaren, Adam Bernstein, Vince Gilligan', 'Drug Crime, Epic, Dark Comedy, Tragedy', 2, 'TV-MA', '11.jpg'),
(12, 'Chernobyl', '2019-05-06', 'Johan Renck', 'Disaster, Period Drama', 2, 'TV-MA', '12.jpg'),
(13, 'Band of Brothers', '2001-09-09', 'David Frankel, Mikael Salomon, Tom Hanks', 'Historical Epic, Action', 2, 'TV-MA', '13.jpg'),
(14, 'The Wire', '2002-06-02', 'Joe Chappelle, Ernest R. Dickerson, Clark Johnson', 'Cop Drama, Drug Crime', 2, 'TV-MA', '14.jpg'),
(15, 'Avatar: The Last Airbender', '2005-02-21', 'Giancarlo Volpe, Ethan Spaulding, Lauren MacMullan', 'Adventure Epic, Martial Arts', 2, 'TV-Y7-FV', '15.jpg'),
(16, 'The Sopranos', '1999-01-10', 'Timothy Van Patten, John Patterson, Allen Coulter', 'Gangster, Crime Drama', 2, 'TV-MA', '16.jpg'),
(17, 'Game of Thrones', '2011-04-17', 'David Nutter, Alan Taylor, Alex Graves', 'Action Epic, Dark Fantasy', 2, 'TV-MA', '17.jpg'),
(18, 'Fullmetal Alchemist: Brotherhood', '2009-04-09', 'Yasuhiro Irie, Takuya Igarashi', 'Anime, Fantasy Epic', 2, 'TV-14', '18.jpg'),
(19, 'Attack on Titan', '2013-09-28', 'Tetsuro Araki, Masashi Koizuka', 'Anime, Dark Fantasy', 2, 'TV-MA', '19.jpg'),
(20, 'The Last Dance', '2020-04-19', 'Jason Hehir', 'Sports Documentary, History', 2, 'TV-MA', '20.jpg'),
(21, 'Rick and Morty', '2013-12-02', 'Wesley Archer, Jacob Hair, Pete Michels', 'Buddy Comedy, Parody', 2, 'TV-MA', '21.jpg'),
(22, 'Sherlock', '2010-10-24', 'Paul McGuigan, Jeremy Lovering, Nick Hurran', 'Police Procedural, Crime', 2, 'TV-14', '22.jpg'),
(23, 'Better Call Saul', '2015-02-08', 'Vince Gilligan, Thomas Schnauz, Peter Gould', 'Drug Crime, Legal Drama', 2, 'TV-MA', '23.jpg'),
(24, 'The Office', '2005-03-24', 'Paul Feig, Randall Einhorn, Ken Kwapis', 'Mockumentary, Sitcom', 2, 'TV-14', '24.jpg'),
(25, 'True Detective', '2014-01-12', 'Cary Joji Fukunaga, Issa Lopez, Daniel Sackheim', 'Cop Drama, Hard-Boiled Detective', 2, 'TV-MA', '25.jpg'),
(26, 'Red Dead Redemption 2', '2018-10-26', 'Rob Nelson', 'Western Epic, Action', 3, 'M', '26.jpg'),
(27, 'Metal Gear Solid', '1998-10-21', 'Hideo Kojima', 'Mystery, Drama', 3, 'M', '27.jpg'),
(28, 'The Last of Us', '2013-06-14', 'Neil Druckmann, Bruce Straley', 'Zombie Horror, Survival', 3, 'M', '28.jpg'),
(29, 'Baldur\'s Gate 3', '2023-08-03', 'Swen Vincke', 'Fantasy, Action-Adventure', 3, 'M', '29.jpg'),
(30, 'The Witcher 3: Wild Hunt', '2015-05-19', 'Konrad Tomaszkiewicz', 'Fantasy, Dark Comedy', 3, 'M', '30.jpg'),
(31, 'The Legend of Zelda: Ocarina of Time', '1998-11-23', 'Eiji Aonuma, Yoichi Yamada, Yoshiaki Koizumi', 'Fantasy, Action', 3, 'E', '31.jpg'),
(32, 'Final Fantasy VII', '1997-09-07', 'Yoshinori Kitase, Yasushi Matsumura, Masato Yagi', 'Fantasy, Sci-Fi', 3, 'T', '32.jpg'),
(33, 'God of War', '2018-04-20', 'Cory Barlog', 'Dark Fantasy, Drama', 3, 'M', '33.jpg'),
(34, 'God of War: Ragnarok', '2022-11-09', 'Eric Williams', 'Dark Fantasy, Drama', 3, 'M', '34.jpg'),
(35, 'Mass Effect 2', '2010-01-26', 'Casey Hudson', 'Cyber Thriller, Action-Adventure', 3, 'M', '35.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `media_ratings`
--

CREATE TABLE `media_ratings` (
  `media_id` int(11) NOT NULL,
  `average_rating` decimal(3,2) NOT NULL,
  `total_ratings` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media_ratings`
--

INSERT INTO `media_ratings` (`media_id`, `average_rating`, `total_ratings`) VALUES
(3, 3.00, 175),
(8, 4.00, 203);

-- --------------------------------------------------------

--
-- Table structure for table `media_types`
--

CREATE TABLE `media_types` (
  `media_type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media_types`
--

INSERT INTO `media_types` (`media_type_id`, `type_name`) VALUES
(9, 'audio_book'),
(4, 'comic_book'),
(5, 'manga'),
(1, 'movie'),
(6, 'novel'),
(2, 'tv_show'),
(3, 'video_game'),
(7, 'web_novel'),
(8, 'web_series');

-- --------------------------------------------------------

--
-- Table structure for table `newmediarequest`
--

CREATE TABLE `newmediarequest` (
  `request_id` int(11) NOT NULL,
  `media_name` varchar(255) NOT NULL,
  `media_type` varchar(100) NOT NULL,
  `media_description` text NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_reviews`
--

CREATE TABLE `user_reviews` (
  `review_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `rating` tinyint(2) NOT NULL,
  `review_text` text DEFAULT NULL,
  `review_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_reviews`
--

INSERT INTO `user_reviews` (`review_id`, `media_id`, `rating`, `review_text`, `review_date`) VALUES
(1, 4, 3, 'Was a good movie.', '2025-10-28 19:07:41'),
(2, 2, 3, 'Really good movie', '2025-10-28 19:09:55'),
(3, 6, 3, 'Best movie ever!', '2025-10-28 19:09:55'),
(4, 3, 2, 'Good movie but could be better.', '2025-10-28 19:10:28'),
(5, 1, 1, 'Good movie but needed some work to be better.', '2025-10-28 19:10:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `idx_media_type` (`media_type_id`);

--
-- Indexes for table `media_ratings`
--
ALTER TABLE `media_ratings`
  ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `media_types`
--
ALTER TABLE `media_types`
  ADD PRIMARY KEY (`media_type_id`),
  ADD UNIQUE KEY `uq_type_name` (`type_name`);

--
-- Indexes for table `newmediarequest`
--
ALTER TABLE `newmediarequest`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `user_reviews`
--
ALTER TABLE `user_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_user_reviews_media_id` (`media_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `media_types`
--
ALTER TABLE `media_types`
  MODIFY `media_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `newmediarequest`
--
ALTER TABLE `newmediarequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_reviews`
--
ALTER TABLE `user_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `fk_media_type` FOREIGN KEY (`media_type_id`) REFERENCES `media_types` (`media_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `media_ratings`
--
ALTER TABLE `media_ratings`
  ADD CONSTRAINT `fk_media_ratings_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_reviews`
--
ALTER TABLE `user_reviews`
  ADD CONSTRAINT `fk_user_reviews_media` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
