-- Per-site database schema (MySQL/MariaDB, InnoDB, utf8mb4).
-- Run this on each site DB. Panel (super admin) stores connection to this DB.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `site_pages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int unsigned NOT NULL DEFAULT 1,
  `slug` varchar(128) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(512) DEFAULT NULL,
  `template` varchar(64) DEFAULT 'default',
  `sort_order` int NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_site_slug` (`site_id`,`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int unsigned NOT NULL DEFAULT 1,
  `key` varchar(64) NOT NULL,
  `value` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_site_key` (`site_id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
