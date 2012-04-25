-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2012 at 10:52 PM
-- Server version: 5.1.53
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cakedev`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes_collectibles_edits`
--

CREATE TABLE IF NOT EXISTS `attributes_collectibles_edits` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `edit_id` int(10) NOT NULL,
  `action` varchar(1) NOT NULL,
  `base_id` int(10) DEFAULT NULL,
  `attribute_id` int(10) NOT NULL,
  `collectible_id` int(10) NOT NULL,
  `edit_user_id` int(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `variant` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collectible_id` (`collectible_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `active` (`active`),
  KEY `edit_user_id` (`edit_user_id`),
  KEY `edit_id` (`edit_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=125 ;

--
-- Dumping data for table `attributes_collectibles_edits`
--

INSERT INTO `attributes_collectibles_edits` (`id`, `edit_id`, `action`, `base_id`, `attribute_id`, `collectible_id`, `edit_user_id`, `description`, `variant`, `active`, `created`, `modified`) VALUES
(1, 119, 'D', 78, 7, 104, 1, 'Spiked Fist', 0, 1, '2011-09-19 17:25:09', '2011-09-19 17:25:09'),
(2, 135, 'A', NULL, 3, 174, 1, 'Light up eyes and base', 0, 1, '2011-09-21 20:43:20', '2011-09-21 20:43:20'),
(3, 147, 'E', 344, 17, 246, 1, 'Red and brown colored faux leather jacket', 0, 1, '2011-09-23 22:20:49', '2011-09-23 22:20:49'),
(4, 0, 'A', NULL, 11, 280, 1, 'Abnormal brain', 0, 1, '2011-09-25 15:23:24', '2011-09-25 15:23:24'),
(5, 0, 'A', NULL, 11, 280, 1, 'Labeled brain jar', 0, 1, '2011-09-25 15:23:24', '2011-09-25 15:23:24'),
(6, 0, 'A', NULL, 11, 280, 1, 'Shackles', 0, 1, '2011-09-25 15:23:24', '2011-09-25 15:23:24'),
(7, 0, 'A', NULL, 11, 280, 1, 'Small bouquet of flower', 0, 1, '2011-09-25 15:23:24', '2011-09-25 15:23:24'),
(8, 0, 'A', NULL, 12, 280, 1, 'Stone foot plate', 0, 1, '2011-09-25 15:23:24', '2011-09-25 15:23:24'),
(11, 158, 'A', NULL, 14, 280, 1, 'Pants', 0, 1, '2011-09-25 15:23:24', '2011-09-25 15:23:24'),
(13, 162, 'A', NULL, 13, 280, 1, 'Shirt', 0, 1, '2011-09-25 16:28:34', '2011-09-25 16:28:34'),
(15, 163, 'A', NULL, 11, 280, 2, 'Abnormal brain', 0, 1, '2011-09-25 16:31:59', '2011-09-25 16:31:59'),
(16, 164, 'A', NULL, 17, 280, 1, 'Ill-fitting jacket', 0, 1, '2011-09-25 16:39:55', '2011-09-25 16:39:55'),
(17, 165, 'A', NULL, 11, 280, 1, 'Labeled brain jar', 0, 1, '2011-09-25 16:39:55', '2011-09-25 16:39:55'),
(18, 166, 'A', NULL, 11, 280, 1, 'Shackles', 0, 1, '2011-09-25 16:39:55', '2011-09-25 16:39:55'),
(19, 167, 'A', NULL, 11, 280, 1, 'Small bouquet of flowers ', 0, 1, '2011-09-25 16:39:55', '2011-09-25 16:39:55'),
(20, 168, 'A', NULL, 12, 280, 1, 'Stone foot plate', 0, 1, '2011-09-25 16:39:55', '2011-09-25 16:39:55'),
(21, 172, 'A', NULL, 11, 292, 1, '56 tarot cards', 0, 1, '2011-09-26 18:57:42', '2011-09-26 18:57:42'),
(22, 175, 'A', NULL, 3, 201, 20, 'LED Tri-laser function', 0, 1, '2011-09-28 03:54:59', '2011-09-28 03:54:59'),
(23, 176, 'A', NULL, 6, 263, 1, 'Interchangeable head with Johnny Depp likeness', 0, 1, '2011-09-28 16:17:33', '2011-09-28 16:17:33'),
(24, 177, 'A', NULL, 6, 263, 1, 'Interchangeable head with Johnny Depp likeness', 0, 1, '2011-09-28 16:17:33', '2011-09-28 16:17:33'),
(25, 178, 'A', NULL, 7, 263, 1, 'Pair relaxed', 0, 1, '2011-09-28 16:17:33', '2011-09-28 16:17:33'),
(26, 179, 'A', NULL, 7, 263, 1, 'Pair for holding flintlock pistol', 0, 1, '2011-09-28 16:19:47', '2011-09-28 16:19:47'),
(27, 180, 'A', NULL, 7, 263, 1, 'Right hand for holding saber', 0, 1, '2011-09-28 16:19:47', '2011-09-28 16:19:47'),
(28, 181, 'A', NULL, 7, 263, 1, 'Pair for holding rudder or telescope', 0, 1, '2011-09-28 16:19:47', '2011-09-28 16:19:47'),
(29, 182, 'A', NULL, 7, 263, 1, 'Pair showing his iconic gesture', 0, 1, '2011-09-28 16:19:47', '2011-09-28 16:19:47'),
(30, 183, 'A', NULL, 15, 263, 1, 'faux-leather detachable brown hat', 0, 1, '2011-09-28 16:19:47', '2011-09-28 16:19:47'),
(31, 184, 'A', NULL, 13, 263, 1, 'cream-colored long-sleeved shirt', 0, 1, '2011-09-28 16:19:47', '2011-09-28 16:19:47'),
(32, 185, 'A', NULL, 17, 263, 1, 'blue and brown patterned vest', 0, 1, '2011-09-28 16:20:42', '2011-09-28 16:20:42'),
(33, 186, 'A', NULL, 17, 263, 1, 'dark brown long jacket', 0, 1, '2011-09-28 16:20:42', '2011-09-28 16:20:42'),
(34, 187, 'A', NULL, 18, 263, 1, 'pair of dark brown pants', 0, 1, '2011-09-28 16:20:42', '2011-09-28 16:20:42'),
(35, 188, 'D', 614, 18, 263, 1, 'pair of dark brown pants', 0, 1, '2011-09-28 16:22:19', '2011-09-28 16:22:19'),
(36, 189, 'A', NULL, 14, 263, 1, 'pair of dark brown pants', 0, 1, '2011-09-28 16:22:19', '2011-09-28 16:22:19'),
(37, 190, 'A', NULL, 18, 263, 1, 'dark brown faux-leather with buckle', 0, 1, '2011-09-28 16:24:06', '2011-09-28 16:24:06'),
(38, 191, 'A', NULL, 18, 263, 1, 'light brown faux-leather with buckle', 0, 1, '2011-09-28 16:24:06', '2011-09-28 16:24:06'),
(39, 192, 'A', NULL, 18, 263, 1, 'shoulder band with saber sheath', 0, 1, '2011-09-28 16:24:06', '2011-09-28 16:24:06'),
(40, 193, 'A', NULL, 16, 263, 1, 'pair of brown boots', 0, 1, '2011-09-28 16:24:06', '2011-09-28 16:24:06'),
(41, 194, 'A', NULL, 8, 263, 1, 'flintlock pistol', 0, 1, '2011-09-28 16:24:06', '2011-09-28 16:24:06'),
(42, 195, 'A', NULL, 8, 263, 1, 'flintlock pistol', 0, 1, '2011-09-28 16:24:06', '2011-09-28 16:24:06'),
(43, 196, 'A', NULL, 8, 263, 1, 'saber', 0, 1, '2011-09-28 16:24:06', '2011-09-28 16:24:06'),
(44, 197, 'A', NULL, 13, 263, 1, 'left lace wristband', 0, 1, '2011-09-28 16:27:27', '2011-09-28 16:27:27'),
(45, 198, 'A', NULL, 13, 263, 1, 'right lace wristband', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(46, 199, 'A', NULL, 13, 263, 1, 'scarf', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(47, 200, 'A', NULL, 11, 263, 1, 'piece of feather on waist', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(48, 201, 'A', NULL, 11, 263, 1, 'compass', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(49, 202, 'A', NULL, 11, 263, 1, 'small telescope', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(50, 203, 'A', NULL, 11, 263, 1, 'head sculpt of his mother', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(51, 204, 'A', NULL, 11, 263, 1, 'duck''s foot sculpture', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(52, 205, 'A', NULL, 11, 263, 1, 'bottle of rum', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(53, 206, 'A', NULL, 11, 263, 1, 'The Black Pearl in a Bottle', 0, 1, '2011-09-28 16:27:28', '2011-09-28 16:27:28'),
(54, 212, 'A', NULL, 20, 327, 1, 'Autographed by H.R. Giger', 0, 1, '2011-09-28 18:40:41', '2011-09-28 18:40:41'),
(55, 214, 'A', NULL, 6, 349, 1, 'Detachable hair sculpture for wearing helmet ', 0, 1, '2011-09-28 18:52:30', '2011-09-28 18:52:30'),
(56, 215, 'A', NULL, 7, 349, 1, 'pair of fists (right fist is gloved) ', 0, 1, '2011-09-28 18:52:30', '2011-09-28 18:52:30'),
(57, 216, 'A', NULL, 7, 349, 1, 'pair of relaxed palms (right palm is gloved) ', 0, 1, '2011-09-28 18:52:30', '2011-09-28 18:52:30'),
(58, 217, 'A', NULL, 7, 349, 1, 'pair for holding hammer (right palm is gloved)', 0, 1, '2011-09-28 18:52:30', '2011-09-28 18:52:30'),
(59, 218, 'A', NULL, 21, 349, 1, 'detachable red cape ', 0, 1, '2011-09-28 19:00:09', '2011-09-28 19:00:09'),
(60, 219, 'A', NULL, 13, 349, 1, 'black shirt with silver patterned long sleeves', 0, 1, '2011-09-28 19:00:09', '2011-09-28 19:00:09'),
(61, 220, 'A', NULL, 14, 349, 1, 'pair of black pants', 0, 1, '2011-09-28 19:00:09', '2011-09-28 19:00:09'),
(62, 221, 'A', NULL, 16, 349, 1, 'pair of black boots with red stripes at the back', 0, 1, '2011-09-28 19:00:09', '2011-09-28 19:00:09'),
(63, 222, 'A', NULL, 8, 349, 1, 'authentic metal hammer with patterned holder', 0, 1, '2011-09-28 19:00:09', '2011-09-28 19:00:09'),
(64, 223, 'A', NULL, 15, 349, 1, 'movie-accurate helmet ', 0, 1, '2011-09-28 19:00:10', '2011-09-28 19:00:10'),
(65, 224, 'A', NULL, 12, 349, 1, 'Figure stand with Thor nameplate and film logo', 0, 1, '2011-09-28 19:00:10', '2011-09-28 19:00:10'),
(66, 232, 'A', NULL, 11, 487, 20, 'Removable CPU Chip', 0, 1, '2011-09-30 02:36:29', '2011-09-30 02:36:29'),
(67, 233, 'A', NULL, 11, 479, 20, 'Removable CPU Chip', 0, 1, '2011-09-30 02:37:25', '2011-09-30 02:37:25'),
(68, 255, 'A', NULL, 12, 215, 24, 'Option to display on base or mount on wall.', 0, 1, '2011-10-02 06:18:06', '2011-10-02 06:18:06'),
(69, 256, 'A', NULL, 12, 214, 20, 'Desk top or wall mountable', 0, 1, '2011-10-03 07:54:00', '2011-10-03 07:54:00'),
(70, 285, 'D', 230, 3, 174, 11, 'Light up eyes and base', 0, 1, '2011-10-18 07:59:07', '2011-10-18 07:59:07'),
(71, 289, 'E', 1190, 12, 865, 1, 'Penguin Waiter Figure from the Ink and Paint Club', 0, 1, '2011-10-18 16:20:39', '2011-10-18 16:20:39'),
(72, 299, 'A', NULL, 20, 868, 1, 'Certificate of Authenticity with Harrison Ford''s signature.', 0, 1, '2011-10-23 09:43:46', '2011-10-23 09:43:46'),
(73, 301, 'A', NULL, 22, 1010, 10, 'Clothing and furniture reproduced in scale with precise detail', 0, 1, '2011-10-23 10:19:49', '2011-10-23 10:19:49'),
(74, 302, 'A', NULL, 12, 1010, 10, 'Handsome display base ', 0, 1, '2011-10-23 10:19:49', '2011-10-23 10:19:49'),
(75, 313, 'A', NULL, 3, 112, 11, 'Lightsaber features electronic light function ', 0, 1, '2011-10-26 06:04:39', '2011-10-26 06:04:39'),
(76, 351, 'D', 1558, 7, 1304, 1, 'Hands holding Claws of Hades', 0, 1, '2011-11-03 15:50:26', '2011-11-03 15:50:26'),
(77, 356, 'E', 1434, 14, 1174, 1, 'Fabric shorts', 0, 1, '2011-11-06 09:10:07', '2011-11-06 09:10:07'),
(78, 357, 'A', NULL, 24, 1174, 1, 'Fabric Socks', 0, 1, '2011-11-06 09:10:07', '2011-11-06 09:10:07'),
(79, 358, 'A', NULL, 23, 1174, 1, 'Fabric Short Robe', 0, 1, '2011-11-06 09:10:07', '2011-11-06 09:10:07'),
(80, 364, 'A', NULL, 11, 1224, 1, 'Detailed Stack of Toys with Fabric Drawstring Sack', 0, 1, '2011-11-10 14:50:40', '2011-11-10 14:50:40'),
(81, 367, 'E', 1523, 7, 1264, 1, 'Right hand for alternate unmasked display!', 0, 1, '2011-11-12 22:58:45', '2011-11-12 22:58:45'),
(82, 375, 'D', 355, 12, 254, 11, 'Figure stand with Barney Ross nameplate and movie ', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(83, 376, 'A', NULL, 6, 254, 11, 'Authentic and detailed likeness of Sylvester Stallone as Barney Ross', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(84, 377, 'A', NULL, 7, 254, 11, 'Seven (7) switch-out hands ', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(85, 378, 'A', NULL, 15, 254, 11, 'One (1) beret', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(86, 379, 'A', NULL, 16, 254, 11, 'One (1) pair of black boots', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(87, 380, 'A', NULL, 13, 254, 11, 'One (1) round-neck black T-shirt', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(88, 381, 'A', NULL, 22, 254, 11, 'One (1) tactical vest ', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(89, 382, 'A', NULL, 14, 254, 11, 'One (1) pair of black military trousers', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(90, 383, 'A', NULL, 18, 254, 11, 'One (1) faux leather black belt', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(91, 384, 'A', NULL, 13, 254, 11, 'One (1) V-neck green T-shirt', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(92, 385, 'A', NULL, 14, 254, 11, 'One (1) pair of blue jeans', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(93, 386, 'A', NULL, 18, 254, 11, 'One (1) faux leather brown belt', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(94, 387, 'A', NULL, 8, 254, 11, 'Two (2) pistols with pouches', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(95, 388, 'A', NULL, 8, 254, 11, 'One (1) revolver with pouch', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(96, 389, 'A', NULL, 8, 254, 11, 'One (1) assault rifle', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(97, 390, 'A', NULL, 8, 254, 11, 'Two (2) packs of magazines with pouches', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(98, 391, 'A', NULL, 11, 254, 11, 'One (1) watch', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(99, 392, 'A', NULL, 11, 254, 11, 'One (1) pair of sunglasses', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(100, 393, 'A', NULL, 11, 254, 11, 'One (1) pair of goggles ', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(101, 394, 'A', NULL, 11, 254, 11, 'Two (2) necklaces including one (1) with cross, one (1) with military plate and danger sign pendant', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(102, 395, 'A', NULL, 12, 254, 11, 'Figure stand with Barney Ross nameplate and movie logo', 0, 1, '2011-11-14 05:38:43', '2011-11-14 05:38:43'),
(103, 419, 'E', 1831, 3, 1520, 1, 'Functioning LED head lights', 0, 1, '2011-11-21 19:36:15', '2011-11-21 19:36:15'),
(104, 455, 'A', NULL, 8, 1821, 1, 'Cross with removable knife', 0, 1, '2011-12-03 11:36:46', '2011-12-03 11:36:46'),
(105, 456, 'A', NULL, 8, 1821, 1, 'Railroad spike', 0, 1, '2011-12-03 11:36:46', '2011-12-03 11:36:46'),
(106, 475, 'A', NULL, 6, 415, 20, 'Switch-out head with facehugger', 0, 1, '2011-12-07 22:22:51', '2011-12-07 22:22:51'),
(107, 493, 'A', NULL, 3, 1015, 20, 'L.E.D light up chest piece', 0, 1, '2012-01-12 08:33:45', '2012-01-12 08:33:45'),
(108, 494, 'A', NULL, 3, 1015, 20, 'L.E.D light up lightsaber', 0, 1, '2012-01-12 08:33:46', '2012-01-12 08:33:46'),
(109, 508, 'A', NULL, 6, 2282, 1, 'Switch-out human head', 0, 1, '2012-01-20 13:44:06', '2012-01-20 13:44:06'),
(110, 509, 'A', NULL, 6, 2282, 1, 'Switch-out vampire head', 0, 1, '2012-01-20 13:44:06', '2012-01-20 13:44:06'),
(111, 510, 'A', NULL, 7, 2282, 1, 'Switch-out right hand, fist', 0, 1, '2012-01-20 13:44:06', '2012-01-20 13:44:06'),
(112, 511, 'A', NULL, 7, 2282, 1, 'Switch-out right hand, axe-wielding', 0, 1, '2012-01-20 13:44:06', '2012-01-20 13:44:06'),
(113, 512, 'A', NULL, 7, 2282, 1, 'Switch-out right hand, railroad spike-wielding', 0, 1, '2012-01-20 13:44:06', '2012-01-20 13:44:06'),
(114, 525, 'D', 2427, 8, 1865, 1, 'Pistol', 0, 1, '2012-01-27 22:37:12', '2012-01-27 22:37:12'),
(115, 526, 'A', NULL, 26, 1865, 1, 'Pistol', 0, 1, '2012-01-27 22:37:12', '2012-01-27 22:37:12'),
(116, 535, 'D', 2856, 8, 2194, 1, 'MP-5, pistol', 0, 1, '2012-02-02 20:07:46', '2012-02-02 20:07:46'),
(117, 536, 'A', NULL, 26, 2194, 1, 'MP-5', 0, 1, '2012-02-02 20:07:46', '2012-02-02 20:07:46'),
(118, 537, 'A', NULL, 26, 2194, 1, 'Pistol', 0, 1, '2012-02-02 20:07:46', '2012-02-02 20:07:46'),
(119, 539, 'A', NULL, 3, 871, 20, 'Light up wrist computer.', 0, 1, '2012-02-03 08:42:05', '2012-02-03 08:42:05'),
(120, 544, 'D', 2514, 6, 1888, 1, 'Plastic officer cap', 0, 1, '2012-02-14 19:59:36', '2012-02-14 19:59:36'),
(121, 545, 'D', 3677, 6, 1888, 1, 'Cloth officer cap', 0, 1, '2012-02-14 19:59:36', '2012-02-14 19:59:36'),
(122, 546, 'A', NULL, 15, 1888, 1, 'Plastic officer cap', 0, 1, '2012-02-14 19:59:36', '2012-02-14 19:59:36'),
(123, 547, 'A', NULL, 15, 1888, 1, 'Cloth officer cap', 0, 1, '2012-02-14 19:59:36', '2012-02-14 19:59:36'),
(124, 600, 'A', NULL, 5, 2422, 72, 'Road to Rivendell artprint', 0, 1, '2012-03-11 11:33:14', '2012-03-11 11:33:14');
