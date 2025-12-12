-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 05:58 AM
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
-- Table structure for table `content_ratings`
--

CREATE TABLE `content_ratings` (
  `rating_id` int(11) NOT NULL,
  `rating_code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content_ratings`
--

INSERT INTO `content_ratings` (`rating_id`, `rating_code`, `description`) VALUES
(1, 'E', 'Everyone'),
(2, 'E10+', 'Everyone 10+'),
(3, 'T', 'Teen'),
(4, 'M', 'Mature'),
(5, 'A', 'Adults Only'),
(6, 'G', 'General Audiences'),
(7, 'PG', 'Parental Guidance Suggested'),
(8, 'PG-13', 'Parents Strongly Cautioned'),
(9, 'R', 'Restricted'),
(10, 'NC-17', 'Adults Only 18+'),
(11, 'TV-Y', 'All Children'),
(12, 'TV-Y7', 'Older Children 7+'),
(13, 'TV-PG', 'Parental Guidance Suggested'),
(14, 'TV-14', 'Parents Strongly Cautioned'),
(15, 'TV-MA', 'Mature Audience Only');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `genre_id` int(11) NOT NULL,
  `genre_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`genre_id`, `genre_name`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Comedy'),
(4, 'Crime'),
(14, 'Documentary'),
(5, 'Drama'),
(6, 'Fantasy'),
(12, 'Historical'),
(7, 'Horror'),
(8, 'Martial Arts'),
(9, 'Mystery'),
(13, 'Post-apocalyptic'),
(15, 'Romance'),
(10, 'Science Fiction'),
(11, 'Superhero'),
(16, 'Thriller');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `media_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `release_date` date NOT NULL,
  `director` varchar(255) NOT NULL,
  `media_type_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `content_rating_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`media_id`, `title`, `release_date`, `director`, `media_type_id`, `image_path`, `genre_id`, `content_rating_id`) VALUES
