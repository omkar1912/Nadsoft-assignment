-- Business Listing & Rating System Database
-- Created for machine test evaluation

CREATE DATABASE IF NOT EXISTS `business_listing` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `business_listing`;

-- --------------------------------------------------------
-- Table structure for `businesses`
-- --------------------------------------------------------

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `ratings`
-- --------------------------------------------------------

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `rating` decimal(2,1) NOT NULL DEFAULT '0.0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `business_id` (`business_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Sample data for `businesses`
-- --------------------------------------------------------

INSERT INTO `businesses` (`id`, `name`, `address`, `phone`, `email`, `created_at`) VALUES
(1, 'Tech Solutions Inc', '123 Main Street, New York, NY 10001', '+1-555-0101', 'info@techsolutions.com', NOW()),
(2, 'Green Valley Restaurant', '456 Oak Avenue, Los Angeles, CA 90001', '+1-555-0102', 'contact@greenvalley.com', NOW()),
(3, 'Digital Marketing Pro', '789 Pine Road, Chicago, IL 60601', '+1-555-0103', 'hello@digitalmarketingpro.com', NOW()),
(4, 'Auto Repair Center', '321 Elm Street, Houston, TX 77001', '+1-555-0104', 'service@autorepair.com', NOW()),
(5, 'Sunrise Fitness Gym', '654 Maple Drive, Phoenix, AZ 85001', '+1-555-0105', 'info@sunrisefitness.com', NOW());

-- --------------------------------------------------------
-- Sample data for `ratings`
-- --------------------------------------------------------

INSERT INTO `ratings` (`business_id`, `name`, `email`, `phone`, `rating`, `created_at`) VALUES
(1, 'John Doe', 'john@example.com', '+1-555-1001', 4.5, NOW()),
(1, 'Jane Smith', 'jane@example.com', '+1-555-1002', 3.5, NOW()),
(2, 'Mike Johnson', 'mike@example.com', '+1-555-1003', 5.0, NOW()),
(2, 'Sarah Wilson', 'sarah@example.com', '+1-555-1004', 4.0, NOW()),
(3, 'David Brown', 'david@example.com', '+1-555-1005', 3.0, NOW()),
(4, 'Lisa Davis', 'lisa@example.com', '+1-555-1006', 4.5, NOW()),
(5, 'Tom Anderson', 'tom@example.com', '+1-555-1007', 2.5, NOW());
