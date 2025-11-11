-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 11:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `royal_cozy`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_details`
--

CREATE TABLE `customer_details` (
  `Firstname` varchar(255) NOT NULL,
  `Lastname` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `City` varchar(255) NOT NULL,
  `Province` varchar(255) NOT NULL,
  `Postal` int(255) NOT NULL,
  `Phonenumber` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Arrival` date NOT NULL,
  `Departure` date NOT NULL,
  `NumberOfAdults` int(11) NOT NULL,
  `NumberOfKids` int(11) NOT NULL,
  `GCASH` int(11) NOT NULL,
  `SpecialRequest` varchar(255) NOT NULL,
  `RoomType` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_details`
--

INSERT INTO `customer_details` (`Firstname`, `Lastname`, `Address`, `City`, `Province`, `Postal`, `Phonenumber`, `Email`, `Arrival`, `Departure`, `NumberOfAdults`, `NumberOfKids`, `GCASH`, `SpecialRequest`, `RoomType`) VALUES
('dsadasd', 'sdasdasdasd', 'dsadasqweqw', 'qweq', 'qwewq', 2010, 'qwewq', 'joreyoguin@gmail.com', '2025-10-09', '2025-10-15', 2, 3, 1000, '', ''),
('greyyyyy', 'guin', 'fdsjhffs', 'sdaadadasdasd', 'dsadas', 22831, '32132131', 'ejsendeguzman25@gmail.com', '2025-01-01', '2025-02-02', 1, 223, 23, '', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
