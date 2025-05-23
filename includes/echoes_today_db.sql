-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 11:08 AM
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
  `trending` tinyint(1) DEFAULT 0,
  `youtube_link` varchar(255) DEFAULT NULL,
  `has_video` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tags` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `content`, `excerpt`, `featured_image`, `author_id`, `category_id`, `status`, `is_featured`, `trending`, `youtube_link`, `has_video`, `view_count`, `published_at`, `created_at`, `updated_at`, `tags`) VALUES
(7, 'New Healthcare Bill Passes Senate Vote', 'healthcare-bill-passes-senate', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ac magna vel urna efficitur efficitur.', 'Senate approves controversial healthcare legislation with narrow margin', 'images/articles/healthcare-bill.jpg', 2, 1, 'published', 1, 0, NULL, 0, 1245, '2025-05-16 08:03:53', '2025-05-16 08:03:53', '2025-05-16 08:03:53', NULL),
(8, 'Local Team Wins Championship', 'local-team-championship-win', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ac magna vel urna efficitur efficitur.', 'Underdogs triumph in final match of the season', 'images/articles/championship.jpg', 3, 2, 'published', 0, 0, NULL, 0, 400000, '2025-05-16 08:03:53', '2025-05-16 08:03:53', '2025-05-16 08:36:48', NULL),
(9, 'Summer Blockbuster Breaks Box Office Records', 'summer-blockbuster-records', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ac magna vel urna efficitur efficitur.', 'New action film exceeds expectations with $500M opening weekend', 'images/articles/blockbuster.jpg', 2, 3, 'pending_review', 0, 0, NULL, 0, 0, NULL, '2025-05-16 08:03:53', '2025-05-16 08:03:53', NULL),
(42, 'Breakthrough in Quantum Computing Achieved', 'quantum-computing-breakthrough', 'Scientists have achieved a major breakthrough in quantum computing technology that could revolutionize data processing capabilities. The new quantum processor demonstrated unprecedented stability and error correction rates.', 'Research team demonstrates stable qubit operation opening doors for practical quantum applications', 'images/articles/quantum-computing.jpg', 2, 7, 'published', 1, 0, NULL, 0, 3422, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(43, 'New Smartphone Launch Breaks Pre-order Records', 'smartphone-launch-records', 'The latest flagship smartphone has broken all previous pre-order records within the first 24 hours of announcement. Experts attribute this success to the revolutionary camera system and extended battery life.', 'Tech giant sees unprecedented demand for latest device with advanced AI features', 'images/articles/new-smartphone.jpg', 3, 7, 'published', 0, 0, NULL, 0, 1576, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(44, 'New Study Links Gut Health to Improved Immunity', 'gut-health-immunity-study', 'A groundbreaking study published in a leading medical journal has established a clear correlation between gut microbiome diversity and enhanced immune response in adults of all ages.', 'Research confirms importance of dietary diversity for stronger immune system', 'images/articles/gut-health.jpg', 2, 8, 'published', 0, 0, NULL, 0, 945, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(45, 'Revolutionary Cancer Treatment Shows Promise in Clinical Trials', 'cancer-treatment-trials', 'A novel approach to treating aggressive forms of cancer has shown remarkable results in phase III clinical trials, with over 70% of participants showing significant tumor reduction within weeks.', 'Targeted therapy could change standard of care for previously untreatable cases', 'images/articles/cancer-treatment.jpg', 3, 8, 'published', 0, 0, NULL, 0, 1865, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(46, 'Global Market Volatility Following Central Bank Announcement', 'market-volatility-central-bank', 'Markets worldwide experienced significant fluctuations after the unexpected interest rate decision announced yesterday. Analysts are divided on the long-term implications for economic growth.', 'Investors scramble to adjust portfolios amid uncertainty in financial markets', 'images/articles/market-volatility.jpg', 2, 9, 'published', 0, 0, NULL, 0, 1254, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(47, 'Tech Merger Creates New Industry Giant', 'tech-merger-industry-giant', 'Two leading technology companies have announced a $45 billion merger agreement, creating what analysts are calling a new behemoth in the cloud computing and AI sectors.', 'Historic deal reshapes competitive landscape in multiple technology markets', 'images/articles/tech-merger.jpg', 1, 9, 'published', 1, 0, NULL, 0, 2567, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(48, 'Mars Rover Makes Surprising Discovery', 'mars-rover-discovery', 'The latest Mars exploration mission has transmitted data suggesting the presence of complex organic compounds just below the planet\'s surface, raising new questions about the possibility of ancient microbial life.', 'NASA scientists analyzing unexpected findings from latest soil samples', 'images/articles/mars-rover.jpg', 3, 10, 'published', 0, 0, NULL, 0, 3120, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(49, 'Climate Scientists Develop Improved Prediction Models', 'climate-prediction-models', 'A team of international researchers has unveiled a new generation of climate prediction models with significantly enhanced accuracy for regional forecasting, particularly for extreme weather events.', 'Advanced AI integration allows for more precise local climate projections', 'images/articles/climate-models.jpg', 2, 10, 'published', 0, 0, NULL, 0, 876, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(50, 'International Summit Addresses Global Supply Chain Crisis', 'global-supply-chain-summit', 'Leaders from over 40 countries gathered to address ongoing disruptions in global supply chains, proposing a coordinated framework for building more resilient international trade systems.', 'Nations agree on multilateral approach to prevent future logistics breakdowns', 'images/articles/supply-chain-summit.jpg', 1, 11, 'published', 0, 0, NULL, 0, 1432, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(51, 'Diplomatic Breakthrough in Regional Conflict Negotiations', 'diplomatic-breakthrough', 'After months of stalled talks, negotiators have announced a significant breakthrough in the peace process, with both sides agreeing to a framework for addressing longstanding territorial disputes.', 'Ceasefire agreement brings hope for lasting stability in conflict zone', 'images/articles/diplomacy.jpg', 2, 11, 'published', 1, 0, NULL, 0, 1987, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(52, 'Sustainable Fashion Takes Center Stage at Annual Design Awards', 'sustainable-fashion-awards', 'This year\'s prestigious design awards highlighted a dramatic shift toward sustainable practices, with recycled materials and ethical production processes dominating the winning collections.', 'Industry embraces eco-friendly innovation as consumers demand accountability', 'images/articles/sustainable-fashion.jpg', 3, 12, 'published', 0, 0, NULL, 0, 754, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(53, 'Remote Work Revolution Reshaping Urban Living Trends', 'remote-work-urban-living', 'The widespread adoption of flexible work arrangements is driving significant changes in housing preferences, with many urbanites seeking homes with dedicated office spaces or relocating to smaller communities.', 'Real estate experts note shift in priorities for post-pandemic home buyers', 'images/articles/remote-work.jpg', 2, 12, 'published', 0, 0, NULL, 0, 1098, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(54, 'Election Polls Show Surprising Shift in Key Demographics', 'election-polls-demographic-shift', 'Recent polling data reveals unexpected changes in voting patterns among several key demographics that could significantly impact the upcoming election results in numerous swing districts.', 'Analysts scrambling to explain voter preference changes in traditionally consistent groups', 'images/articles/election-polls.jpg', 2, 1, 'published', 0, 0, NULL, 0, 1876, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(55, 'New Environmental Regulation Framework Announced', 'environmental-regulation-framework', 'The government has unveiled a comprehensive set of environmental regulations aimed at reducing carbon emissions by 35% within the next decade while promoting sustainable industrial practices.', 'Business leaders and environmental groups offer mixed reactions to ambitious new policies', 'images/articles/environment-policy.jpg', 1, 1, 'published', 0, 0, NULL, 0, 1350, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(56, 'Underdog Team Advances to Semi-Finals in Dramatic Fashion', 'underdog-team-semifinals', 'In what sports commentators are calling one of the biggest upsets of the season, the underdog team secured their place in the semi-finals with a last-minute victory against the tournament favorites.', 'Last-second goal caps remarkable comeback in quarter-final thriller', 'images/articles/underdog-victory.jpg', 3, 2, 'published', 0, 0, NULL, 0, 2345, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(57, 'Star Athlete Announces Retirement After Legendary Career', 'athlete-retirement-announcement', 'After fifteen remarkable years that included six championships and countless records, the beloved sports icon has announced plans to retire at the end of the current season.', 'Fans and fellow competitors pay tribute to one of the greatest players of a generation', 'images/articles/athlete-retirement.jpg', 3, 2, 'published', 0, 0, NULL, 0, 2890, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(58, 'Award-Winning Director Announces Ambitious New Project', 'director-new-project', 'The acclaimed filmmaker has revealed details about an upcoming production that will push technological boundaries while exploring complex social themes in a post-pandemic world.', 'Industry insiders predict groundbreaking visual techniques and narrative innovation', 'images/articles/new-film-project.jpg', 1, 3, 'published', 0, 0, NULL, 0, 978, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(59, 'Music Festival Returns With Star-Studded Lineup', 'music-festival-returns', 'After a three-year hiatus, the popular music festival has announced its return with an impressive roster of performers spanning multiple genres, from established legends to breakthrough artists.', 'Ticket sales break records as music fans eagerly await the three-day event', 'images/articles/music-festival.jpg', 2, 3, 'published', 0, 0, NULL, 0, 1456, '2025-05-16 09:03:35', '2025-05-16 09:03:35', '2025-05-16 09:03:35', NULL),
(62, 'Albania to Host Balkan Tech Summit 2025', 'albania-to-host-balkan-tech-summit-2025', 'The Ministry of Innovation announced that Tirana will host the Balkan Tech Summit in October 2025. The event aims to boost tech entrepreneurship in the region and provide networking opportunities for startups and investors. Over 50 speakers and 100 companies are expected to participate.\r\n', 'Albania is set to host the largest technology summit in the Balkans, bringing together innovators and startups from across Europe.', 'uploads/1747860516_images.jpeg', 2, 7, 'pending_review', 0, 1, '', 0, 0, NULL, '2025-05-21 20:48:36', '2025-05-21 20:48:36', 'summit, albania, tech, startups');

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
(9, 3),
(42, 9),
(44, 5),
(46, 7),
(48, 8),
(49, 6),
(52, 10);

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
(3, 'Entertainment', 'entertainment', 'Celebrity news, movie reviews, and cultural events', 1, 3, '2025-05-16 08:00:51', '2025-05-16 08:00:51'),
(7, 'Technology', 'technology', 'Latest tech innovations, gadget reviews, and digital trends', 1, 4, '2025-05-16 08:54:09', '2025-05-16 08:54:09'),
(8, 'Health', 'health', 'Medical breakthroughs, wellness tips, and health advisories', 1, 5, '2025-05-16 08:54:09', '2025-05-16 08:54:09'),
(9, 'Business', 'business', 'Financial news, market analysis, and corporate updates', 1, 6, '2025-05-16 08:54:09', '2025-05-16 08:54:09'),
(10, 'Science', 'science', 'Scientific discoveries, research breakthroughs, and space exploration', 1, 7, '2025-05-16 08:54:09', '2025-05-16 08:54:09'),
(11, 'World News', 'world-news', 'Global affairs, international politics, and foreign policy', 1, 8, '2025-05-16 08:54:09', '2025-05-16 08:54:09'),
(12, 'Lifestyle', 'lifestyle', 'Fashion trends, food, travel, and personal well-being', 1, 9, '2025-05-16 08:54:09', '2025-05-16 08:54:09');

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
(3, 'Olympics', '2025-05-16 08:01:02'),
(4, 'AI', '2025-05-16 09:03:49'),
(5, 'Healthcare', '2025-05-16 09:03:49'),
(6, 'Climate Change', '2025-05-16 09:03:49'),
(7, 'Economy', '2025-05-16 09:03:49'),
(8, 'Space Exploration', '2025-05-16 09:03:49'),
(9, 'Innovation', '2025-05-16 09:03:49'),
(10, 'Sustainability', '2025-05-16 09:03:49'),
(11, 'Politics', '2025-05-16 09:03:49'),
(12, 'Sports', '2025-05-16 09:03:49'),
(13, 'Entertainment', '2025-05-16 09:03:49');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `article_revisions`
--
ALTER TABLE `article_revisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