(1, 'Star Wars: Episode VII - The Force Awakens', '2015-12-14', 'J.J. Abrams', 1, '1.jpg', 10, 8),
(2, 'Avengers: Endgame', '2019-04-18', 'Anthony Russo, Joe Russo', 1, '2.jpg', 2, 8),
(3, 'Spider-Man: No Way Home', '2025-12-13', 'Jon Watts', 1, '3.jpg', 11, 8),
(4, 'Avatar', '2009-12-16', 'James Cameron', 1, '4.jpg', 2, 8),
(5, 'Top Gun: Maverick', '2022-05-24', 'Joseph Kosinski', 1, '5.jpg', 1, 8),
(6, 'Avengers: Infinity War', '2018-04-25', 'Anthony Russo, Joe Russo', 1, '6.jpg', 2, 8),
(7, 'Titanic', '1997-12-14', 'James Cameron', 1, '7.jpg', NULL, 8),
(8, 'The Avengers', '2012-04-11', 'Joss Whedon', 1, '8.jpg', 1, 8),
(9, 'The Dark Knight', '2008-07-14', 'Christopher Nolan', 1, '9.jpg', 1, 8),
(10, 'The Matrix', '1999-03-24', 'Lilly Wachowski, Lana Wachowski', 1, '10.jpg', 1, 9),
(11, 'Breaking Bad', '2008-01-20', 'Michelle MacLaren, Adam Bernstein, Thomas Schnauz, Colin Bucksey, Vince Gilligan, Terry McDonough, Johan Renck, Rian Johnson, Tim Hunter, Peter Medak, Bryan Cranston, Michael Slovis, Jim McKay, David Slade', 2, '11.jpg', 4, 15),
(12, 'Chernobyl', '2019-05-06', 'Johan Renck', 2, '12.jpg', 5, 15),
(13, 'Band of Brothers', '2001-09-09', 'David Frankel, Mikael Salomon, David Leland, Tom Hanks', 2, '13.jpg', 12, 15),
(14, 'The Wire', '2002-06-02', 'Joe Chappelle, Ernest R. Dickerson, Clark Johnson', 2, '14.jpg', 5, 15),
(15, 'Avatar: The Last Airbender', '2005-02-21', 'Giancarlo Volpe, Ethan Spaulding, Lauren MacMullan', 2, '15.jpg', 8, NULL),
(16, 'The Sopranos', '1999-01-10', 'Timothy Van Patten, John Patterson, Allen Coulter', 2, '16.jpg', 5, 15),
(17, 'Game of Thrones', '2011-04-17', 'David Nutter, Alan Taylor, Alex Graves', 2, '17.jpg', 6, 15),
(18, 'Fullmetal Alchemist: Brotherhood', '2009-04-09', 'Yasuhiro Irie, Takuya Igarashi', 2, '18.jpg', 6, 14),
(19, 'Attack on Titan', '2013-09-28', 'Tetsuro Araki, Masashi Koizuka', 2, '19.jpg', 6, 15),
(20, 'The Last Dance', '2020-04-19', 'Jason Hehir', 2, '20.jpg', 14, 15),
(21, 'Rick and Morty', '2013-12-02', 'Wesley Archer, Jacob Hair, Pete Michels', 2, '21.jpg', 3, 15),
(22, 'Sherlock', '2010-10-24', 'Paul McGuigan, Jeremy Lovering, Tom Shankland, Nick Hurran', 2, '22.jpg', 4, 14),
(23, 'Better Call Saul', '2015-02-08', 'Vince Gilligan, Thomas Schnauz, Peter Gould', 2, '23.jpg', 5, 15),
(24, 'The Office', '2005-03-24', 'Paul Feig, Randall Einhorn, Ken Kwapis', 2, '24.jpg', NULL, 14),
(25, 'True Detective', '2014-01-12', 'Cary Joji Fukunaga, Issa López, Jeremy Saulnier, Daniel Sackheim', 2, '25.jpg', 5, 15),
(26, 'Red Dead Redemption 2', '2018-10-26', 'Rob Nelson', 3, '26.jpg', 1, 4),
(27, 'Metal Gear Solid', '1998-10-21', 'Hideo Kojima', 3, '27.jpg', 5, 4),
(28, 'The Last of Us', '2013-06-14', 'Neil Druckmann, Bruce Straley', 3, '28.jpg', 7, 4),
(29, 'Baldur\'s Gate 3', '2023-08-03', 'Swen Vincke', 3, '29.jpg', 6, 4),
(30, 'The Witcher 3: Wild Hunt', '2015-05-19', 'Konrad Tomaszkiewicz', 3, '30.jpg', 6, 4),
(31, 'The Legend of Zelda: Ocarina of Time', '1998-11-23', 'Eiji Aonuma, Yoichi Yamada, Yoshiaki Koizumi', 3, '31.jpg', 6, 1),
(32, 'Final Fantasy VII', '1997-09-07', 'Yoshinori Kitase, Yasushi Matsumura, Masato Yagi', 3, '32.jpg', 10, 3),
(33, 'God of War', '2018-04-20', 'Cory Barlog', 3, '33.jpg', 6, 4),
(34, 'God of War: Ragnarok', '2022-11-09', 'Eric Williams', 3, '34.jpg', 6, 4),
(35, 'Mass Effect 2', '2010-01-26', 'Casey Hudson', 3, '35.jpg', 2, 4),
(36, 'Berserk', '1989-08-01', 'Kentaro Miura', 5, '36.jpg', 6, 4),
(37, 'Fullmetal Alchemist (Manga)', '2001-07-12', 'Hiromu Arakawa', 5, '37.jpg', 6, 3),
(38, 'Death Note (Manga)', '2003-12-01', 'Tsugumi Ohba', 5, '38.jpg', 9, 3),
(39, 'One Piece', '1997-07-22', 'Eiichiro Oda', 5, '39.jpg', 2, 3),
(40, 'Naruto', '1999-09-21', 'Masashi Kishimoto', 5, '40.jpg', 1, 3),
(41, 'Monster (Manga)', '1994-12-05', 'Naoki Urasawa', 5, '41.jpg', 4, 3),
(42, 'Attack on Titan (Manga)', '2009-09-09', 'Hajime Isayama', 5, '42.jpg', 6, 4),
(43, 'Vagabond', '1998-03-23', 'Takehiko Inoue', 5, '43.jpg', 12, 4),
(44, 'Tokyo Ghoul', '2011-09-08', 'Sui Ishida', 5, '44.jpg', 7, 4),
(45, 'Jujutsu Kaisen (Manga)', '2018-03-05', 'Gege Akutami', 5, '45.jpg', 6, 3),
(46, 'Chainsaw Man', '2018-12-03', 'Tatsuki Fujimoto', 5, '46.jpg', 7, 4),
(47, 'Claymore', '2001-05-06', 'Norihiro Yagi', 5, '47.jpg', 6, 3),
(48, 'Blue Exorcist', '2009-04-04', 'Kazue Kato', 5, '48.jpg', 6, 3),
(49, 'Soul Eater', '2004-05-12', 'Atsushi Ōkubo', 5, '49.jpg', 6, 3),
(50, 'Bleach', '2001-08-07', 'Tite Kubo', 5, '50.jpg', 6, 3),
(51, 'The Hobbit', '1937-09-21', 'J.R.R. Tolkien', 6, '51.jpg', 6, 7),
(52, 'The Fellowship of the Ring', '1954-07-29', 'J.R.R. Tolkien', 6, '52.jpg', 6, 7),
(53, 'Dune', '1965-08-01', 'Frank Herbert', 6, '53.jpg', 10, 7),
(54, '1984', '1949-06-08', 'George Orwell', 6, '54.jpg', 10, 7),
(55, 'The Catcher in the Rye', '1951-07-16', 'J.D. Salinger', 6, '55.jpg', 5, 8),
(56, 'Frankenstein', '1818-01-01', 'Mary Shelley', 6, '56.jpg', 7, 7),
(57, 'Dracula', '1897-05-26', 'Bram Stoker', 6, '57.jpg', 7, 7),
(58, 'Pride and Prejudice', '1813-01-28', 'Jane Austen', 6, '58.jpg', 15, 7),
(59, 'The Great Gatsby', '1925-04-10', 'F. Scott Fitzgerald', 6, '59.jpg', 5, 7),
(60, 'Moby-Dick', '1851-10-18', 'Herman Melville', 6, '60.jpg', 2, 7),
(61, 'The Name of the Wind', '2007-03-27', 'Patrick Rothfuss', 6, '61.jpg', 6, 8),
(62, 'The Way of Kings', '2010-08-31', 'Brandon Sanderson', 6, '62.jpg', 6, 8),
(63, 'Gone Girl', '2012-06-05', 'Gillian Flynn', 6, '63.jpg', 16, 9),
(64, 'It', '1986-09-15', 'Stephen King', 6, '64.jpg', 7, 9),
(65, 'The Girl with the Dragon Tattoo', '2005-08-01', 'Stieg Larsson', 6, '65.jpg', 4, 9),
(66, 'Solo Leveling', '2016-07-25', 'Chugong', 7, '66.jpg', 6, 3),
(67, 'The Beginning After the End', '2018-01-01', 'TurtleMe', 7, '67.jpg', 6, 8),
(68, 'Omniscient Reader’s Viewpoint', '2018-02-01', 'Sing-Shong', 7, '68.jpg', 6, 8),
(69, 'The Wandering Inn', '2016-10-01', 'pirateaba', 7, '69.jpg', 6, 8),
(70, 'Mother of Learning', '2011-12-01', 'Domagoj Kurmaic', 7, '70.jpg', 6, 8),
(71, 'Re:Zero (Web Novel)', '2012-04-20', 'Tappei Nagatsuki', 7, '71.jpg', 6, 8),
(72, 'Shadow Slave', '2021-01-01', 'Guiltythree', 7, '72.jpg', 6, 8),
(73, 'Warlock of the Magus World', '2015-01-01', 'The Plagiarist', 7, '73.jpg', 6, 4),
(74, 'Lord of the Mysteries', '2018-04-01', 'Cuttlefish That Loves Diving', 7, '74.jpg', 9, 8),
(75, 'Overgeared', '2014-01-01', 'Park Saenal', 7, '75.jpg', 6, 3),
(76, 'The Legend of the Sun Knight', '2007-01-01', 'Yu Wo', 7, '76.jpg', 3, 7),
(77, 'The Second Coming of Gluttony', '2019-01-01', 'Ro Yu-jin', 7, '77.jpg', 6, 4),
(78, 'Supreme Magus', '2014-05-01', 'Legion20', 7, '78.jpg', 6, 8),
(79, 'My House of Horrors', '2017-01-01', 'I Fix Air Conditioners', 7, '79.jpg', 7, 4),
(80, 'Trash of the Count’s Family', '2018-01-01', 'Yoo Ryeo Han', 7, '80.jpg', 6, 8),
(81, 'Critical Role', '2015-03-12', 'Matthew Mercer', 8, '81.jpg', 6, 14),
(82, 'Red vs Blue', '2003-04-01', 'Rooster Teeth', 8, '82.jpg', 3, 14),
(83, 'Helluva Boss', '2020-11-25', 'Vivienne Medrano', 8, '83.jpg', 3, 15),
(84, 'Hazbin Hotel', '2019-10-28', 'Vivienne Medrano', 8, '84.jpg', 3, 15),
(85, 'Video Game High School', '2012-05-11', 'Matthew Arnold', 8, '85.jpg', 3, 14),
(86, 'The Guild', '2007-07-27', 'Felicia Day', 8, '86.jpg', 3, 13),
(87, 'RWBY', '2013-07-18', 'Monty Oum', 8, '87.jpg', 6, 14),
(88, 'The Legend of Vox Machina', '2022-01-28', 'Sunrise Animation', 8, '88.jpg', 6, 15),
(89, 'Don’t Hug Me I’m Scared', '2011-07-29', 'Becky Sloan', 8, '89.jpg', 7, 14),
(90, 'The Amazing Digital Circus', '2023-10-13', 'Glitch Productions', 8, '90.jpg', 6, 14),
(91, 'Camp Camp', '2016-06-10', 'Rooster Teeth', 8, '91.jpg', 3, 14),
(92, 'Meta Runner', '2019-07-25', 'Glitch Productions', 8, '92.jpg', 10, 13),
(93, 'The Mandela Catalogue', '2021-06-09', 'Alex Kister', 8, '93.jpg', 7, 14),
(94, 'Local 58', '2015-01-01', 'Kris Straub', 8, '94.jpg', 7, 15),
(95, 'Backrooms (Found Footage)', '2019-01-01', 'Kane Parsons', 8, '95.jpg', 7, 14),
(96, 'The Way of Kings (Audiobook)', '2010-08-31', 'Brandon Sanderson', 9, '96.jpg', 6, 8),
(97, 'Mistborn (Audiobook)', '2006-07-17', 'Brandon Sanderson', 9, '97.jpg', 6, 8),
(98, 'The Martian (Audiobook)', '2011-09-27', 'Andy Weir', 9, '98.jpg', 10, 8),
(99, 'Project Hail Mary (Audiobook)', '2021-05-04', 'Andy Weir', 9, '99.jpg', 10, 8),
(100, 'The Hunger Games (Audiobook)', '2008-09-14', 'Suzanne Collins', 9, '100.jpg', 10, 8),
(101, 'Harry Potter and the Sorcerer\'s Stone (Audiobook)', '1997-06-26', 'J.K. Rowling', 9, '101.jpg', 6, 7),
(102, 'The Girl with the Dragon Tattoo (Audiobook)', '2005-08-01', 'Stieg Larsson', 9, '102.jpg', 4, 9),
(103, 'It (Audiobook)', '1986-09-15', 'Stephen King', 9, '103.jpg', 7, 9),
(104, 'The Da Vinci Code (Audiobook)', '2003-03-18', 'Dan Brown', 9, '104.jpg', 9, 8),
(105, 'Ender\'s Game (Audiobook)', '1985-01-15', 'Orson Scott Card', 9, '105.jpg', 10, 7),
(106, 'Ready Player One (Audiobook)', '2011-08-16', 'Ernest Cline', 9, '106.jpg', 10, 8),
(107, 'American Gods (Audiobook)', '2001-06-19', 'Neil Gaiman', 9, '107.jpg', 6, 9),
(108, 'The Shining (Audiobook)', '1977-01-28', 'Stephen King', 9, '108.jpg', 7, 9),
(109, 'The Expanse: Leviathan Wakes (Audiobook)', '2011-06-02', 'James S. A. Corey', 9, '109.jpg', 10, 8),
(110, 'The Wheel of Time: The Eye of the World (Audiobook)', '1990-01-01', 'Robert Jordan', 9, '110.jpg', 6, 8);

