-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2024 at 01:15 PM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
