-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2026 at 07:40 PM
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
-- Database: `helpdesk_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `assigned_to` int(11) DEFAULT NULL,
  `category` enum('Hardware','Software','Network','Email','Printer','Other') DEFAULT 'Other'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `title`, `description`, `status`, `created_at`, `priority`, `assigned_to`, `category`) VALUES
(7, 6, 'booking ', 'no rooms available right now\r\n', 'Resolved', '2026-03-31 06:19:22', 'Medium', NULL, 'Other'),
(8, 7, 'NETWORK', 'the connection to the printers is denied', 'Resolved', '2026-04-01 04:22:23', 'Medium', 5, 'Other'),
(9, 7, 'INTERNET', 'The wifi connection has problem it connect but cant provide internet acces to our devices,leads to delay of the activities that depend on internet', 'Pending', '2026-04-01 04:30:39', 'Medium', 5, 'Other'),
(10, 6, 'HARDWARE', 'Usb from printer to computer is not working well, when you connect it cant print documents', 'Pending', '2026-04-02 21:05:01', 'Medium', NULL, 'Other'),
(11, 12, 'DESKTOP', 'attacked by viruses ,am not safe with my data available and what am still uploading', 'Resolved', '2026-04-04 20:31:33', 'High', NULL, ''),
(12, 12, 'EMAIL', 'my email cant receive from others also it opens only in some devices', 'Pending', '2026-04-05 10:31:11', 'High', 6, ''),
(13, 12, 'vifaa', 'ethernet za ofisi zimepata changamoto hatupati mtandao ofisin kwetu', 'Pending', '2026-04-13 12:55:15', 'Medium', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_comments`
--

INSERT INTO `ticket_comments` (`id`, `ticket_id`, `user_id`, `message`, `created_at`) VALUES
(1, 9, 5, 'hoo', '2026-04-01 05:40:47'),
(2, 7, 6, 'nachoka mimi', '2026-04-01 05:41:50'),
(3, 7, 5, 'tatizo nini ndugu', '2026-04-01 05:45:12'),
(4, 7, 6, 'tunashugulikia tatizo lako', '2026-04-01 07:02:51'),
(5, 7, 5, 'hello kiongozi👋', '2026-04-02 15:47:48'),
(6, 11, 12, 'habari \r\n', '2026-04-04 20:32:23'),
(7, 12, 6, 'HEY I HAVE SOME QUESTIONS ON YOUR PROBLEM', '2026-04-05 10:35:27'),
(8, 12, 12, 'just ask\r\n', '2026-04-05 10:36:05'),
(9, 12, 6, 'since when the problem exist?', '2026-04-07 21:01:19'),
(10, 12, 12, 'a week ago\r\n', '2026-04-07 21:02:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone_number`, `reset_token`, `reset_expires`, `remember_token`) VALUES
(5, 'musa', 'musa.simime23@mustudent.ac.tz', '12345', 'admin', '0710491818', NULL, NULL, NULL),
(6, 'sese', 'sese@gmail.com', '12345', 'tech', NULL, NULL, NULL, NULL),
(12, 'mariam', 'mariam@gmail.com', '12345', 'user', '0714252627', NULL, NULL, NULL),
(13, 'nuhu', 'nuhu@gmail.com', '12345', 'user', NULL, NULL, NULL, NULL),
(14, 'shaban', 'shaban@gmail.com', '12345', 'user', '0654321567', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD CONSTRAINT `ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  ADD CONSTRAINT `ticket_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
