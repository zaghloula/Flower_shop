-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2024 at 10:43 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flower`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `fname`, `lname`, `email`, `password`) VALUES
(1, 'manar', 'ahmed', 'mana.ahmed@admin.com', 'manaradmin'),
(2, 'Nado', 'Ras', 'nado@adminn.com', 'e19d5cd5af0378da05f63f891c7467af');

-- --------------------------------------------------------

--
-- Table structure for table `flower`
--

CREATE TABLE `flower` (
  `flower_id` int(11) NOT NULL,
  `color` varchar(20) NOT NULL,
  `cost_per_day` float NOT NULL,
  `status` enum('Active','Out of service') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flower`
--

INSERT INTO `flower` (`flower_id`, `color`, `cost_per_day`, `status`) VALUES
(1, 'Red', 15.00, 'Active'),
(2, 'White', 12.50, 'Active'),
(3, 'Purple', 25.00, 'Active'),
(4, 'Yellow', 10.00, 'Active'),
(5, 'Pink', 18.00, 'Active'),
(6, 'Blue', 20.00, 'Active'),
(7, 'Orange', 8.00, 'Active'),
(8, 'White', 9.00, 'Active'),
(9, 'Red', 13.00, 'Active'),
(10, 'Pink', 16.00, 'Active'),
(11, 'Lavender', 14.00, 'Active'),
(12, 'White', 18.50, 'Active'),
(13, 'Peach', 11.00, 'Active'),
(14, 'Red', 12.00, 'Active'),
(15, 'Yellow', 10.50, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`name`, `email`, `number`, `message`) VALUES
('', '', '', ''),  -- Empty row with null values
('abdo@gmail.com', '5273', 'thanks', ''),  -- Added empty string for 'message'
('abdo@gmail.com', '853573', 'ssssss', '');  -- Added empty string for 'message'

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `license_id` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `pnumber` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `license_id`, `email`, `password`, `fname`, `lname`, `pnumber`) VALUES
(1, '165443250', 'a@a.com', '202cb962ac59075b964b07152d234b70', 'AHMED', 'AYMAN', 2126612),
(2, '521223130', 'b@b.com', '202cb962ac59075b964b07152d234b70', 'BASSEM', 'BABLO', 1530016612),
(3, '165413240', 'c@c.com', '202cb962ac59075b964b07152d234b70', 'CARLOS', 'HISHAM', 112354),
(4, '144216330', 'd@d.com', '202cb962ac59075b964b07152d234b70', 'DAAWOD', 'DAHY', 1112002),
(5, '1654442530', 'e@e.com', '202cb962ac59075b964b07152d234b70', 'EMAD', 'SAYED', 1516742),
(6, '654358530', 'f@f.com', '202cb962ac59075b964b07152d234b70', 'FARID', 'FARES', 1516397),
(7, '321421360', 'g@g.com', '202cb962ac59075b964b07152d234b70', 'GAMAL', 'GABER', 1510002),
(8, '512421330', 'h@h.com', '202cb962ac59075b964b07152d234b70', 'HOSSAM', 'AHMED', 1612202),
(9, '165532410', 'i@i.com', '202cb962ac59075b964b07152d234b70', 'ENJY', 'EBRAHIM', 1516612),
(10, '124421360', 'j@j.com', '202cb962ac59075b964b07152d234b70', 'JODY', 'OSAMA', 1612457),
(11, '165447856', 'k@k.com', '202cb962ac59075b964b07152d234b70', 'KAMAL', 'TAREK', 1522312),
(12, '543124320', 'l@l.com', '202cb962ac59075b964b07152d234b70', 'LAYAN', 'MOHAMED', 2105106),
(13, '654427650', 'm@m.com', '202cb962ac59075b964b07152d234b70', 'MOODY', 'WASSIM', 2311612),
(14, '165436130', 'n@n.com', '202cb962ac59075b964b07152d234b70', 'NADER', 'AHMED', 3326612),
(15, '165444323', 'o@o.com', '202cb962ac59075b964b07152d234b70', 'OSAMA', 'OMAR', 1555512),
(16, '165443130', 'p@p.com', '202cb962ac59075b964b07152d234b70', 'PANCIH', 'YAHIA', 1514892),
(17, '165421330', 'q@q.com', '202cb962ac59075b964b07152d234b70', 'MAHMOUD', 'SHARK', 5179462),
(18, '165403250', 'r@r.com', '202cb962ac59075b964b07152d234b70', 'SHEKO', 'RAMADAN', 1512212),
(19, '1011121314', 'nadine@n.com', 'nadinecust', 'Nadine', 'Rasmy', 2147483647);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `product` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `name`, `email`, `amount`, `product`, `created_at`) VALUES
(1, 'abdo', 'abdo@gmail.com', 1.00, 'rose', '2024-05-11 17:59:45'),
(2, 'abdo', 'abdo@gmail.com', 1.00, 'rose', '2024-05-11 18:01:09'),
(3, 'abdo', 'abdoo@gmail.com', 3.00, 'lilac', '2024-05-11 18:23:04'),
(4, 'abdo', 'abdoo@gmail.com', 1.00, 'roses', '2024-05-11 18:24:09'),
(5, 'nadiner', 'nadine@user.com', 1.00, 'roses', '2024-05-11 19:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--





CREATE TABLE `user_form` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`id`, `name`, `email`, `password`, `user_type`) VALUES
(1, 'alaml Wk50', 'alamlwk50@gmail.com', '25f9e794323b453885f5181f1b624d0b', 'admin'),
(2, 'Alaa melook', '2205214@ANU.edu.eg', '25f9e794323b453885f5181f1b624d0b', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
