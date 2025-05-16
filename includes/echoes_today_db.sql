-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 10:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `echoes_today_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `ad_type` enum('banner','sidebar','popup','in-article') NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `redirect_url` varchar(255) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `impressions` int(11) DEFAULT 0,
  `clicks` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`id`, `name`, `ad_type`, `image_path`, `redirect_url`, `width`, `height`, `is_active`, `start_date`, `end_date`, `impressions`, `clicks`, `created_at`, `updated_at`) VALUES
(1, 'Summer Sale Banner', 'banner', 'images/ads/summer-sale.jpg', 'https://example.com/summer', 728, 90, 1, '2025-05-01', '2025-08-31', 5642, 128, '2025-05-16 08:01:46', '2025-05-16 08:01:46'),
(2, 'Tech Store Sidebar', 'sidebar', 'images/ads/tech-store.jpg', 'https://example.com/tech', 300, 600, 1, '2025-04-15', '2025-07-15', 3218, 89, '2025-05-16 08:01:46', '2025-05-16 08:01:46'),
(3, 'Travel Deal Popup', 'popup', 'images/ads/travel-deal.jpg', 'https://example.com/travel', 500, 500, 0, '2025-06-01', '2025-06-30', 1500, 45, '2025-05-16 08:01:46', '2025-05-16 08:01:46');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('draft','pending_review','published','rejected') DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `author_id`, `category_id`, `status`, `is_featured`, `view_count`, `published_at`, `created_at`, `updated_at`) VALUES
(7, 'New Healthcare Bill Passes Senate Vote', 'healthcare-bill-passes-senate', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ac magna vel urna efficitur efficitur.', 'Senate approves controversial healthcare legislation with narrow margin', 'images/articles/healthcare-bill.jpg', 2, 1, 'published', 1, 1245, '2025-05-16 08:03:53', '2025-05-16 08:03:53', '2025-05-16 08:03:53'),
(8, 'Local Team Wins Championship', 'local-team-championship-win', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ac magna vel urna efficitur efficitur.', 'Underdogs triumph in final match of the season', 'images/articles/championship.jpg', 3, 2, 'published', 0, 867, '2025-05-16 08:03:53', '2025-05-16 08:03:53', '2025-05-16 08:03:53'),
(9, 'Summer Blockbuster Breaks Box Office Records', 'summer-blockbuster-records', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ac magna vel urna efficitur efficitur.', 'New action film exceeds expectations with $500M opening weekend', 'images/articles/blockbuster.jpg', 2, 3, 'pending_review', 0, 0, NULL, '2025-05-16 08:03:53', '2025-05-16 08:03:53');

-- --------------------------------------------------------

--
-- Table structure for table `article_revisions`
--

CREATE TABLE `article_revisions` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `editor_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `revision_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article_tags`
--

INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
(7, 1),
(8, 2),
(9, 3);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Politics', 'politics', 'Latest political news and analysis', 1, 1, '2025-05-16 08:00:51', '2025-05-16 08:00:51'),
(2, 'Sports', 'sports', 'Coverage of local and international sporting events', 1, 2, '2025-05-16 08:00:51', '2025-05-16 08:00:51'),
(3, 'Entertainment', 'entertainment', 'Celebrity news, movie reviews, and cultural events', 1, 3, '2025-05-16 08:00:51', '2025-05-16 08:00:51');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `created_at`) VALUES
(1, 'COVID-19', '2025-05-16 08:01:02'),
(2, 'Election', '2025-05-16 08:01:02'),
(3, 'Olympics', '2025-05-16 08:01:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','journalist') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `first_name`, `last_name`, `bio`, `profile_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$KIsMVbkMUg1v8Wc3xIW9Mu/7CE5inJ0nH3UzdECxmH2KEZ3KIwtIe', 'admin@echoestoday.com', 'admin', 'John', 'Admin', 'Chief administrator of the newspaper', NULL, 'active', '2025-05-16 08:03:38', '2025-05-16 08:03:38'),
(2, 'sarahsmith', '$2y$10$ZA4ukQ7ISDaGf.zuUjBV2emJzTghoVEl7gQpXUUeKbdQ.LarK9deW', 'sarah@echoestoday.com', 'journalist', 'Sarah', 'Smith', 'Senior political correspondent with 10 years experience', NULL, 'active', '2025-05-16 08:03:38', '2025-05-16 08:03:38'),
(3, 'mikejones', '$2y$10$cuTMiNy7yuQr3WXRl4OYTux2rnELAa6Xr9Io9wLVUlc4vLJDlyWae', 'mike@echoestoday.com', 'journalist', 'Mike', 'Jones', 'Sports journalist covering major leagues', NULL, 'active', '2025-05-16 08:03:38', '2025-05-16 08:03:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `article_revisions`
--
ALTER TABLE `article_revisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `editor_id` (`editor_id`);

--
-- Indexes for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `article_revisions`
--
ALTER TABLE `article_revisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `article_revisions`
--
ALTER TABLE `article_revisions`
  ADD CONSTRAINT `article_revisions_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_revisions_ibfk_2` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
