-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2010 at 12:07 AM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `warzone1`
--

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `turn` int(11) NOT NULL DEFAULT '0' COMMENT 'which players turn it is. 0 player1, 1 player2',
  `status` int(11) NOT NULL COMMENT 'status of the match. 0 waiting for player, 1 waiting for server',
  `setname` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`id`, `turn`, `status`, `setname`) VALUES
(1, 1, 0, 'set1');

-- --------------------------------------------------------

--
-- Table structure for table `pawn`
--

DROP TABLE IF EXISTS `pawn`;
CREATE TABLE IF NOT EXISTS `pawn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `hitpoints` int(11) NOT NULL COMMENT 'hitpoints of the unit',
  `pierceattack` int(11) NOT NULL,
  `piercearmor` int(11) NOT NULL,
  `bashattack` int(11) NOT NULL,
  `basharmor` int(11) NOT NULL,
  `attackrange` int(11) NOT NULL,
  `walkrange` int(11) NOT NULL,
  `xpos` int(11) NOT NULL,
  `ypos` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pawn_player1` (`player_id`),
  KEY `fk_pawn_game1` (`game_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

--
-- Dumping data for table `pawn`
--

INSERT INTO `pawn` (`id`, `name`, `hitpoints`, `pierceattack`, `piercearmor`, `bashattack`, `basharmor`, `attackrange`, `walkrange`, `xpos`, `ypos`, `player_id`, `game_id`) VALUES
(1, 'Castle', 200, 0, 10, 0, 0, 0, 0, 5, 18, 1, 1),
(2, 'Knight', 100, 4, 10, 10, 6, 2, 6, 5, 11, 1, 1),
(3, 'Knight', 100, 4, 10, 10, 6, 2, 6, 5, 12, 1, 1),
(4, 'Knight', 100, 4, 10, 10, 6, 2, 6, 5, 24, 1, 1),
(5, 'Knight', 100, 4, 10, 10, 6, 2, 6, 5, 25, 1, 1),
(6, 'Archer', 25, 6, 6, 1, 2, 10, 4, 6, 16, 1, 1),
(7, 'Archer', 25, 6, 6, 1, 2, 10, 4, 6, 17, 1, 1),
(8, 'Archer', 25, 6, 6, 1, 2, 10, 4, 6, 18, 1, 1),
(9, 'Archer', 25, 6, 6, 1, 2, 10, 4, 6, 19, 1, 1),
(10, 'Archer', 25, 6, 6, 1, 2, 10, 4, 6, 20, 1, 1),
(11, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 8, 13, 1, 1),
(12, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 8, 14, 1, 1),
(13, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 8, 15, 1, 1),
(14, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 8, 21, 1, 1),
(15, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 8, 22, 1, 1),
(16, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 8, 23, 1, 1),
(17, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 10, 16, 1, 1),
(18, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 10, 17, 1, 1),
(19, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 10, 18, 1, 1),
(20, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 10, 19, 1, 1),
(21, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 10, 20, 1, 1),
(22, 'Castle', 200, 0, 10, 0, 0, 0, 0, 45, 18, 2, 1),
(23, 'Knight', 100, 4, 10, 10, 6, 2, 6, 45, 11, 2, 1),
(24, 'Knight', 100, 4, 10, 10, 6, 2, 6, 45, 12, 2, 1),
(25, 'Knight', 100, 4, 10, 10, 6, 2, 6, 45, 24, 2, 1),
(26, 'Knight', 100, 4, 10, 10, 6, 2, 6, 45, 25, 2, 1),
(27, 'Archer', 25, 6, 6, 1, 2, 10, 4, 44, 16, 2, 1),
(28, 'Archer', 25, 6, 6, 1, 2, 10, 4, 44, 17, 2, 1),
(29, 'Archer', 25, 6, 6, 1, 2, 10, 4, 44, 18, 2, 1),
(30, 'Archer', 25, 6, 6, 1, 2, 10, 4, 44, 19, 2, 1),
(31, 'Archer', 25, 6, 6, 1, 2, 10, 4, 44, 20, 2, 1),
(32, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 42, 13, 2, 1),
(33, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 42, 14, 2, 1),
(34, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 42, 15, 2, 1),
(35, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 42, 21, 2, 1),
(36, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 42, 22, 2, 1),
(37, 'Swordsman', 35, 2, 8, 6, 8, 1, 2, 42, 23, 2, 1),
(38, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 40, 16, 2, 1),
(39, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 40, 17, 2, 1),
(40, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 40, 18, 2, 1),
(41, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 40, 19, 2, 1),
(42, 'Halbherdier', 25, 12, 4, 4, 4, 2, 4, 40, 20, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

DROP TABLE IF EXISTS `player`;
CREATE TABLE IF NOT EXISTS `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `game_id` int(11) NOT NULL,
  `playercount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `playercol_UNIQUE` (`hash`),
  KEY `fk_player_game1` (`game_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`id`, `hash`, `email`, `game_id`, `playercount`) VALUES
(1, '8917cb8a99687bb033bbf92549f92eb3', 'example1@example.com', 1, 0),
(2, 'c74547f1d8d5ff03708a34963d326c21', 'example2@example.com', 1, 1);
