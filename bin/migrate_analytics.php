<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/app/Core/Autoloader.php';

try {
    $db = \App\Core\Database::getInstance()->getConnection();
    if (!$db) {
        echo "Database connection failed\n";
        exit(1);
    }

    $sql1 = "CREATE TABLE IF NOT EXISTS `visitors` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `ip` VARCHAR(64) NOT NULL,
      `country` VARCHAR(100) DEFAULT 'United States',
      `country_code` VARCHAR(10) DEFAULT 'US',
      `city` VARCHAR(100) DEFAULT NULL,
      `device` ENUM('desktop', 'mobile', 'tablet') NOT NULL DEFAULT 'desktop',
      `browser` VARCHAR(100) DEFAULT 'Chrome',
      `url` VARCHAR(255) NOT NULL,
      `referrer` VARCHAR(255) DEFAULT 'direct',
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_vis_created` (`created_at`),
      INDEX `idx_vis_url` (`url`),
      INDEX `idx_vis_device` (`device`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $sql2 = "CREATE TABLE IF NOT EXISTS `analytics_events` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `visitor_ip` VARCHAR(64) NOT NULL,
      `url` VARCHAR(255) NOT NULL,
      `event_type` ENUM('cart_add', 'checkout_start', 'order_complete', 'click', 'dwell') NOT NULL,
      `event_name` VARCHAR(100) NOT NULL,
      `event_value` INT UNSIGNED DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX `idx_evt_type_created` (`event_type`, `created_at`),
      INDEX `idx_evt_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql1);
    $db->exec($sql2);

    echo "Tables `visitors` and `analytics_events` migrated successfully!\n";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