-- --------------------------------------------------------

--
-- Table structure for table `media_ratings`
--

CREATE TABLE `media_ratings` (
  `media_id` int(11) NOT NULL,
  `average_rating` decimal(4,2) NOT NULL DEFAULT 0.00,
  `total_ratings` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media_ratings`
--

INSERT INTO `media_ratings` (`media_id`, `average_rating`, `total_ratings`) VALUES
(3, 3.00, 175),
(8, 4.00, 203),
(36, 0.00, 0),
(37, 0.00, 0),
(38, 0.00, 0),
(39, 0.00, 0),
(40, 0.00, 0),
(41, 0.00, 0),
(42, 0.00, 0),
(43, 0.00, 0),
(44, 0.00, 0),
(45, 0.00, 0),
(46, 0.00, 0),
(47, 0.00, 0),
(48, 0.00, 0),
(49, 0.00, 0),
(50, 0.00, 0),
(51, 0.00, 0),
(52, 0.00, 0),
(53, 0.00, 0),
(54, 0.00, 0),
(55, 0.00, 0),
(56, 0.00, 0),
(57, 0.00, 0),
(58, 0.00, 0),
(59, 0.00, 0),
(60, 0.00, 0),
(61, 0.00, 0),
(62, 0.00, 0),
(63, 0.00, 0),
(64, 0.00, 0),
(65, 0.00, 0),
(66, 0.00, 0),
(67, 0.00, 0),
(68, 0.00, 0),
(69, 0.00, 0),
(70, 0.00, 0),
(71, 0.00, 0),
(72, 0.00, 0),
(73, 0.00, 0),
(74, 0.00, 0),
(75, 0.00, 0),
(76, 0.00, 0),
(77, 0.00, 0),
(78, 0.00, 0),
(79, 0.00, 0),
(80, 0.00, 0),
(81, 0.00, 0),
(82, 0.00, 0),
(83, 0.00, 0),
(84, 0.00, 0),
(85, 0.00, 0),
(86, 0.00, 0),
(87, 0.00, 0),
(88, 0.00, 0),
(89, 0.00, 0),
(90, 0.00, 0),
(91, 0.00, 0),
(92, 0.00, 0),
(93, 0.00, 0),
(94, 0.00, 0),
(95, 0.00, 0),
(96, 0.00, 0),
(97, 0.00, 0),
(98, 0.00, 0),
(99, 0.00, 0),
(100, 0.00, 0),
(101, 0.00, 0),
(102, 0.00, 0),
(103, 0.00, 0),
(104, 0.00, 0),
(105, 0.00, 0),
(106, 0.00, 0),
(107, 0.00, 0),
(108, 0.00, 0),
(109, 0.00, 0),
(110, 0.00, 0);

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
(9, 'Audio Book'),
(4, 'Comic Book'),
(5, 'Manga'),
(1, 'Movie'),
(6, 'Novel'),
(2, 'TV Show'),
(3, 'Video Game'),
(7, 'Web Novel'),
(8, 'Web Series');

-- --------------------------------------------------------

--
-- Table structure for table `newmediarequest`
--

CREATE TABLE `newmediarequest` (
  `request_id` int(11) NOT NULL,
  `media_name` varchar(255) NOT NULL,
  `media_type` varchar(255) NOT NULL,
  `media_description` text DEFAULT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newmediarequest`
--

INSERT INTO `newmediarequest` (`request_id`, `media_name`, `media_type`, `media_description`, `request_date`) VALUES
(1, 'Devil may cry 5', 'Video Game', 'action game', '2025-12-07 18:01:28'),
(2, 'book', 'Book', 'description 12/10/2025', '2025-12-10 16:51:33'),
(3, 'Sample Data', 'Movie', 'Sample Data', '2025-12-10 19:08:10'),
(4, 'SampleData001', 'Movie', 'SampleData001', '2025-12-10 20:53:14'),
(5, 'SampleData002', 'TV Show', 'SampleData002', '2025-12-10 20:53:24'),
(6, 'SampleData003', 'Video Game', 'SampleData003', '2025-12-10 20:53:34');

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
(5, 1, 1, 'Good movie but needed some work to be better.', '2025-10-28 19:10:28'),
(6, 1, 0, 'edrcfytvgybhunjikmol', '2025-12-07 18:00:54'),
(7, 1, 0, 'sample data', '2025-12-07 18:01:08'),
(8, 1, 1, 'sample data', '2025-12-07 18:01:15'),
(9, 1, 0, 'sample text', '2025-12-07 18:03:11'),
(10, 54, 0, '', '2025-12-07 18:30:46'),
(11, 54, 0, '', '2025-12-07 18:31:47'),
(12, 54, 1, '', '2025-12-07 18:31:49'),
(13, 54, 0, 'sample text', '2025-12-07 18:35:14'),
(14, 107, 0, 'sample text', '2025-12-07 18:35:29'),
(15, 54, 5, '', '2025-12-10 19:08:19'),
(16, 54, 0, '', '2025-12-10 19:08:24'),
(17, 54, 10, 'trdfghyh', '2025-12-10 19:08:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `content_ratings`
--
ALTER TABLE `content_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `rating_code` (`rating_code`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`),
  ADD UNIQUE KEY `genre_name` (`genre_name`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`media_id`);

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
-- AUTO_INCREMENT for table `content_ratings`
--
ALTER TABLE `content_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `media_types`
--
ALTER TABLE `media_types`
  MODIFY `media_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `newmediarequest`
--
ALTER TABLE `newmediarequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_reviews`
--
ALTER TABLE `user_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

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
