-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 21, 2013 at 02:52 AM
-- Server version: 5.1.53
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cs_2_5`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=316 ;

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
  KEY `user_id` (`user_id`,`activity_type_id`),
  KEY `created` (`created`)
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- Table structure for table `attributes_collectibles`
--

CREATE TABLE IF NOT EXISTS `attributes_collectibles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) NOT NULL,
  `attribute_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `count` int(10) NOT NULL DEFAULT '1' COMMENT 'for this collectible and this attribute this will specifiy the count',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `attribute_collectible_type_id` int(10) NOT NULL DEFAULT '1' COMMENT 'for now 1 = added, 2 = wanted',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=87 ;

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
  `edit_user_id` int(10) NOT NULL,
  `count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `edit_user_id` (`edit_user_id`),
  KEY `edit_id` (`edit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

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
  `active` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `attribute_id` (`attribute_id`,`collectible_id`),
  KEY `collectible_id` (`collectible_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=150 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=135 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=21 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `collectibles`
--

CREATE TABLE IF NOT EXISTS `collectibles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `original` tinyint(1) NOT NULL,
  `entity_type_id` int(11) NOT NULL,
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
  `upc` varchar(12) DEFAULT NULL,
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
  `collectibles_user_count` int(10) NOT NULL,
  `custom` tinyint(1) NOT NULL,
  `custom_status_id` int(10) DEFAULT NULL,
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
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=730 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibles_edits`
--

CREATE TABLE IF NOT EXISTS `collectibles_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `official` tinyint(1) NOT NULL,
  `edit_id` int(10) NOT NULL,
  `action_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL COMMENT 'User id of the person editing the collectible.',
  `user_id` int(10) NOT NULL,
  `base_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `collectibletype_id` int(10) NOT NULL,
  `specialized_type_id` int(10) DEFAULT NULL,
  `description` text NOT NULL,
  `msrp` double(20,2) DEFAULT NULL,
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
  `custom` tinyint(1) NOT NULL,
  `custom_status_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `base_id` (`base_id`),
  KEY `edit_id` (`edit_id`),
  KEY `edit_user_id` (`edit_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

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
  `name` varchar(255) NOT NULL,
  `manufacture_id` int(10) DEFAULT NULL,
  `collectibletype_id` int(10) NOT NULL,
  `specialized_type_id` int(10) DEFAULT NULL,
  `description` text NOT NULL,
  `msrp` double(20,2) DEFAULT NULL,
  `currency_id` int(10) NOT NULL,
  `edition_size` int(10) DEFAULT NULL COMMENT 'Allowed to be null because it can be not set.',
  `upc` varchar(12) DEFAULT NULL,
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
  `signed` tinyint(1) NOT NULL DEFAULT '0',
  `official` tinyint(1) NOT NULL DEFAULT '1',
  `custom` tinyint(1) NOT NULL,
  `custom_status_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`version_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3835 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1557 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=600 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=111 ;

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
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `user_id` (`user_id`),
  KEY `stash_id` (`stash_id`),
  KEY `condition_id` (`condition_id`),
  KEY `merchant_id` (`merchant_id`),
  KEY `sort_number` (`sort_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=589 ;

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
  `artist_proof` int(1) NOT NULL DEFAULT '0',
  `purchase_date` date DEFAULT NULL,
  PRIMARY KEY (`version_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=745 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibletypes_manufactures`
--

CREATE TABLE IF NOT EXISTS `collectibletypes_manufactures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `collectibletype_id` int(10) NOT NULL,
  `manufacture_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectibletype_id` (`collectibletype_id`),
  KEY `manufacture_id` (`manufacture_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Table structure for table `collectibletypes_manufacture_specialized_types`
--

CREATE TABLE IF NOT EXISTS `collectibletypes_manufacture_specialized_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `collectibletypes_manufacture_id` int(10) NOT NULL,
  `specialized_type_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectibletypes_manufactures_id` (`collectibletypes_manufacture_id`,`specialized_type_id`),
  KEY `specialized_type_id` (`specialized_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=131 ;

-- --------------------------------------------------------

--
-- Table structure for table `conditions`
--

CREATE TABLE IF NOT EXISTS `conditions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=951 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Table structure for table `licenses`
--

CREATE TABLE IF NOT EXISTS `licenses` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '1' COMMENT 'user id of the person who added it',
  `name` varchar(255) NOT NULL,
  `collectible_count` int(10) NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=129 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=242 ;

-- --------------------------------------------------------

--
-- Table structure for table `manufactures`
--

CREATE TABLE IF NOT EXISTS `manufactures` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '1' COMMENT 'User id of the person who added it',
  `title` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `collectible_count` int(10) NOT NULL,
  `series_id` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

CREATE TABLE IF NOT EXISTS `merchants` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `collectibles_user_count` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `processed` tinyint(1) NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `user_id` int(10) NOT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invites` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5968 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE IF NOT EXISTS `series` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=259 ;

-- --------------------------------------------------------

--
-- Table structure for table `specialized_types`
--

CREATE TABLE IF NOT EXISTS `specialized_types` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `collectible_count` int(10) NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

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
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `collectibles_user_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `entity_type_id` (`entity_type_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=136 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `subscribed` tinyint(1) NOT NULL DEFAULT '1',
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_type_id` (`entity_type_id`,`user_id`),
  KEY `subscribed` (`subscribed`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=18 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=230 ;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` char(36) COLLATE utf8_bin NOT NULL,
  `user_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `transaction_type_id` int(10) NOT NULL,
  `ext_transaction_id` varchar(200) COLLATE utf8_bin NOT NULL,
  `type` varchar(50) COLLATE utf8_bin NOT NULL,
  `listing_price` double(20,2) NOT NULL,
  `sale_price` double(20,2) NOT NULL,
  `number_of_bids` int(10) NOT NULL,
  `status` varchar(100) COLLATE utf8_bin NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `processed` tinyint(1) NOT NULL,
  `flagged` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `revision_id` int(10) NOT NULL,
  `status_id` int(10) NOT NULL DEFAULT '4',
  `user_id` int(10) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=795 ;

-- --------------------------------------------------------

--
-- Table structure for table `uploads_edits`
--

CREATE TABLE IF NOT EXISTS `uploads_edits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `base_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` varchar(200) NOT NULL,
  `size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `action_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`edit_user_id`),
  KEY `upload_id` (`base_id`),
  KEY `edit_id` (`edit_id`)
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3084 ;

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
  `stash_count` int(10) NOT NULL,
  `invite_count` int(10) NOT NULL,
  `edit_count` int(10) NOT NULL,
  `collectible_count` int(10) NOT NULL,
  `user_upload_count` int(10) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `force_password_reset` tinyint(1) NOT NULL DEFAULT '0',
  `comment_count` int(10) NOT NULL,
  `points` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=198 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=107 ;
