-- GlowHaus E-Commerce Database Schema
-- Skincare & Beauty Products

SET SQL_MODE = "STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION";

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `status` ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
    `google_id` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `profiles` (
    `user_id` INT PRIMARY KEY,
    `first_name` VARCHAR(100) DEFAULT '',
    `last_name` VARCHAR(100) DEFAULT '',
    `phone` VARCHAR(20) DEFAULT '',
    `country` VARCHAR(100) DEFAULT 'Nigeria',
    `address` TEXT DEFAULT NULL,
    `state` VARCHAR(100) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `zip_code` VARCHAR(20) DEFAULT NULL,
    `profile_image` VARCHAR(500) DEFAULT NULL,
    `billing_same_as_delivery` TINYINT(1) DEFAULT 1,
    `billing_first_name` VARCHAR(100) DEFAULT NULL,
    `billing_last_name` VARCHAR(100) DEFAULT NULL,
    `billing_country` VARCHAR(100) DEFAULT NULL,
    `billing_address` TEXT DEFAULT NULL,
    `billing_state` VARCHAR(100) DEFAULT NULL,
    `billing_city` VARCHAR(100) DEFAULT NULL,
    `billing_zip_code` VARCHAR(20) DEFAULT NULL,
    `billing_phone` VARCHAR(20) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `categories` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_title` VARCHAR(255) NOT NULL,
    `category_description` TEXT DEFAULT NULL,
    `category_image` VARCHAR(500) DEFAULT NULL,
    `category_slug` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`category_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `brands` (
    `brand_id` INT AUTO_INCREMENT PRIMARY KEY,
    `brand_title` VARCHAR(255) NOT NULL,
    `brand_description` TEXT DEFAULT NULL,
    `brand_image` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
    `product_id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_name` VARCHAR(500) NOT NULL,
    `sku` VARCHAR(191) DEFAULT NULL,
    `product_slug` VARCHAR(500) DEFAULT NULL,
    `category_id` INT DEFAULT NULL,
    `brand_id` INT DEFAULT NULL,
    `colors` VARCHAR(500) DEFAULT NULL,
    `details` TEXT DEFAULT NULL,
    `sizes_details` TEXT DEFAULT NULL,
    `warranty` TEXT DEFAULT NULL,
    `care` TEXT DEFAULT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `product_status` ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    `date_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE SET NULL,
    FOREIGN KEY (`brand_id`) REFERENCES `brands`(`brand_id`) ON DELETE SET NULL,
    INDEX `idx_category` (`category_id`),
    INDEX `idx_brand` (`brand_id`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_status` (`product_status`),
    UNIQUE KEY `uq_product_slug` (`product_slug`),
    FULLTEXT INDEX `idx_search` (`product_name`, `details`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_images` (
    `image_id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `is_main` TINYINT(1) DEFAULT 0,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    INDEX `idx_product_main` (`product_id`, `is_main`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_variants` (
    `variant_id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `size` VARCHAR(100) DEFAULT NULL,
    `texture` VARCHAR(100) DEFAULT NULL,
    `quantity` INT DEFAULT 0,
    `original_price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `discount_price` DECIMAL(10,2) DEFAULT NULL,
    `status` ENUM('available', 'outOfStock', 'discontinued') DEFAULT 'available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    INDEX `idx_product` (`product_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cart` (
    `cart_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `variant_id` INT DEFAULT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    FOREIGN KEY (`variant_id`) REFERENCES `product_variants`(`variant_id`) ON DELETE SET NULL,
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_total` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `shipping_fee` DECIMAL(10,2) DEFAULT 0,
    `delivery_method` ENUM('pickup', 'express') DEFAULT 'pickup',
    `pickup_location` VARCHAR(255) DEFAULT NULL,
    `payment_reference` VARCHAR(255) DEFAULT NULL,
    `payment_transaction_id` VARCHAR(255) DEFAULT NULL,
    `delivery_email` VARCHAR(255) DEFAULT NULL,
    `order_note` TEXT DEFAULT NULL,
    `order_status` ENUM('Processing', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled', 'Returned') DEFAULT 'Processing',
    `status_notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`order_status`),
    INDEX `idx_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `variant_id` INT DEFAULT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    FOREIGN KEY (`variant_id`) REFERENCES `product_variants`(`variant_id`) ON DELETE SET NULL,
    INDEX `idx_order` (`order_id`),
    INDEX `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `favorites` (
    `favorite_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_fav` (`user_id`, `product_id`),
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reviews` (
    `review_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `order_id` INT DEFAULT NULL,
    `rating` TINYINT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `review_text` TEXT DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
    INDEX `idx_product_status` (`product_id`, `status`),
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `transaction_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `customer_name` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `transaction_id` VARCHAR(255) DEFAULT NULL,
    `payment_reference` VARCHAR(255) DEFAULT NULL,
    `order_id` INT DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    `payment_method` VARCHAR(100) DEFAULT 'online',
    `currency` VARCHAR(10) DEFAULT 'NGN',
    `delivery_method` VARCHAR(50) DEFAULT NULL,
    `transaction_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_order` (`order_id`),
    INDEX `idx_date` (`transaction_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `subscriptions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('active', 'unsubscribed') DEFAULT 'active',
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(100) NOT NULL,
    `title` VARCHAR(500) NOT NULL,
    `message` TEXT NOT NULL,
    `reference_id` INT DEFAULT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `for_user_id` INT DEFAULT NULL,
    `for_admin` TINYINT(1) DEFAULT 0,
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_admin` (`for_admin`, `is_read`),
    INDEX `idx_user` (`for_user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `returns` (
    `return_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `reason` TEXT NOT NULL,
    `return_status` ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    `admin_notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`return_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `issues` (
    `issue_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `order_id` INT DEFAULT NULL,
    `subject` VARCHAR(500) NOT NULL,
    `description` TEXT NOT NULL,
    `issue_status` ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    `admin_response` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`issue_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) DEFAULT '',
    `last_name` VARCHAR(100) DEFAULT '',
    `role` ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_status_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `old_status` VARCHAR(50) DEFAULT NULL,
    `new_status` VARCHAR(50) NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `changed_by` INT DEFAULT NULL,
    `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
