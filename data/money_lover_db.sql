-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2015 at 03:33 AM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `money_lover_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `wallet_id` int(11) DEFAULT NULL,
  `type_id` int(1) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `is_locked` int(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `wallet_id`, `type_id`, `parent`, `is_locked`, `created`, `modified`, `deleted`, `status`) VALUES
(177, 'Difference', 63, 1, NULL, 1, '2015-08-14 03:27:26', '2015-08-14 04:04:21', NULL, 0),
(178, 'Received', 63, 1, NULL, 1, '2015-08-14 03:27:26', '2015-08-14 04:04:21', NULL, 0),
(179, 'Difference', 63, 2, NULL, 1, '2015-08-14 03:27:26', '2015-08-14 04:04:21', NULL, 0),
(180, 'Loan', 63, 2, NULL, 1, '2015-08-14 03:27:26', '2015-08-14 04:04:21', NULL, 0),
(181, 'Debt', 63, 1, NULL, 1, '2015-08-14 03:27:26', '2015-08-14 04:04:21', NULL, 0),
(182, 'Giai tri', 63, 1, 0, 0, '2015-08-14 03:59:43', '2015-08-14 04:04:21', NULL, 0),
(183, 'Difference', 64, 1, NULL, 1, '2015-08-14 04:10:38', '2015-08-14 04:10:38', NULL, 1),
(184, 'Received', 64, 1, NULL, 1, '2015-08-14 04:10:38', '2015-08-14 04:10:38', NULL, 1),
(185, 'Difference', 64, 2, NULL, 1, '2015-08-14 04:10:38', '2015-08-14 04:10:38', NULL, 1),
(186, 'Loan', 64, 2, NULL, 1, '2015-08-14 04:10:38', '2015-08-14 04:10:38', NULL, 1),
(187, 'Debt', 64, 1, NULL, 1, '2015-08-14 04:10:39', '2015-08-14 04:10:39', NULL, 1),
(188, 'Difference', 65, 1, NULL, 1, '2015-08-14 04:10:56', '2015-08-14 04:10:56', NULL, 1),
(189, 'Received', 65, 1, NULL, 1, '2015-08-14 04:10:56', '2015-08-14 04:10:56', NULL, 1),
(190, 'Difference', 65, 2, NULL, 1, '2015-08-14 04:10:56', '2015-08-14 04:10:56', NULL, 1),
(191, 'Loan', 65, 2, NULL, 1, '2015-08-14 04:10:56', '2015-08-14 04:10:56', NULL, 1),
(192, 'Debt', 65, 1, NULL, 1, '2015-08-14 04:10:56', '2015-08-14 04:10:56', NULL, 1),
(193, 'Difference', 66, 1, NULL, 1, '2015-08-14 11:00:26', '2015-08-14 11:00:26', NULL, 1),
(194, 'Received', 66, 1, NULL, 1, '2015-08-14 11:00:26', '2015-08-14 11:00:26', NULL, 1),
(195, 'Difference', 66, 2, NULL, 1, '2015-08-14 11:00:26', '2015-08-14 11:00:26', NULL, 1),
(196, 'Loan', 66, 2, NULL, 1, '2015-08-14 11:00:26', '2015-08-14 11:00:26', NULL, 1),
(197, 'Debt', 66, 1, NULL, 1, '2015-08-14 11:00:26', '2015-08-14 11:00:26', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `amount` float NOT NULL,
  `note` text,
  `parent` int(11) DEFAULT NULL,
  `done_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `status` int(1) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `wallet_id`, `category_id`, `title`, `amount`, `note`, `parent`, `done_date`, `created`, `modified`, `deleted`, `status`) VALUES
(15, 63, 182, '123123', 123123, '', NULL, '2015-08-14 04:02:00', '2015-08-14 04:02:52', '2015-08-14 04:02:52', NULL, 1),
(16, 64, 185, 'Transfer Money', 123123, 'Transfer money to olala', NULL, '2015-08-14 04:23:41', '2015-08-14 04:23:41', '2015-08-14 04:23:41', NULL, 1),
(17, 64, 196, 'Transfer Money', 10000, 'Received from olala', NULL, '2015-08-14 04:23:00', '2015-08-14 04:23:41', '2015-08-17 10:50:03', NULL, 1),
(18, 64, 185, 'Transfer Money', 123, 'Transfer money to olala', NULL, '2015-08-14 04:23:52', '2015-08-14 04:23:52', '2015-08-14 04:23:52', NULL, 1),
(19, 64, 184, 'Transfer Money', 123, 'Received from olala', NULL, '2015-08-14 04:23:52', '2015-08-14 04:23:52', '2015-08-14 04:23:52', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(1) NOT NULL,
  `title` varchar(20) NOT NULL,
  `status` int(1) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `title`, `status`) VALUES
(1, 'income', NULL),
(2, 'expense', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `total_balance` float NOT NULL DEFAULT '0',
  `is_actived` int(1) NOT NULL DEFAULT '0',
  `token` varchar(255) DEFAULT NULL,
  `last_wallet` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `status` int(1) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `total_balance`, `is_actived`, `token`, `last_wallet`, `created`, `modified`, `deleted`, `status`) VALUES
(57, 'thanhnt07.vn@gmail.com', '$2y$10$0veQ/cqcBCzXvACgvl8YwOOOa40zjG5ZXaLsYP4YswGAwq8swM4H.', 1354350000, 1, '867d341c35f74aa2495a9bc40d60fc30f224607c', 64, '2015-08-14 02:57:33', '2015-08-18 01:02:59', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE IF NOT EXISTS `wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `init_balance` float NOT NULL,
  `current_balance` float NOT NULL DEFAULT '0',
  `is_current` int(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `status` int(1) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `title`, `init_balance`, `current_balance`, `is_current`, `created`, `modified`, `deleted`, `status`) VALUES
(63, 57, 'Vi 1', 10000000, 10123100, 1, '2015-08-14 03:27:26', '2015-08-14 04:04:21', NULL, 0),
(64, 57, 'olala', 123123000, 123246000, 1, '2015-08-14 04:10:38', '2015-08-18 01:02:59', NULL, 1),
(65, 57, 'okaka', 1000000000000, 1000000000000, 0, '2015-08-14 04:10:56', '2015-08-18 01:02:59', NULL, 1),
(66, 57, 'Chan', 123123, 123123, 0, '2015-08-14 11:00:26', '2015-08-17 07:31:11', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`), ADD KEY `wallet_key` (`wallet_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`), ADD KEY `category_key` (`category_id`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`), ADD KEY `user_key` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=198;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=67;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
