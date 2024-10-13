-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 12:53 AM
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
-- Database: `bakery_oop`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`) VALUES
(1, 'Jane Smith', 'jane@example.com', 'I love your cakes!'),
(2, 'brian', 'brian@gmail.com', 'yawa'),
(3, 'brian', 'brian@gmail.com', 'yawa'),
(4, 'brian', 'brian@gmail.com', 'yawa'),
(5, 'brian', 'brian@gmail.com', 'yawa'),
(6, 'brian', 'brian@gmail.com', 'yawa'),
(7, 'brian', 'brian@gmail.com', 'yawa'),
(8, 'brian', 'brian@gmail.com', 'yawa'),
(9, 'brian', 'brian@gmail.com', 'yawa');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_type` enum('delivery','pickup') NOT NULL DEFAULT 'pickup',
  `delivery_address` text DEFAULT NULL,
  `pickup_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_email`, `product_id`, `quantity`, `total_price`, `payment_method`, `address`, `status`, `order_date`, `order_type`, `delivery_address`, `pickup_time`) VALUES
(63, 21, NULL, 0, 0, 299.00, 'credit_card', 'yipee', 'processing', '2024-09-28 15:38:49', 'pickup', NULL, NULL),
(64, 21, NULL, 0, 0, 46000.00, 'credit_card', 'yumm', 'pending', '2024-09-28 23:30:22', 'pickup', NULL, NULL),
(65, 21, NULL, 0, 0, 7130.00, 'cod', '310', 'pending', '2024-09-28 23:30:52', 'pickup', NULL, NULL),
(89, 21, 'brian@gmail.com', 0, 0, 23.00, 'credit_card', '', 'pending', '2024-10-10 11:49:35', 'delivery', 'yippe', '0000-00-00 00:00:00'),
(90, 21, 'brian@gmail.com', 0, 0, 23.00, 'credit_card', '', 'pending', '2024-10-10 11:49:53', 'delivery', 'yippe', '0000-00-00 00:00:00'),
(91, 21, 'brian@gmail.com', 0, 0, 23.00, 'credit_card', '', 'pending', '2024-10-10 11:50:00', 'delivery', '23', '0000-00-00 00:00:00'),
(92, 21, 'brian@gmail.com', 0, 0, 46.00, 'credit_card', '', 'pending', '2024-10-10 11:50:49', 'delivery', 'enhancing', '0000-00-00 00:00:00'),
(93, 21, 'brian@gmail.com', 0, 0, 69.00, 'credit_card', '', 'pending', '2024-10-10 11:51:59', 'delivery', '23', '0000-00-00 00:00:00'),
(94, 21, 'brian@gmail.com', 0, 0, 184.00, 'credit_card', '', 'pending', '2024-10-10 11:52:32', 'delivery', 'qw', '0000-00-00 00:00:00'),
(95, 21, 'brian@gmail.com', 0, 0, 253.00, 'credit_card', '', 'pending', '2024-10-10 11:53:39', 'delivery', 'yipppee', '0000-00-00 00:00:00'),
(96, 21, 'brian@gmail.com', 0, 0, 253.00, 'credit_card', '', 'pending', '2024-10-10 11:54:10', 'delivery', 'yay', '0000-00-00 00:00:00'),
(97, 21, 'brian@gmail.com', 0, 0, 253.00, 'credit_card', '', 'pending', '2024-10-10 11:55:50', 'delivery', 'yipee', '0000-00-00 00:00:00'),
(103, 21, 'brian@gmail.com', 0, 0, 23.00, 'cod', '', 'shipped', '2024-10-11 09:33:13', 'pickup', '', '2024-10-18 17:32:00'),
(104, 21, 'brian@gmail.com', 0, 0, 1150.00, 'credit_card', '', 'delivered', '2024-10-11 11:05:00', 'delivery', 'yippe', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(64, 103, 132, 1),
(65, 104, 133, 50);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category_id`, `featured`, `quantity`) VALUES
(131, '23', '23', 23.00, 'cakey.jpg', NULL, 0, 0),
(132, '23', '23', 23.00, 'brian_ragasi.jpg', NULL, 0, 1),
(133, 'brian', 'yippe', 23.00, '1.png', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `review`, `approved`, `review_date`) VALUES
(3, 21, 131, 1, 'KALAMI!', 0, '2024-10-01 14:24:14'),
(4, 21, 131, 3, 'yum', 0, '2024-10-04 11:11:39'),
(5, 21, 131, 2, '23', 0, '2024-10-04 11:12:24'),
(6, 21, 131, 2, 'yu', 0, '2024-10-10 10:05:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `loyalty_points` int(11) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `isAdmin` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `loyalty_points`, `reset_token`, `isAdmin`) VALUES
(6, 'admin', 'admin@gmail.com', '$2y$10$yf3pCYzsjeIOaUVHYG3g4ey.ujdz0DZGZ4ycFwesMkB6bJHOqx46W', 0, NULL, 1),
(21, 'brian', 'brian@gmail.com', '$2y$10$1aVZpvX2vSYPY4srPms8wOLazGpoeNQZMLRiWgXSlQ9sRia4yWtY.', 55890, NULL, 0),
(22, 'brian', 'ragasibrian2@gmail.com', '$2y$10$C2y4SzyY1ubH4LJNyDFjje0cgDY5G2HHcI41YfyQr5GfgHDUz9LVK', 23, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
