-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2024 at 04:09 PM
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
-- Database: `wellness`
--

-- --------------------------------------------------------

--
-- Table structure for table `affiliates`
--

CREATE TABLE `affiliates` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(255) NOT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `phone_number` int(15) DEFAULT NULL,
  `service_provider` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `affiliates`
--

INSERT INTO `affiliates` (`id`, `customer_id`, `referrer_id`, `created_at`, `status`, `account_name`, `phone_number`, `service_provider`) VALUES
(31, 13, 0, '2024-12-04 07:51:24', 'active', '', NULL, NULL),
(32, 24, 31, '2024-12-04 09:05:50', '1', '', NULL, NULL),
(34, 28, 32, '2024-12-04 20:11:10', 'active', NULL, NULL, NULL),
(44, 57, 0, '2024-12-18 13:53:03', '', NULL, NULL, NULL),
(45, 59, 0, '2024-12-18 14:50:27', '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `affiliate_earnings`
--

CREATE TABLE `affiliate_earnings` (
  `id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `typeof_purchase` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `affiliate_earnings`
--

INSERT INTO `affiliate_earnings` (`id`, `affiliate_id`, `amount`, `product_id`, `product_name`, `typeof_purchase`, `created_at`, `status`) VALUES
(40, 34, 8.88, 1, '14 DAYS TO AMAZING HEALTH', 'L1 Purchase', '2024-12-09 14:20:43', 0),
(41, 32, 1.18, 1, '14 DAYS TO AMAZING HEALTH', 'L2 Purchase', '2024-12-09 14:20:44', 0),
(42, 34, 8.88, 1, '14 DAYS TO AMAZING HEALTH', 'L1 Purchase', '2024-12-09 14:37:55', 0),
(43, 32, 1.18, 1, '14 DAYS TO AMAZING HEALTH', 'L2 Purchase', '2024-12-09 14:37:55', 0),
(56, 32, 278.13, 3, 'CERTIFIED HEALTH COACH', 'L1 Purchase', '2024-12-09 15:25:42', 0),
(57, 31, 37.08, 3, 'CERTIFIED HEALTH COACH', 'L2 Purchase', '2024-12-09 15:25:42', 0),
(58, 31, 278.13, 3, 'CERTIFIED HEALTH COACH', 'L1 Purchase', '2024-12-09 15:29:34', 0),
(59, 31, 278.13, 3, 'CERTIFIED HEALTH COACH', 'L1 Purchase', '2024-12-09 15:31:53', 0),
(60, 34, 887.80, 1, 'VIRTUAL 8 WEEKS TO WELLNESS', 'L1 Purchase', '2024-12-09 16:07:40', 0),
(61, 32, 118.37, 1, 'VIRTUAL 8 WEEKS TO WELLNESS', 'L2 Purchase', '2024-12-09 16:07:40', 0),
(62, 34, 887.80, 1, 'VIRTUAL 8 WEEKS TO WELLNESS', 'L1 Purchase', '2024-12-09 16:07:56', 0),
(63, 32, 118.37, 1, 'VIRTUAL 8 WEEKS TO WELLNESS', 'L2 Purchase', '2024-12-09 16:07:56', 0),
(64, 31, 110.64, 5, 'Fourteen Days Skinny Tummy Challenge', 'L1 Purchase', '2024-12-11 01:14:56', 0),
(65, 31, 110.64, 5, 'Fourteen Days Skinny Tummy Challenge', 'L1 Purchase', '2024-12-11 01:15:10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `booked_appointments`
--

CREATE TABLE `booked_appointments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` int(11) NOT NULL,
  `visit_date` datetime NOT NULL,
  `message` text DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booked_appointments`
--

INSERT INTO `booked_appointments` (`id`, `name`, `email`, `number`, `visit_date`, `message`, `duration`, `created_at`, `amount`) VALUES
(1, 'test', 'test', 0, '0000-00-00 00:00:00', 'test', 0, '2024-12-09 20:52:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `price`, `path`) VALUES
(1, '14 DAYS TO AMAZING HEALTH', 3.99, NULL),
(2, '14 DAYS TO AMAZING HEALTH WORK MANUAL', 3.99, NULL),
(3, 'MY HEALTH AND THE CREATOR (DIGITAL)', 4.99, '../eBooks/My Health and The Creator Bible Study CWC.pdf'),
(4, 'MY HEALTH AND THE CREATOR (PHYSICAL)', 0.01, NULL),
(5, 'EXERCISE & NUTRITION MANUAL', 3.99, NULL),
(6, 'FIGHT CANCER & BOOST IMMUNE SYSTEM', 5, '../eBooks/FIGHT CANCER AND BOOST IMMUNE SYSTEM.pdf'),
(7, 'GET HEALTHY FOR LIFE', 3.99, '../eBooks/Get Healthy For Life.pdf'),
(8, 'INCREDIBLY DELICIOUS VEGAN RECIPES (2nd Ed.)', 3.99, NULL),
(9, 'UNDO HYPERTENSION GUIDE', 5, '../eBooks/UNDO HYPERTENSION.pdf'),
(10, 'UNDO DIABETES GUIDE', 5, '../eBooks/UNDO DIABETES BROCHURE.pdf'),
(11, 'UNDO HEART DISEASE GUIDE', 5, '../eBooks/UNDO HEART DISEASE.pdf'),
(12, 'UNDO OBESITY & FATTY LIVER GUIDE\r\n', 5, '../eBooks/OBESITY AND FATTY LIVER.pdf'),
(13, 'UNDO DISEASES BOOKLETS COMBO', 20, NULL),
(14, 'INCREDIBLY DELICIOUS VEGAN (1ST ED.)', 9.99, NULL),
(15, 'COOPER WELLNESS CENTER PROGRAM MANUAL', 9.99, NULL),
(16, 'CHARLIE GOES TO THE DOCTOR', 24.99, '../eBooks/Charlie Goes To The Doctor.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course` text NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course`, `price`) VALUES
(1, 'FOURTEEN DAYS to Amazing Health', 79),
(2, 'Transformation To a Healthier You', 59),
(3, 'CERTIFIED HEALTH COACH', 125),
(5, 'Fourteen Days Skinny Tummy Challenge', 49.99);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` int(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `affiliate` int(11) NOT NULL,
  `affiliate_referrer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `password`, `affiliate`, `affiliate_referrer_id`, `created_at`) VALUES
(13, 'Daniel', 'danieltesla746@gmail.com', 202248817, '$2y$10$hGLyMKLlV0bagLOVf8XWZuDnrVSkQNGHtAG71h2wohzHdZ4xOrTgW', 1, NULL, '2024-12-04 07:51:24'),
(24, 'Stephen', 'seshun65@gmail.com', 505987567, '$2y$10$K1JRwwTcs/y6RNFj.xolOOyMJYee76UQ15ldUd5uZFkaUqzQ1DY5G', 1, 31, '2024-12-04 09:05:50'),
(28, 'Joseph', 'joseph@gmail.com', 549264541, '$2y$10$MirFShTsBDlyU7G4SDvly.cVlVKpiC/XvB6uZVyp5lyTnHk8mG7Ca', 1, 32, '2024-12-04 20:11:10'),
(29, 'Hanfred', 'hanfred@gmail.com', 200000000, '$2y$10$0K2B4tgSOHSBZ3KfCEbYNO9ZGGbAPSJb52ALPdk/iHovj5LAz3YhS', 1, 34, '2024-12-04 20:14:04'),
(57, 'Steve', 'gamiest_framed.8z@icloud.com', 548636054, '$2y$10$q8j1nARu2d4t93o3gVxqc.rp.cEh18meD9gL9RCnR6fQtWco1SZD2', 1, NULL, '2024-12-18 13:53:03'),
(58, 'Nicholas Ashiatey', 'nic1ash@yahoo.co.uk', 249234134, '$2y$10$PylJqm6PrG/T6ez/4Bk8WObDnLPwgr5PKJR9yCesrrF9ncb.8dzd.', 0, NULL, '2024-12-13 17:10:01'),
(59, 'Dondre Dockery', 'donadockerymd@gmail.com', 2147483647, '$2y$10$HAOxjcdRXqrs2OO8EvSU3OqO7gfSaht0BSxgdWG/5PoAdKMr/UCKq', 1, NULL, '2024-12-18 14:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `one-time_donation`
--

CREATE TABLE `one-time_donation` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` float NOT NULL,
  `reference` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `one-time_donation`
--

INSERT INTO `one-time_donation` (`id`, `email`, `amount`, `reference`, `date`) VALUES
(1, 'test', 0, 'test', '2024-12-09 20:51:07');

-- --------------------------------------------------------

--
-- Table structure for table `payouts`
--

CREATE TABLE `payouts` (
  `id` int(11) NOT NULL,
  `affiliate_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `recipient_code` varchar(255) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `program` varchar(255) NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `program`, `price`) VALUES
(1, 'VIRTUAL 8 WEEKS TO WELLNESS', 399),
(2, 'RESIDENTIAL PROGRAM', 3250);

-- --------------------------------------------------------

--
-- Table structure for table `sold_courses`
--

CREATE TABLE `sold_courses` (
  `id` int(11) NOT NULL,
  `course` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sold_courses`
--

INSERT INTO `sold_courses` (`id`, `course`, `email`, `amount`, `reference`, `date`) VALUES
(2, 'test', 'test', 0, 'test', '2024-12-09 20:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `sold_programs`
--

CREATE TABLE `sold_programs` (
  `id` int(11) NOT NULL,
  `program` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sold_programs`
--

INSERT INTO `sold_programs` (`id`, `program`, `email`, `amount`, `reference`, `date`) VALUES
(1, 'test', 'test', 0, 'test', '2024-12-09 20:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `book_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `reference`, `email`, `book_id`, `amount`, `status`, `created_at`) VALUES
(1, 'BOOK181255492', 'nic1ash@yahoo.co.uk', 1, 60.82, 'success', '2024-12-02 15:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'cooperdockeryhealth@gmail.com', '$2y$10$X7mrrNrySYsbnYtldwKYUeH.HLn0q21F49w3TVXwHA2cz/ENZ7tY2', 'admin', '2024-11-25 19:13:36', '2024-11-25 19:15:47'),
(2, 'admin', '$2y$10$Nr1Hy0wWYE9e1zD8mbTCwu6dolco9pTYQKzk5LQHEmoCTyi7rKZkq', 'user', '2024-09-20 02:33:39', '2024-10-09 11:53:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affiliates`
--
ALTER TABLE `affiliates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `affiliate_earnings`
--
ALTER TABLE `affiliate_earnings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booked_appointments`
--
ALTER TABLE `booked_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `one-time_donation`
--
ALTER TABLE `one-time_donation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payouts`
--
ALTER TABLE `payouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `affiliate_id` (`affiliate_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sold_courses`
--
ALTER TABLE `sold_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sold_programs`
--
ALTER TABLE `sold_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliates`
--
ALTER TABLE `affiliates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `affiliate_earnings`
--
ALTER TABLE `affiliate_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `booked_appointments`
--
ALTER TABLE `booked_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `one-time_donation`
--
ALTER TABLE `one-time_donation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payouts`
--
ALTER TABLE `payouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sold_courses`
--
ALTER TABLE `sold_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sold_programs`
--
ALTER TABLE `sold_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payouts`
--
ALTER TABLE `payouts`
  ADD CONSTRAINT `payouts_ibfk_1` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
