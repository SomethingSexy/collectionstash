-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 05, 2015 at 05:40 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cs_4_1_lean`
--
CREATE DATABASE IF NOT EXISTS `cs_4_1_lean` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `cs_4_1_lean`;

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `action_type_id` int(10) NOT NULL,
  `reason` text COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1225 ;

-- --------------------------------------------------------

--
-- Table structure for table `action_types`
--

CREATE TABLE IF NOT EXISTS `action_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `action` varchar(100) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `action_types`
--

INSERT INTO `action_types` (`id`, `action`, `created`, `modified`) VALUES
(1, 'Add', '2012-08-25 15:38:03', '2012-08-25 15:38:03'),
(2, 'Edit', '2012-08-25 15:38:03', '2012-08-25 15:38:03'),
(3, 'Approved', '2012-08-25 15:38:03', '2012-08-25 15:38:03'),
(4, 'Delete', '2012-08-25 15:38:03', '2012-08-25 15:38:03');

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE IF NOT EXISTS `activities` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `activity_type_id` int(10) NOT NULL,
  `data` text COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`activity_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `activity_types`
--

CREATE TABLE IF NOT EXISTS `activity_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `activity_types`
--

INSERT INTO `activity_types` (`id`, `type`, `created`, `modified`) VALUES
(1, 'Add Comment', '2012-12-07 21:10:42', '2012-12-07 21:10:42'),
(2, 'Add to Stash', '2012-12-08 12:44:00', '2012-12-08 12:44:00'),
(3, 'Remove from Stash', '2012-12-08 12:44:00', '2012-12-08 12:44:00'),
(4, 'Edit User Collectible', '2012-12-09 18:05:40', '2012-12-09 18:05:40'),
(5, 'Add Photo', '2012-12-09 18:05:40', '2012-12-09 18:05:40'),
(6, 'User Submit New', '2012-12-15 11:49:17', '2012-12-15 11:49:17'),
(7, 'User Submit Edit', '2012-12-15 11:49:17', '2012-12-15 11:49:17'),
(8, 'Admin Approve New', '2012-12-15 11:49:17', '2012-12-15 11:49:17'),
(9, 'Admin Approve Edit', '2012-12-15 11:49:17', '2012-12-15 11:49:17'),
(10, 'User Invites User', '2012-12-16 11:46:50', '2012-12-16 11:46:50'),
(11, 'User Add New', '2013-04-28 17:15:42', '2013-04-28 17:15:42'),
(12, 'User Edit', '2013-04-28 17:15:42', '2013-04-28 17:15:42'),
(13, 'User Add Listing', '2013-05-28 17:42:42', '2013-05-28 17:42:42'),
(14, 'Add to Wish List', '2013-12-27 00:00:00', '2013-12-27 00:00:00'),
(15, 'Remove to Wish List', '2013-12-27 00:00:00', '2013-12-27 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE IF NOT EXISTS `artists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `artists_collectible_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=333 ;

-- --------------------------------------------------------

--
-- Table structure for table `artists_collectibles`
--

CREATE TABLE IF NOT EXISTS `artists_collectibles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `artist_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `revision_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `artist_id` (`artist_id`,`collectible_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1416 ;

-- --------------------------------------------------------

--
-- Table structure for table `artists_collectibles_edits`
--

CREATE TABLE IF NOT EXISTS `artists_collectibles_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `base_id` int(10) DEFAULT NULL,
  `artist_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `edit_id` (`edit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(10) NOT NULL,
  `status_id` int(10) NOT NULL DEFAULT '4',
  `revision_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL COMMENT 'user id of the user who originally submitted this attribute',
  `attribute_category_id` int(10) NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `artist_id` int(10) DEFAULT NULL,
  `scale_id` int(10) DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'mass',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `entity_type_id` (`entity_type_id`),
  KEY `artist_id` (`artist_id`),
  KEY `status_id` (`status_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6813 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_collectibles`
--

CREATE TABLE IF NOT EXISTS `attributes_collectibles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) NOT NULL,
  `attribute_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `count` int(10) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `attribute_collectible_type_id` int(10) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7480 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_collectibles_edits`
--

CREATE TABLE IF NOT EXISTS `attributes_collectibles_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `base_id` int(10) DEFAULT NULL,
  `attribute_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `attribute_collectible_type_id` int(10) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `active` (`active`),
  KEY `edit_user_id` (`edit_user_id`),
  KEY `edit_id` (`edit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_collectibles_revs`
--

CREATE TABLE IF NOT EXISTS `attributes_collectibles_revs` (
  `version_id` int(10) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(10) NOT NULL,
  `revision_id` int(10) NOT NULL,
  `attribute_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `count` int(10) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL,
  `attribute_collectible_type_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `attribute_id` (`attribute_id`,`collectible_id`),
  KEY `collectible_id` (`collectible_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8890 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_edits`
--

CREATE TABLE IF NOT EXISTS `attributes_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `base_id` int(10) NOT NULL,
  `replace_attribute_id` int(10) NOT NULL,
  `status_id` int(10) NOT NULL DEFAULT '4',
  `revision_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL COMMENT 'user id of the user who originally submitted this attribute',
  `attribute_category_id` int(10) NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `artist_id` int(10) DEFAULT NULL,
  `scale_id` int(10) DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `edit_id` (`edit_id`,`edit_user_id`,`base_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_revs`
--

CREATE TABLE IF NOT EXISTS `attributes_revs` (
  `version_id` int(10) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(10) NOT NULL,
  `revision_id` int(10) NOT NULL,
  `attribute_category_id` int(10) NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `artist_id` int(10) DEFAULT NULL,
  `scale_id` int(10) DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_bin NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11947 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_uploads`
--

CREATE TABLE IF NOT EXISTS `attributes_uploads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) NOT NULL,
  `attribute_id` int(10) NOT NULL,
  `upload_id` int(10) NOT NULL,
  `primary` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_id` (`attribute_id`,`upload_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_uploads_edits`
--

CREATE TABLE IF NOT EXISTS `attributes_uploads_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `base_id` int(10) DEFAULT NULL,
  `attribute_id` int(10) NOT NULL,
  `upload_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `primary` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `base_id` (`base_id`),
  KEY `edit_id` (`edit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_uploads_revs`
--

CREATE TABLE IF NOT EXISTS `attributes_uploads_revs` (
  `version_id` int(10) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(10) NOT NULL,
  `attribute_id` int(10) NOT NULL,
  `upload_id` int(10) NOT NULL,
  `primary` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=145 ;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_categories`
--

CREATE TABLE IF NOT EXISTS `attribute_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `path_name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Dumping data for table `attribute_categories`
--

INSERT INTO `attribute_categories` (`id`, `parent_id`, `lft`, `rght`, `name`, `path_name`, `created`, `modified`) VALUES
(1, NULL, 1, 54, 'Part', 'Part', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(5, 1, 34, 35, 'Print', 'Part/Print', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(6, 1, 32, 33, 'Head', 'Part/Head', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(7, 1, 30, 31, 'Hand', 'Part/Hand', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(8, 1, 36, 47, 'Weapon', 'Part/Weapon', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(9, 1, 6, 29, 'Clothing', 'Part/Clothing', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(10, 1, 4, 5, 'Environment', 'Part/Environment', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(11, 1, 48, 49, 'Prop', 'Part/Prop', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(12, 1, 2, 3, 'Display Base', 'Part/Display Base', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(13, 9, 19, 20, 'Shirt', 'Part/Clothing/Shirt', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(14, 9, 17, 18, 'Pants', 'Part/Clothing/Pants', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(15, 9, 13, 14, 'Hat', 'Part/Clothing/Hat', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(16, 9, 21, 22, 'Shoes', 'Part/Clothing/Shoes', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(17, 9, 15, 16, 'Jacket', 'Part/Clothing/Jacket', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(18, 9, 11, 12, 'Belt', 'Part/Clothing/Belt', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(20, 2, 60, 61, 'Autograph', 'Feature/Autograph', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(21, 9, 25, 26, 'Cape', 'Part/Clothing/Cape', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(22, 9, 23, 24, 'Miscellaneous', 'Part/Clothing/Miscellaneous', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(23, 9, 9, 10, 'Robe', 'Part/Clothing/Robe', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(24, 9, 7, 8, 'Socks', 'Part/Clothing/Socks', '0000-00-00 00:00:00', '2012-11-17 19:42:40'),
(25, 9, 27, 28, 'Armour', 'Part/Clothing/Armour', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(26, 8, 39, 40, 'Firearm', 'Part/Weapon/Firearm', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(27, 8, 43, 44, 'Melee', 'Part/Weapon/Melee', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(28, 8, 45, 46, 'Miscellaneous', 'Part/Weapon/Miscellaneous', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(29, 8, 37, 38, 'Accessory', 'Part/Weapon/Accessory', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(30, 8, 41, 42, 'Holster', 'Part/Weapon/Holster', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(32, 1, 50, 51, 'Body', 'Part/Body', '0000-00-00 00:00:00', '2012-11-17 19:42:41'),
(34, 1, 52, 53, 'Document', 'Part/Document', '2012-11-17 19:40:37', '2012-11-17 19:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `collectibles`
--

CREATE TABLE IF NOT EXISTS `collectibles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(10) NOT NULL,
  `revision_id` int(10) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'This is the user id of the user who added this collectible originally.',
  `status_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `collectibletype_id` int(10) NOT NULL,
  `specialized_type_id` int(10) DEFAULT NULL,
  `description` text NOT NULL,
  `msrp` double(20,2) DEFAULT NULL,
  `currency_id` int(10) NOT NULL DEFAULT '1',
  `edition_size` int(10) DEFAULT NULL COMMENT 'Allowed to be null because it can be not set.',
  `upc` varchar(13) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `product_width` double(20,2) DEFAULT NULL,
  `product_depth` double(20,2) DEFAULT NULL,
  `license_id` int(10) DEFAULT NULL,
  `series_id` int(10) DEFAULT NULL,
  `variant` tinyint(1) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `exclusive` tinyint(1) NOT NULL,
  `retailer_id` int(10) DEFAULT NULL,
  `variant_collectible_id` int(10) NOT NULL,
  `product_length` double(20,2) DEFAULT NULL,
  `product_weight` decimal(20,2) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `scale_id` int(10) DEFAULT NULL,
  `release` year(4) DEFAULT NULL,
  `limited` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(255) DEFAULT NULL,
  `numbered` tinyint(1) NOT NULL,
  `pieces` int(10) DEFAULT NULL,
  `signed` tinyint(1) NOT NULL DEFAULT '0',
  `official` tinyint(1) NOT NULL DEFAULT '1',
  `original` tinyint(1) NOT NULL,
  `custom` tinyint(1) NOT NULL,
  `custom_status_id` int(10) DEFAULT NULL,
  `collectibles_user_count` int(10) NOT NULL,
  `collectibles_wish_list_count` int(10) NOT NULL,
  `collectible_price_fact_id` int(10) DEFAULT NULL,
  `viewed` int(10) NOT NULL DEFAULT '0',
  `parsed_from_url` tinyint(1) NOT NULL DEFAULT '0',
  `parsed_data` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `manufacture_id` (`manufacture_id`),
  KEY `collectibletype_id` (`collectibletype_id`),
  KEY `license_id` (`license_id`),
  KEY `scale_id` (`scale_id`),
  KEY `variant_collectible_id` (`variant_collectible_id`),
  KEY `user_id` (`user_id`),
  KEY `specialized_type_id` (`specialized_type_id`),
  KEY `entity_type_id` (`entity_type_id`),
  KEY `status_id` (`status_id`),
  FULLTEXT KEY `name_2` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5218 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_edits`
--

CREATE TABLE IF NOT EXISTS `collectibles_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL COMMENT 'User id of the person editing the collectible.',
  `action_id` int(10) NOT NULL,
  `base_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `collectibletype_id` int(10) NOT NULL,
  `specialized_type_id` int(10) DEFAULT NULL,
  `description` text NOT NULL,
  `msrp` double(20,2) NOT NULL,
  `currency_id` int(10) NOT NULL DEFAULT '1',
  `edition_size` int(10) DEFAULT NULL COMMENT 'Allowed to be null because it can be not set.',
  `upc` varchar(12) DEFAULT NULL,
  `product_width` double(20,2) DEFAULT NULL,
  `product_depth` double(20,2) DEFAULT NULL,
  `license_id` int(10) DEFAULT NULL,
  `series_id` int(10) DEFAULT NULL,
  `variant` tinyint(1) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `exclusive` tinyint(1) NOT NULL,
  `exclusive_manufacture_id` int(10) DEFAULT NULL,
  `variant_collectible_id` int(10) NOT NULL,
  `product_length` double(20,2) DEFAULT NULL,
  `product_weight` decimal(20,2) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `scale_id` int(10) DEFAULT NULL,
  `release` year(4) DEFAULT NULL,
  `limited` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(255) DEFAULT NULL,
  `retailer_id` int(10) DEFAULT NULL,
  `numbered` tinyint(1) NOT NULL,
  `pieces` int(10) DEFAULT NULL,
  `signed` tinyint(1) NOT NULL DEFAULT '0',
  `official` tinyint(1) NOT NULL,
  `original` tinyint(1) NOT NULL,
  `custom` tinyint(1) NOT NULL,
  `custom_status_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `base_id` (`base_id`),
  KEY `edit_id` (`edit_id`),
  KEY `edit_user_id` (`edit_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_revs`
--

CREATE TABLE IF NOT EXISTS `collectibles_revs` (
  `version_id` int(10) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(10) NOT NULL,
  `revision_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `status_id` int(10) NOT NULL,
  `state` int(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `collectibletype_id` int(10) NOT NULL,
  `specialized_type_id` int(10) DEFAULT NULL,
  `description` text NOT NULL,
  `msrp` double(20,2) DEFAULT NULL,
  `currency_id` int(10) NOT NULL,
  `edition_size` int(10) DEFAULT NULL COMMENT 'Allowed to be null because it can be not set.',
  `upc` varchar(13) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `product_width` double(20,2) DEFAULT NULL,
  `product_depth` double(20,2) DEFAULT NULL,
  `approval_id` int(10) NOT NULL,
  `license_id` int(10) DEFAULT NULL,
  `series_id` int(10) DEFAULT NULL,
  `variant` tinyint(1) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `exclusive` tinyint(1) NOT NULL,
  `retailer_id` int(10) DEFAULT NULL,
  `variant_collectible_id` int(10) NOT NULL,
  `product_length` double(20,2) DEFAULT NULL,
  `product_weight` decimal(20,2) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `scale_id` int(10) DEFAULT NULL,
  `release` year(4) DEFAULT NULL,
  `limited` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(255) DEFAULT NULL,
  `numbered` tinyint(1) NOT NULL,
  `pieces` int(10) DEFAULT NULL,
  `signed` tinyint(4) NOT NULL DEFAULT '0',
  `official` tinyint(1) NOT NULL DEFAULT '1',
  `original` tinyint(1) NOT NULL,
  `custom` tinyint(1) NOT NULL,
  `custom_status_id` int(10) DEFAULT NULL,
  `viewed` int(10) NOT NULL DEFAULT '0',
  `parsed_from_url` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21499 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_tags`
--

CREATE TABLE IF NOT EXISTS `collectibles_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `tag_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15434 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_tags_edits`
--

CREATE TABLE IF NOT EXISTS `collectibles_tags_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `base_id` int(10) DEFAULT NULL,
  `edit_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `tag_id` int(10) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `edit_user_id` (`edit_user_id`),
  KEY `edit_id` (`edit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_uploads`
--

CREATE TABLE IF NOT EXISTS `collectibles_uploads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `upload_id` int(10) NOT NULL,
  `primary` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`,`upload_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7985 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_uploads_edits`
--

CREATE TABLE IF NOT EXISTS `collectibles_uploads_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `base_id` int(10) DEFAULT NULL,
  `collectible_id` int(10) NOT NULL,
  `upload_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `primary` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `base_id` (`base_id`),
  KEY `edit_id` (`edit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_uploads_revs`
--

CREATE TABLE IF NOT EXISTS `collectibles_uploads_revs` (
  `version_id` int(10) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `upload_id` int(10) NOT NULL,
  `primary` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9610 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_users`
--

CREATE TABLE IF NOT EXISTS `collectibles_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `stash_id` int(10) NOT NULL,
  `condition_id` int(10) DEFAULT NULL,
  `merchant_id` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `edition_size` int(10) DEFAULT NULL,
  `cost` double(20,2) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `artist_proof` tinyint(1) NOT NULL DEFAULT '0',
  `sort_number` int(10) NOT NULL,
  `collectible_user_remove_reason_id` int(10) DEFAULT NULL,
  `listing_id` char(36) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `sale` tinyint(1) NOT NULL,
  `remove_date` date DEFAULT NULL,
  `notes` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `notes_private` tinyint(1) NOT NULL DEFAULT '0',
  `user_upload_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `user_id` (`user_id`),
  KEY `stash_id` (`stash_id`),
  KEY `condition_id` (`condition_id`),
  KEY `merchant_id` (`merchant_id`),
  KEY `active` (`active`),
  KEY `sale` (`sale`),
  KEY `user_upload_id` (`user_upload_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12038 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_users_revs`
--

CREATE TABLE IF NOT EXISTS `collectibles_users_revs` (
  `version_id` int(10) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `stash_id` int(10) NOT NULL,
  `condition_id` int(10) DEFAULT NULL,
  `merchant_id` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `edition_size` int(10) DEFAULT NULL,
  `cost` double(20,2) DEFAULT NULL,
  `artist_proof` tinyint(1) NOT NULL DEFAULT '0',
  `purchase_date` date DEFAULT NULL,
  `collectible_user_remove_reason_id` int(10) DEFAULT NULL,
  `listing_id` char(36) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `sale` tinyint(1) NOT NULL,
  `remove_date` date DEFAULT NULL,
  `notes` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `notes_private` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13062 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_wish_lists`
--

CREATE TABLE IF NOT EXISTS `collectibles_wish_lists` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `collectible_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `wish_list_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`,`user_id`,`wish_list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1416 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibletypes`
--

CREATE TABLE IF NOT EXISTS `collectibletypes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `collectible_count` int(10) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `collectibletypes`
--

INSERT INTO `collectibletypes` (`id`, `parent_id`, `lft`, `rght`, `name`, `collectible_count`, `modified`, `created`) VALUES
(1, NULL, 1, 6, 'Action Figure', 1103, '2011-09-16 22:06:14', '2011-09-16 22:06:14'),
(2, NULL, 7, 8, 'Diorama', 123, '2011-09-16 22:06:14', '2011-09-16 22:06:14'),
(3, 11, 20, 19, 'Prop Replica', 70, '2011-09-16 22:06:14', '2011-09-16 22:06:14'),
(4, NULL, 11, 12, 'Bust', 935, '2011-09-16 22:06:14', '2011-09-16 22:06:14'),
(5, NULL, 13, 14, 'Maquette', 123, '2011-09-16 22:06:14', '2011-09-16 22:06:14'),
(6, NULL, 15, 16, 'Ornament', 6, '2011-09-16 22:06:14', '2011-09-16 22:06:14'),
(7, NULL, 17, 18, 'Statue', 1591, '2011-09-16 22:06:14', '2011-09-16 22:06:14'),
(8, 1, 2, 3, 'Action Figure Environment', 24, '2011-09-16 22:07:39', '2011-09-16 22:07:39'),
(9, 1, 4, 5, 'Action Figure Accessory', 38, '2011-09-16 22:08:11', '2011-09-16 22:08:11'),
(10, NULL, 27, 28, 'Print', 236, '2013-02-10 12:27:15', '2013-02-10 12:27:15'),
(11, NULL, 19, 20, 'Replica', 133, '2011-09-28 18:46:14', '2011-09-28 18:46:18'),
(12, NULL, 21, 22, 'Vinyl Figure', 189, '2011-10-13 20:25:41', '2011-10-13 20:25:44'),
(13, 7, 18, 19, 'Statue Accessory', 2, '2011-10-27 21:12:58', '2011-10-27 21:13:01'),
(14, NULL, 23, 24, 'Designer Toy', 2, '2012-11-25 08:35:52', '2012-11-25 08:35:52'),
(15, NULL, 25, 26, 'Coin', 16, '2013-01-14 19:11:39', '2013-01-14 19:11:39'),
(17, NULL, 29, 30, 'Film Cell', 0, '2013-05-20 15:30:05', '2013-05-20 15:30:05'),
(18, NULL, 31, 32, 'Button', 7, '2013-09-24 15:05:18', '2013-09-24 15:05:18'),
(19, NULL, 33, 34, 'Keychain', 5, '2013-10-31 09:10:38', '2013-10-31 09:10:38'),
(20, NULL, 35, 38, 'Model Kit', 3, '2013-11-06 19:43:40', '2013-11-06 19:43:40'),
(21, NULL, 39, 42, 'Weapon', 0, '2013-11-06 22:20:47', '2013-11-06 22:20:47'),
(22, 21, 40, 41, 'Knife', 0, '2013-11-06 22:21:33', '2013-11-06 22:21:33'),
(23, 20, 36, 37, 'Garage Kit', 0, '2013-11-07 05:38:02', '2013-11-07 05:38:02');

-- --------------------------------------------------------

--
-- Table structure for table `collectible_favorites`
--

CREATE TABLE IF NOT EXISTS `collectible_favorites` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `favorite_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `favorite_id` (`favorite_id`,`collectible_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectible_price_facts`
--

CREATE TABLE IF NOT EXISTS `collectible_price_facts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `average_price` double(20,2) NOT NULL,
  `average_price_ebay` double(20,2) NOT NULL,
  `average_price_external` double(20,2) NOT NULL,
  `total_transactions` int(10) NOT NULL DEFAULT '0',
  `total_transactions_ebay` int(10) NOT NULL DEFAULT '0',
  `total_transactions_external` int(10) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=611 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectible_user_remove_reasons`
--

CREATE TABLE IF NOT EXISTS `collectible_user_remove_reasons` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `reason` varchar(100) COLLATE utf8_bin NOT NULL,
  `remove` tinyint(1) NOT NULL,
  `sold_cost_required` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `collectible_user_remove_reasons`
--

INSERT INTO `collectible_user_remove_reasons` (`id`, `reason`, `remove`, `sold_cost_required`, `created`, `modified`) VALUES
(1, 'Sold', 0, 0, '2013-06-04 00:00:00', '2013-06-04 00:00:00'),
(2, 'Traded', 0, 0, '2013-06-04 00:00:00', '2013-06-04 00:00:00'),
(3, 'Duplicate', 1, 0, '2013-06-04 00:00:00', '2013-06-04 00:00:00'),
(4, 'Accidently Added', 1, 0, '2013-06-04 00:00:00', '2013-06-04 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `collectible_views`
--

CREATE TABLE IF NOT EXISTS `collectible_views` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `collectible_id` int(10) NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `collectible_id_2` (`collectible_id`),
  KEY `collectible_id_3` (`collectible_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=110 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `comment` text COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`),
  KEY `comment_type_id` (`entity_type_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=922 ;

-- --------------------------------------------------------

--
-- Table structure for table `conditions`
--

CREATE TABLE IF NOT EXISTS `conditions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `conditions`
--

INSERT INTO `conditions` (`id`, `name`) VALUES
(1, 'New'),
(2, 'Mint in Box'),
(3, 'Mint in package'),
(4, 'Mint on card'),
(5, 'Mint and Complete'),
(6, 'Mint, no box'),
(7, 'Near Mint'),
(8, 'Never removed from box'),
(9, 'Loose'),
(10, 'Very Fine'),
(11, 'Fine'),
(12, 'Very Good'),
(13, 'Good'),
(14, 'Fair'),
(15, 'Poor');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `iso_code` varchar(255) COLLATE utf8_bin NOT NULL,
  `sign` varchar(255) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `iso_code`, `sign`, `created`, `modified`) VALUES
(1, 'USD', '$', '2012-03-18 21:36:06', '2012-03-18 21:36:06'),
(2, 'EUR', '€', '2012-03-18 21:36:06', '2012-03-18 21:36:06'),
(3, 'GBP', '£', '2012-03-18 21:37:00', '2012-03-18 21:37:00'),
(4, 'JPY', '¥', '2012-03-18 21:37:00', '2012-03-18 21:37:00'),
(5, 'CNY', '¥', '2012-03-18 21:37:57', '2012-03-18 21:37:57');

-- --------------------------------------------------------

--
-- Table structure for table `custom_statuses`
--

CREATE TABLE IF NOT EXISTS `custom_statuses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `status` varchar(100) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `custom_statuses`
--

INSERT INTO `custom_statuses` (`id`, `status`, `created`, `modified`) VALUES
(1, 'Draft', '2013-04-06 10:40:59', '2013-04-06 10:40:59'),
(2, 'Prototype', '2013-04-06 10:40:59', '2013-04-06 10:40:59'),
(3, 'Work in Progress', '2013-04-06 10:40:59', '2013-04-06 10:40:59'),
(4, 'Completed', '2013-04-06 10:40:59', '2013-04-06 10:40:59');

-- --------------------------------------------------------

--
-- Table structure for table `edits`
--

CREATE TABLE IF NOT EXISTS `edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0=not approved 1=approved 2=denied',
  `notes` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `receiver` varchar(200) COLLATE utf8_bin NOT NULL,
  `subject` varchar(200) COLLATE utf8_bin NOT NULL,
  `body` text COLLATE utf8_bin NOT NULL,
  `template` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `template_json_data` text COLLATE utf8_bin,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sent` (`sent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `entity_types`
--

CREATE TABLE IF NOT EXISTS `entity_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8_bin NOT NULL,
  `comment_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7148 ;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `forgotten_requests`
--

CREATE TABLE IF NOT EXISTS `forgotten_requests` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `invites`
--

CREATE TABLE IF NOT EXISTS `invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(80) NOT NULL,
  `registered` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Table structure for table `latest_comments`
--

CREATE TABLE IF NOT EXISTS `latest_comments` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) NOT NULL,
  `entity_type_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL COMMENT 'here for fast reference',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_type_id` (`entity_type_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=381 ;

-- --------------------------------------------------------

--
-- Table structure for table `licenses`
--

CREATE TABLE IF NOT EXISTS `licenses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `collectible_count` int(10) NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=391 ;

-- --------------------------------------------------------

--
-- Table structure for table `licenses_manufactures`
--

CREATE TABLE IF NOT EXISTS `licenses_manufactures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `manufacture_id` int(10) NOT NULL,
  `license_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `manufacture_id` (`manufacture_id`),
  KEY `license_id` (`license_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=727 ;

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE IF NOT EXISTS `listings` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `listing_type_id` int(10) NOT NULL,
  `ext_item_id` varchar(200) COLLATE utf8_bin NOT NULL,
  `type` varchar(50) COLLATE utf8_bin NOT NULL,
  `listing_name` varchar(200) COLLATE utf8_bin NOT NULL,
  `listing_description` text COLLATE utf8_bin NOT NULL,
  `url` varchar(255) COLLATE utf8_bin NOT NULL,
  `listing_price` double(20,2) DEFAULT NULL,
  `current_price` double(20,2) DEFAULT NULL,
  `traded_for` text COLLATE utf8_bin,
  `quantity` int(10) NOT NULL,
  `quantity_sold` int(10) NOT NULL,
  `number_of_bids` int(10) NOT NULL,
  `status` varchar(100) COLLATE utf8_bin NOT NULL,
  `condition_ext_id` int(10) DEFAULT NULL,
  `condition_name` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `relisted` tinyint(1) NOT NULL,
  `relisted_ext_id` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `processed` tinyint(1) NOT NULL,
  `flagged` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `flagged` (`flagged`),
  KEY `collectible_id` (`collectible_id`),
  KEY `ext_item_id` (`ext_item_id`),
  KEY `status` (`status`),
  KEY `end_date` (`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `manufactures`
--

CREATE TABLE IF NOT EXISTS `manufactures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `bio` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `upload_id` int(10) DEFAULT NULL,
  `collectible_count` int(10) NOT NULL,
  `series_id` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`),
  KEY `upload_id` (`upload_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=142 ;

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

CREATE TABLE IF NOT EXISTS `merchants` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `collectibles_user_count` int(10) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=273 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `subject` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `notification_type` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `notification_json_data` text COLLATE utf8_bin,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `points`
--

CREATE TABLE IF NOT EXISTS `points` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `points` int(10) NOT NULL,
  `activity_type_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_type_id` (`activity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE IF NOT EXISTS `polls` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

CREATE TABLE IF NOT EXISTS `poll_options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `poll_id` int(10) NOT NULL,
  `vote_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `user_id` int(10) NOT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invites` int(2) NOT NULL DEFAULT '0',
  `email_notification` tinyint(1) NOT NULL DEFAULT '1',
  `email_newsletter` tinyint(1) NOT NULL DEFAULT '1',
  `display_name` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=387 ;

-- --------------------------------------------------------

--
-- Table structure for table `retailers`
--

CREATE TABLE IF NOT EXISTS `retailers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `collectible_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=139 ;

-- --------------------------------------------------------

--
-- Table structure for table `revisions`
--

CREATE TABLE IF NOT EXISTS `revisions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `action` varchar(1) NOT NULL,
  `notes` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39183 ;

-- --------------------------------------------------------

--
-- Table structure for table `scales`
--

CREATE TABLE IF NOT EXISTS `scales` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `scale` varchar(50) NOT NULL,
  `collectible_count` int(10) NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `scales`
--

INSERT INTO `scales` (`id`, `scale`, `collectible_count`, `modified`, `created`) VALUES
(1, '1:64', 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, '1:34', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, '1:32', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, '1:24', 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, '1:18', 33, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, '1:6', 1356, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, '1:4', 581, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, '1:1', 165, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, '1:5', 188, '2011-09-13 14:47:35', '2011-09-13 14:47:38'),
(11, '1:9', 63, '2011-09-19 19:27:00', '2011-09-19 19:27:03'),
(12, '1:2', 93, '2011-09-19 19:29:05', '2011-09-19 19:29:07'),
(13, '1:3', 17, '2011-09-19 19:29:14', '2011-09-19 19:29:17'),
(14, '1:7', 194, '2011-09-19 19:30:20', '2011-09-19 19:30:20'),
(15, '1:8', 173, '2011-09-19 19:30:20', '2011-09-19 19:30:20'),
(16, '1:10', 48, '2011-09-19 19:30:20', '2011-09-19 19:30:20'),
(17, '1:11', 5, '2011-09-19 19:30:20', '2011-09-19 19:30:20'),
(18, '1:12', 112, '2011-09-19 19:30:20', '2011-09-19 19:30:20'),
(19, '1:400', 1, '2012-08-06 21:55:47', '2012-08-06 21:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE IF NOT EXISTS `series` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1150 ;

-- --------------------------------------------------------

--
-- Table structure for table `stashes`
--

CREATE TABLE IF NOT EXISTS `stashes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `privacy` int(1) NOT NULL DEFAULT '0',
  `collectibles_user_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `entity_type_id` (`entity_type_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=747 ;

-- --------------------------------------------------------

--
-- Table structure for table `stash_facts`
--

CREATE TABLE IF NOT EXISTS `stash_facts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `stash_id` int(10) NOT NULL,
  `msrp_value` double(20,2) NOT NULL,
  `total_paid` double(20,2) NOT NULL,
  `count_collectibles_paid` int(10) NOT NULL,
  `total_sold` double(20,2) NOT NULL,
  `count_collectibles_sold` int(10) NOT NULL,
  `count_collectibles_remove_sold` int(10) NOT NULL,
  `current_value` double(20,2) NOT NULL,
  `count_collectibles_current_value` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stash_id` (`stash_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=430 ;

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `status` varchar(200) COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `status`, `created`, `modified`) VALUES
(1, 'Draft', '2012-07-29 15:20:04', '2012-07-29 15:20:04'),
(2, 'Submitted', '2012-07-29 15:20:04', '2012-07-29 15:20:04'),
(3, 'Approved', '2012-07-29 15:20:04', '2012-07-29 15:20:04'),
(4, 'Active', '2012-07-29 15:20:04', '2012-07-29 15:20:04'),
(5, 'Inactive', '2012-07-29 15:20:04', '2012-07-29 15:20:04'),
(6, 'Declined', '2012-07-29 15:20:59', '2012-07-29 15:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `collectibles_tag_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1883 ;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `listing_id` char(36) COLLATE utf8_bin NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `ext_transaction_id` varchar(200) COLLATE utf8_bin NOT NULL,
  `sale_price` double(20,2) NOT NULL,
  `sale_date` datetime NOT NULL,
  `bestOffer` tinyint(1) NOT NULL DEFAULT '0',
  `traded` tinyint(1) NOT NULL,
  `traded_for` text COLLATE utf8_bin,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `listing_id` (`listing_id`,`collectible_id`,`ext_transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) NOT NULL,
  `status_id` int(10) NOT NULL DEFAULT '4',
  `user_id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7940 ;

-- --------------------------------------------------------

--
-- Table structure for table `uploads_edits`
--

CREATE TABLE IF NOT EXISTS `uploads_edits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `base_id` int(10) DEFAULT NULL,
  `edit_user_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `edit_id` (`edit_id`),
  KEY `edit_user_id` (`edit_user_id`),
  KEY `base_id` (`base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uploads_revs`
--

CREATE TABLE IF NOT EXISTS `uploads_revs` (
  `version_id` int(10) NOT NULL AUTO_INCREMENT,
  `version_created` datetime NOT NULL,
  `id` int(11) NOT NULL,
  `revision_id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9354 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(80) NOT NULL,
  `collectibles_user_count` int(10) NOT NULL,
  `collectibles_wish_list_count` int(10) NOT NULL,
  `stash_count` int(10) NOT NULL,
  `invite_count` int(10) NOT NULL,
  `edit_count` int(10) NOT NULL,
  `collectible_count` int(10) NOT NULL,
  `user_upload_count` int(10) NOT NULL,
  `comment_count` int(10) NOT NULL,
  `points` bigint(20) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `force_password_reset` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=383 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE IF NOT EXISTS `user_favorites` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `favorite_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `favorite_id` (`favorite_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_point_facts`
--

CREATE TABLE IF NOT EXISTS `user_point_facts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `points` bigint(20) NOT NULL,
  `month` int(1) NOT NULL,
  `year` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`month`,`year`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=585 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_point_year_facts`
--

CREATE TABLE IF NOT EXISTS `user_point_year_facts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `points` bigint(20) NOT NULL,
  `year` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=681 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_uploads`
--

CREATE TABLE IF NOT EXISTS `user_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `name` varchar(200) CHARACTER SET latin1 NOT NULL,
  `type` varchar(200) CHARACTER SET latin1 NOT NULL,
  `size` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1199 ;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `poll_id` int(10) NOT NULL,
  `poll_option_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`poll_id`,`poll_option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `wish_lists`
--

CREATE TABLE IF NOT EXISTS `wish_lists` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `collectibles_wish_list_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=363 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
