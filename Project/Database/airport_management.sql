-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 01, 2026 at 02:38 PM
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
-- Database: `airport_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 3, 'User logged in', '2026-08-31 14:10:40'),
(2, 3, 'Added new user', '2026-08-31 14:21:04'),
(3, 3, 'Added new user', '2026-08-31 14:23:02'),
(4, 3, 'Added new user', '2026-08-31 14:25:15'),
(5, 3, 'Added new user', '2026-08-31 14:36:07'),
(6, 3, 'Added new user', '2026-08-31 14:36:48'),
(7, 3, 'Added new user', '2026-08-31 14:40:02'),
(8, 3, 'Updated user: esham', '2026-08-31 15:07:02'),
(9, 3, 'Updated user: nadim', '2026-08-31 15:07:10'),
(10, 3, 'Deleted user: ', '2026-08-31 15:07:49'),
(11, 3, 'Deleted user: rasel', '2026-08-31 15:07:54'),
(12, 3, 'Added new user: raha', '2026-08-31 15:10:15'),
(13, 3, 'Added new user: emon', '2026-08-31 15:11:12'),
(14, 3, 'Added new airline: Emirates', '2026-08-31 17:38:57'),
(15, 3, 'Added new airline: Qatar Airways', '2026-08-31 17:40:23'),
(16, 3, 'Updated airline: Emirates', '2026-08-31 17:45:38'),
(17, 3, 'Deleted airline: Qatar Airways', '2026-08-31 17:49:02'),
(18, 3, 'Added airport service: Wifi', '2026-08-31 19:22:52'),
(19, 3, 'User logged in', '2026-08-31 21:30:09'),
(20, 3, 'Added airport service: Lost and Found', '2026-08-31 23:16:59'),
(21, 3, 'Updated airport service:  Free Wifi', '2026-08-31 23:21:50'),
(22, 3, 'Deleted airport service:  Free Wifi', '2026-08-31 23:21:54'),
(23, 3, 'Added airline information: Emirates', '2026-09-01 00:26:32'),
(24, 3, 'Updated airline: Emirates', '2026-09-01 00:26:46'),
(25, 3, 'Approved aircraft request: Boeing 777', '2026-09-01 00:44:38'),
(26, 3, 'Rejected aircraft request: Boeing 777-300ER', '2026-09-01 00:45:39'),
(27, 3, 'Updated airport service: Lost and Found', '2026-09-01 00:52:59'),
(28, 3, 'Updated airline information: Emirates', '2026-09-01 00:53:22'),
(29, 3, 'Added new user: Emirates admin', '2026-09-01 01:02:26'),
(30, 3, 'Added new user: staff', '2026-09-01 01:03:15'),
(31, 20, 'User logged in', '2026-09-01 01:08:31'),
(32, 21, 'User logged in', '2026-09-01 01:08:42'),
(33, 3, 'User logged in', '2026-09-01 01:09:16'),
(34, 3, 'User logged in', '2026-09-01 01:12:26'),
(35, 20, 'User logged in', '2026-09-01 10:05:26'),
(36, 20, 'User logged in', '2026-09-01 10:06:31'),
(37, 20, 'User logged in', '2026-09-01 10:07:57'),
(38, 20, 'User logged in', '2026-09-01 10:09:13'),
(39, 3, 'User logged in', '2026-09-01 10:22:30'),
(40, 20, 'User logged in', '2026-09-01 11:38:49'),
(41, 20, 'User logged in', '2026-09-01 11:40:22'),
(42, 20, 'User logged in', '2026-09-01 11:41:51'),
(43, 3, 'User logged in', '2026-09-01 11:50:08'),
(44, 20, 'User logged in', '2026-09-01 11:54:34'),
(45, 3, 'User logged in', '2026-09-01 11:56:15'),
(46, 3, 'Approved aircraft request: NAD6767', '2026-09-01 12:00:24'),
(47, 3, 'Rejected aircraft request: Boeing 777', '2026-09-01 12:00:30'),
(48, 3, 'Rejected aircraft request: Boeing 777', '2026-09-01 12:00:35'),
(49, 3, 'Rejected aircraft request: Boeing 777', '2026-09-01 12:00:40'),
(50, 3, 'Rejected aircraft request: Boeing 777', '2026-09-01 12:01:08'),
(51, 3, 'Rejected aircraft request: Boeing 777', '2026-09-01 12:02:53'),
(52, 20, 'User logged in', '2026-09-01 12:09:32'),
(53, 3, 'User logged in', '2026-09-01 12:10:34'),
(54, 3, 'Approved aircraft request: PO-9898', '2026-09-01 12:10:40'),
(55, 20, 'User logged in', '2026-09-01 12:12:15'),
(56, 3, 'User logged in', '2026-09-01 12:16:46'),
(57, 3, 'Rejected aircraft request: ', '2026-09-01 12:16:58'),
(58, 20, 'User logged in', '2026-09-01 12:17:39'),
(59, 3, 'User logged in', '2026-09-01 12:21:11'),
(60, 3, 'Rejected aircraft request: ', '2026-09-01 12:21:16'),
(61, 20, 'User logged in', '2026-09-01 12:21:26'),
(62, 3, 'User logged in', '2026-09-01 12:27:51'),
(63, 3, 'Rejected aircraft request: ', '2026-09-01 12:27:58'),
(64, 20, 'User logged in', '2026-09-01 12:28:07'),
(65, 3, 'User logged in', '2026-09-01 12:33:12'),
(66, 3, 'Rejected aircraft request: NA1009', '2026-09-01 12:33:24'),
(67, 20, 'User logged in', '2026-09-01 12:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `aircraft_approval_requests`
--

CREATE TABLE `aircraft_approval_requests` (
  `id` int(11) NOT NULL,
  `aircraft_id` int(11) NOT NULL,
  `airline_id` int(11) NOT NULL,
  `request_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `proposed_model` varchar(100) DEFAULT NULL,
  `proposed_capacity` int(11) DEFAULT NULL,
  `proposed_manufacturer` varchar(100) DEFAULT NULL,
  `proposed_manufacturing_date` date DEFAULT NULL,
  `supporting_document` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `feedback` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aircraft_approval_requests`
--

INSERT INTO `aircraft_approval_requests` (`id`, `aircraft_id`, `airline_id`, `request_type`, `description`, `proposed_model`, `proposed_capacity`, `proposed_manufacturer`, `proposed_manufacturing_date`, `supporting_document`, `status`, `feedback`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(1, 4, 1, 'new_aircraft', 'New aircraft submitted for airport authority approval.', 'NA-404', 398, 'NA', '2024-02-13', NULL, 'Rejected', NULL, NULL, NULL, '2026-09-01 11:49:40'),
(2, 5, 1, 'new_aircraft', 'New aircraft submitted for airport authority approval.', 'NAD6767', 430, 'NAD', '2023-03-16', NULL, 'Approved', NULL, NULL, NULL, '2026-09-01 11:55:22'),
(3, 6, 1, 'new_aircraft', 'New aircraft submitted for airport authority approval.', 'PO-9898', 290, 'PO', '2024-07-25', NULL, 'Approved', NULL, NULL, NULL, '2026-09-01 12:10:07'),
(4, 7, 1, 'new_aircraft', 'New aircraft submitted for airport authority approval.', 'NA-8787', 190, 'NA', '2021-02-11', NULL, 'Rejected', NULL, NULL, NULL, '2026-09-01 12:16:28'),
(5, 8, 1, 'new_aircraft', 'New aircraft submitted for airport authority approval.', 'NA4433', 390, 'NA', '2024-03-01', NULL, 'Rejected', NULL, NULL, NULL, '2026-09-01 12:21:00'),
(6, 9, 1, 'new_aircraft', 'New aircraft submitted for airport authority approval.', 'NA6666', 180, 'NA', '2025-02-01', NULL, 'Rejected', NULL, NULL, NULL, '2026-09-01 12:27:43'),
(7, 10, 1, 'new_aircraft', 'New aircraft submitted for airport authority approval.', 'NA1009', 160, 'NA', '2025-02-01', NULL, 'Rejected', NULL, NULL, NULL, '2026-09-01 12:33:01');

-- --------------------------------------------------------

--
-- Table structure for table `airlines`
--

CREATE TABLE `airlines` (
  `id` int(11) NOT NULL,
  `airline_name` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airlines`
