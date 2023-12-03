-- Adminer 4.8.1 MySQL 5.7.39 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT 'Commercial name',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `brands` (`id`, `name`) VALUES
(7,	'Adidas'),
(4,	'Gola'),
(6,	'Le Coq Sportif'),
(5,	'Nike'),
(9,	'Puma'),
(8,	'Reebok'),
(3,	'Under Armour updated');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ean` varchar(13) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `visual_url` text,
  `packaging_details` text,
  `recycled_material_incorporation` decimal(5,2) DEFAULT NULL,
  `hazardous_substance_presence` tinyint(1) DEFAULT NULL,
  `packshot_file` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ean` (`ean`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_data_ibfk_1` (`brand_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `logo` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2023-12-03 07:33:15