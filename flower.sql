-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 01:48 AM
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
-- Database: `flower`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`email`, `password`) VALUES
('admin@gmail.com', '$2y$10$jRJG4F3Tk7nDUCU1PC/fAOrfQAbGiSYX3wjnQBUXGtmjw4e9KJnX6');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `product_price`, `added_at`) VALUES
(4, 3, 3, 'Chrysanthemums', 500.00, '2025-05-14 20:01:41'),
(33, 1, 2, 'Lilies', 300.00, '2025-05-14 22:08:16'),
(34, 1, 2, 'Lilies', 300.00, '2025-05-14 22:08:16'),
(35, 1, 3, 'Chrysanthemums', 500.00, '2025-05-14 22:08:20'),
(36, 1, 11, 'Milk Chocolate', 125.00, '2025-05-14 22:08:34'),
(37, 1, 14, 'Mugs', 275.00, '2025-05-14 22:08:38'),
(56, 4, 2, 'blue Flower', 100.00, '2025-05-18 08:04:39'),
(57, 4, 12, 'Flavour Chocolate', 375.00, '2025-05-18 08:51:17');

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
('weq', 'qeqqe@gmial.com', '11', 'qeqwd');

-- --------------------------------------------------------

--
-- Table structure for table `flower`
--

CREATE TABLE `flower` (
  `flower_id` int(11) NOT NULL,
  `color` varchar(255) NOT NULL,
  `cost_per_day` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flower`
--

INSERT INTO `flower` (`flower_id`, `color`, `cost_per_day`, `status`, `image`, `stock`) VALUES
(1, 'red', 50.00, 'Active', 'images/flowers/68297b8a5e89e.jpg', 96),
(2, 'blue', 100.00, 'Active', 'images/flowers/68297bded8050.jpg', 92),
(3, 'pink', 30.00, 'Active', 'images/flowers/6829972fb340c.jpg', 100);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `product` varchar(255) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `name`, `email`, `amount`, `product`, `payment_date`) VALUES
(1, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:09:55'),
(2, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:09:55'),
(3, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:17:29'),
(4, 'kero', 'Kerlousnasser0@gmail.com', 50.00, 'red Flower', '2025-05-18 07:20:27'),
(5, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:21:56'),
(6, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:22:31'),
(7, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:24:35'),
(8, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:25:21'),
(9, 'kero', 'Kerlousnasser0@gmail.com', 50.00, 'red Flower', '2025-05-18 07:25:21'),
(10, 'kero', 'Kerlousnasser0@gmail.com', 50.00, 'red Flower', '2025-05-18 07:30:23'),
(11, 'kero', 'Kerlousnasser0@gmail.com', 50.00, 'red Flower', '2025-05-18 07:30:23'),
(12, 'kero', 'Kerlousnasser0@gmail.com', 100.00, 'blue Flower', '2025-05-18 07:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `user_form`
--

CREATE TABLE `user_form` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`id`, `name`, `email`, `password`) VALUES
(1, 'Mahmoud Amr Zaghloula', 'mahmoud.amr211@gmail.com', '$2y$10$3bNFs0Hy0Zlbm3xaxs2YH.Vu1KMZEBqlkVFizreSIeq1zqjA9ynHK'),
(3, 'ascasc', 'mahmoud.amr20@gmail.com', '$2y$10$BCuaPoY1rsPHL4jJyY4RwOvFzf.cF10YLSyOYrFcnf7OPjwKDycNW'),
(4, 'kero', 'kero@gmail.com', '$2y$10$NernQ8lEc6pdL5mTD6QZLO.RvyUJWeI73ahJofA83D5pI1Q0cyxJq');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `flower`
--
ALTER TABLE `flower`
  ADD PRIMARY KEY (`flower_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `flower`
--
ALTER TABLE `flower`
  MODIFY `flower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_form` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