--

INSERT INTO `airlines` (`id`, `airline_name`, `country`, `contact`, `email`, `status`, `created_at`) VALUES
(1, 'Emirates', 'UAE', '88775', 'emirates@gmail.com', 'Active', '2026-09-01 00:26:32');

-- --------------------------------------------------------

--
-- Table structure for table `airplanes`
--

CREATE TABLE `airplanes` (
  `id` int(11) NOT NULL,
  `airline_id` int(11) DEFAULT NULL,
  `airline_name` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `manufacturing_date` date DEFAULT NULL,
  `registration_number` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airplanes`
--

INSERT INTO `airplanes` (`id`, `airline_id`, `airline_name`, `model`, `manufacturer`, `manufacturing_date`, `registration_number`, `capacity`, `status`, `created_at`) VALUES
(1, 1, 'Emirates', '33401ER', NULL, NULL, 'REG-249', 200, 'Active', '2026-08-29 18:54:37'),
(2, 1, 'Emirates', 'A1008', NULL, NULL, 'EM76589', 245, 'Active', '2026-08-31 17:38:57'),
(6, 1, 'Emirates', 'PO-9898', 'PO', '2024-07-25', 'REG654', 290, 'Active', '2026-09-01 12:10:07'),
(10, 1, 'Emirates', 'NA1009', 'NA', '2025-02-01', 'REG443', 160, 'Rejected', '2026-09-01 12:33:01');

-- --------------------------------------------------------

--
-- Table structure for table `airport_services`
--

CREATE TABLE `airport_services` (
  `id` int(11) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airport_services`
--

INSERT INTO `airport_services` (`id`, `service_type`, `service_name`, `description`, `location`, `status`, `created_at`) VALUES
(2, 'Facility', 'Lost and Found', 'you can report here to find your lost product', 'beside Terminal-3', 'Active', '2026-08-31 23:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `baggage`
--

CREATE TABLE `baggage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `baggage_status` varchar(50) DEFAULT 'Checked In',
  `location` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baggage`
--

INSERT INTO `baggage` (`id`, `user_id`, `booking_id`, `baggage_status`, `location`, `updated_at`) VALUES
(1, 4, 1, 'Checked In', 'Terminal 1 Counter (Tag Issued)', '2026-08-30 20:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `seat_number` varchar(20) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `flight_id`, `seat_number`, `booking_date`, `payment_status`) VALUES
(1, 4, 2, '3A, 3B', '2026-08-30 20:51:59', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_alerts`
--

CREATE TABLE `emergency_alerts` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `alert_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `flight_number` varchar(50) NOT NULL,
  `airplane_id` int(11) NOT NULL,
  `departure` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `status` varchar(50) DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `flight_number`, `airplane_id`, `departure`, `destination`, `departure_time`, `arrival_time`, `status`, `created_at`) VALUES
(1, 'BG-310', 1, 'DAC', 'DXM', '2026-08-29 00:54:00', '2026-08-29 02:57:00', 'On Time', '2026-08-29 18:54:37'),
(2, 'HS-32', 1, 'DHA ', 'JED', '2026-08-31 03:30:01', '2026-08-31 03:00:01', 'Scheduled', '2026-08-30 20:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `gates`
--

CREATE TABLE `gates` (
  `id` int(11) NOT NULL,
  `gate_number` varchar(20) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `availability` varchar(50) DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gates`
--

INSERT INTO `gates` (`id`, `gate_number`, `flight_id`, `availability`, `created_at`) VALUES
(1, 'G50', 1, 'Occupied', '2026-08-29 18:54:37');

-- --------------------------------------------------------

--
-- Table structure for table `lost_items`
--

CREATE TABLE `lost_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `lost_location` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Reported',
  `reported_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `status`, `created_at`) VALUES
(1, 4, 'Payment Successful! Ticket confirmed for 2 seat(s): 3A, 3B.', 'Payment', 'Unread', '2026-08-30 20:51:59'),
(2, 4, 'Luggage tag issued for flight HS-32. Baggage is now checked in.', 'Baggage', 'Unread', '2026-08-30 20:52:07');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `work_schedule` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(3, 'Admin', 'admin@gmail.com', '$2y$12$f8mbTrJgFSKf.JtkqgXlo.5RysxRkgIrK7UtIH7kV/pe0ugbRDS3m', 'admin', '2026-08-28 21:36:07'),
(4, 'nadim', 'eshmam@gmail.com', '$2y$10$vXbsAXaVegR0ADMvOyfPy.MFSVEcBZOOPNinITdWyR0ZSv6XTeJ4i', 'passenger', '2026-08-30 20:47:32'),
(6, 'anik', 'anik@gmail.com', '$2y$10$C3gNMHPOjtFlTYDEtQAu4eNyb4XR2EnBa2GxFzh8zgdSTm5XvXaRS', 'passenger', '2026-08-31 14:18:50'),
(9, 'abir', 'abir@gmail.com', '$2y$10$NOjkbicutNpek1qu2xcytu22ooEpGs7D0pso4twxJgOiDlX5Eb/ZG', 'passenger', '2026-08-31 14:21:04'),
(10, 'sharif', 'sharif@gmail.com', '$2y$10$M5PalJ2jAd2sIEeMaCQzr.M9Xq3G9KlIShw7BeTxl74vw3P/u3J8C', 'passenger', '2026-08-31 14:23:02'),
(11, 'rafid', 'rafid@gmail.com', '$2y$10$ymUZTrdevaNxuBKFN2axOOP.rVNYC9DzCDO.ezBzp0ri496v0anQu', 'staff', '2026-08-31 14:23:56'),
(14, 'salam', 'salam@gmail.com', '$2y$10$bEcNoPn/IIxwLTuEHeWhq.DSqHH4.mkVPlQWrSXdig1BDvczfOgGK', 'passenger', '2026-08-31 14:25:15'),
(15, 'rafa', 'rafa@gmail.com', '$2y$10$sbr5Socfm1Ebw/KFvXIurOYoE9wFwXha6O5F23ySCqmZv16TYvWzm', 'passenger', '2026-08-31 14:36:07'),
(18, 'raha', 'raha@gmail.com', '$2y$10$zRYdPNYcQu7woiRSCfzcw.bEhKieLfpxhj1d/hBbu6d3JksEVcYCy', 'passenger', '2026-08-31 15:10:15'),
(19, 'emon', 'emon@gmail.com', '$2y$10$rfDIApMskty6l81mmXDnPOkoimGejStxRdT4w9IcF.PwuCUKfH.ta', 'passenger', '2026-08-31 15:11:12'),
(20, 'Emirates admin', 'emirates@gmail.com', '$2y$10$drhnkYXWSSud79W7J8r9gOWk0xPhv9wXBuv//JdbOirABdLcHSgky', 'airline', '2026-09-01 01:02:26'),
(21, 'staff', 'staff@gmail.com', '$2y$10$u4/7Wg5mnvbgTGr3thympO5LnYy0aJuvRR/MA8fFAipfI.DPlehBG', 'staff', '2026-09-01 01:03:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aircraft_approval_requests`
--
ALTER TABLE `aircraft_approval_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_approval_aircraft_id` (`aircraft_id`),
  ADD KEY `idx_approval_airline_id` (`airline_id`),
  ADD KEY `idx_approval_status` (`status`),
  ADD KEY `idx_approval_reviewer` (`reviewed_by`);

--
-- Indexes for table `airlines`
--
ALTER TABLE `airlines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `airplanes`
--
ALTER TABLE `airplanes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`);

--
-- Indexes for table `airport_services`
--
ALTER TABLE `airport_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `baggage`
--
ALTER TABLE `baggage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `flight_id` (`flight_id`);

--
-- Indexes for table `emergency_alerts`
--
ALTER TABLE `emergency_alerts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `flight_number` (`flight_number`),
  ADD KEY `airplane_id` (`airplane_id`);

--
-- Indexes for table `gates`
--
ALTER TABLE `gates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flight_id` (`flight_id`);

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `aircraft_approval_requests`
--
ALTER TABLE `aircraft_approval_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `airlines`
--
ALTER TABLE `airlines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `airplanes`
--
ALTER TABLE `airplanes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `airport_services`
--
ALTER TABLE `airport_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `baggage`
--
ALTER TABLE `baggage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emergency_alerts`
--
ALTER TABLE `emergency_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gates`
--
ALTER TABLE `gates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `baggage`
--
ALTER TABLE `baggage`
  ADD CONSTRAINT `baggage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `baggage_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`);

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `flights_ibfk_1` FOREIGN KEY (`airplane_id`) REFERENCES `airplanes` (`id`);

--
-- Constraints for table `gates`
--
ALTER TABLE `gates`
  ADD CONSTRAINT `gates_ibfk_1` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`);

--
-- Constraints for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD CONSTRAINT `lost_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
