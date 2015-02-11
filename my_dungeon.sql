-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 11, 2015 at 04:16 PM
-- Server version: 5.1.71-community-log
-- PHP Version: 5.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `my_dungeon`
--

-- --------------------------------------------------------

--
-- Table structure for table `Aggiornamento`
--

CREATE TABLE IF NOT EXISTS `Aggiornamento` (
  `Oggi` date NOT NULL,
  `IDOggi` int(11) NOT NULL,
  PRIMARY KEY (`IDOggi`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Aggiornamento`
--

INSERT INTO `Aggiornamento` (`Oggi`, `IDOggi`) VALUES
('2015-02-11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Date`
--

CREATE TABLE IF NOT EXISTS `Date` (
  `Data` datetime NOT NULL,
  `Davide` set('0','1','2') NOT NULL DEFAULT '0',
  `Matteo` set('0','1','2') NOT NULL DEFAULT '0',
  `Andrea` set('0','1','2') NOT NULL DEFAULT '0',
  `Alessandro` set('0','1','2') NOT NULL DEFAULT '0',
  `Antonello` set('0','1','2') NOT NULL DEFAULT '0',
  `Morris` set('0','1','2') NOT NULL DEFAULT '0',
  `Beatrice` set('0','1','2') NOT NULL DEFAULT '0',
  `Marco` set('0','1','2') NOT NULL DEFAULT '0',
  `Leonardo` set('0','1','2') NOT NULL DEFAULT '0',
  `Agnese` set('0','1','2') NOT NULL DEFAULT '0',
  PRIMARY KEY (`Data`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Date`
--

INSERT INTO `Date` (`Data`, `Davide`, `Matteo`, `Andrea`, `Alessandro`, `Antonello`, `Morris`, `Beatrice`, `Marco`, `Leonardo`, `Agnese`) VALUES
('2015-02-09 22:00:00', '2', '1', '0', '1', '0', '1', '1', '1', '0', '1'),
('2015-02-15 21:15:00', '2', '1', '1', '1', '0', '1', '0', '2', '2', '0');

-- --------------------------------------------------------

--
-- Table structure for table `Giocatori`
--

CREATE TABLE IF NOT EXISTS `Giocatori` (
  `Nome` varchar(30) NOT NULL,
  `Short` varchar(1) DEFAULT NULL,
  `Password` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`Nome`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Giocatori`
--

INSERT INTO `Giocatori` (`Nome`, `Short`, `Password`) VALUES
('Davide', 'D', NULL),
('Morris', 'M', NULL),
('Alessandro', 'P', NULL),
('Marco', 'B', NULL),
('Beatrice', 'T', NULL),
('Antonello', 'N', NULL),
('Andrea', 'A', NULL),
('Leonardo', 'L', NULL),
('Matteo', 'F', NULL),
('Agnese', 'S', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Giorni`
--

CREATE TABLE IF NOT EXISTS `Giorni` (
  `Data` date NOT NULL,
  `Davide` set('0','1','2') NOT NULL DEFAULT '0',
  `Matteo` set('0','1','2') NOT NULL DEFAULT '0',
  `Andrea` set('0','1','2') NOT NULL DEFAULT '0',
  `Alessandro` set('0','1','2') NOT NULL DEFAULT '0',
  `Antonello` set('0','1','2') NOT NULL DEFAULT '0',
  `Morris` set('0','1','2') NOT NULL DEFAULT '0',
  `Beatrice` set('0','1','2') NOT NULL DEFAULT '0',
  `Marco` set('0','1','2') NOT NULL DEFAULT '0',
  `Leonardo` set('0','1','2') NOT NULL DEFAULT '0',
  `Agnese` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Data`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Giorni`
--

INSERT INTO `Giorni` (`Data`, `Davide`, `Matteo`, `Andrea`, `Alessandro`, `Antonello`, `Morris`, `Beatrice`, `Marco`, `Leonardo`, `Agnese`) VALUES
('2015-02-22', '2', '1', '0', '0', '0', '1', '1', '2', '0', 1),
('2015-02-23', '2', '0', '0', '0', '0', '0', '0', '1', '0', 2),
('2015-02-24', '1', '0', '0', '0', '0', '0', '0', '0', '0', 2),
('2015-02-25', '2', '0', '0', '0', '0', '0', '0', '0', '0', 2),
('2015-02-11', '2', '0', '0', '1', '0', '1', '0', '2', '0', 0),
('2015-02-12', '1', '0', '1', '1', '0', '1', '0', '1', '0', 0),
('2015-02-13', '1', '0', '1', '2', '0', '2', '1', '0', '0', 1),
('2015-02-14', '0', '0', '0', '0', '0', '0', '0', '0', '0', 0),
('2015-02-15', '2', '1', '1', '1', '0', '1', '0', '2', '2', 0),
('2015-02-16', '0', '1', '0', '0', '0', '1', '0', '1', '0', 2),
('2015-02-17', '0', '0', '0', '0', '0', '1', '0', '0', '0', 2),
('2015-02-18', '2', '1', '0', '0', '0', '1', '0', '1', '0', 2),
('2015-02-19', '1', '1', '0', '0', '0', '2', '1', '0', '0', 2),
('2015-02-20', '1', '1', '0', '0', '0', '0', '0', '2', '0', 0),
('2015-02-21', '0', '1', '0', '0', '0', '0', '0', '0', '0', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
  `IDLog` int(8) NOT NULL AUTO_INCREMENT,
  `Tempo` datetime NOT NULL,
  `Nome` varchar(30) NOT NULL,
  `Azione` enum('0','1','2','3','4','5','6','7','8','9') NOT NULL,
  PRIMARY KEY (`IDLog`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=108 ;

--
-- Dumping data for table `Log`
--

INSERT INTO `Log` (`IDLog`, `Tempo`, `Nome`, `Azione`) VALUES
(55, '2015-02-11 13:27:53', 'Agnese', '3'),
(54, '2015-02-11 13:27:46', 'Agnese', '4'),
(53, '2015-02-11 13:27:41', 'Agnese', '5'),
(52, '2015-02-11 13:27:41', 'Agnese', '1'),
(51, '2015-02-11 10:34:54', 'Beatrice', '3'),
(50, '2015-02-11 10:34:36', 'Beatrice', '4'),
(49, '2015-02-11 10:34:16', 'Beatrice', '5'),
(48, '2015-02-11 10:33:35', 'Beatrice', '4'),
(47, '2015-02-11 10:33:31', 'Beatrice', '5'),
(46, '2015-02-11 10:33:28', 'Beatrice', '2'),
(45, '2015-02-11 10:32:09', 'Beatrice', '5'),
(44, '2015-02-11 10:31:56', 'Beatrice', '4'),
(43, '2015-02-11 10:31:52', 'Beatrice', '3'),
(42, '2015-02-11 10:31:47', 'Beatrice', '5'),
(41, '2015-02-11 10:31:47', 'Beatrice', '1'),
(40, '2015-02-11 10:31:36', 'Beatrice', '5'),
(39, '2015-02-11 10:31:36', 'Beatrice', '1'),
(56, '2015-02-11 13:28:10', 'Agnese', '5'),
(57, '2015-02-11 13:28:16', 'Agnese', '2'),
(58, '2015-02-11 13:28:17', 'Agnese', '5'),
(59, '2015-02-11 13:28:20', 'Agnese', '4'),
(60, '2015-02-11 13:28:32', 'Agnese', '5'),
(61, '2015-02-11 13:28:37', 'Agnese', '2'),
(62, '2015-02-11 13:28:38', 'Agnese', '5'),
(63, '2015-02-11 13:35:48', 'Agnese', '0'),
(64, '2015-02-11 13:40:31', 'Agnese', '1'),
(65, '2015-02-11 13:40:31', 'Agnese', '5'),
(66, '2015-02-11 13:41:39', 'Agnese', '2'),
(67, '2015-02-11 13:41:41', 'Agnese', '5'),
(68, '2015-02-11 13:41:46', 'Agnese', '3'),
(69, '2015-02-11 13:41:55', 'Agnese', '5'),
(70, '2015-02-11 13:41:57', 'Agnese', '4'),
(71, '2015-02-11 13:42:34', 'Agnese', '5'),
(72, '2015-02-11 13:42:52', 'Agnese', '2'),
(73, '2015-02-11 13:42:55', 'Agnese', '5'),
(74, '2015-02-11 14:01:44', 'Agnese', '1'),
(75, '2015-02-11 14:01:44', 'Agnese', '5'),
(76, '2015-02-11 14:03:04', 'Agnese', '1'),
(77, '2015-02-11 14:03:04', 'Agnese', '5'),
(78, '2015-02-11 14:03:07', 'Agnese', '3'),
(79, '2015-02-11 14:04:18', 'Agnese', '4'),
(80, '2015-02-11 14:04:27', 'Agnese', '3'),
(81, '2015-02-11 14:05:36', 'Agnese', '3'),
(82, '2015-02-11 14:06:34', 'Agnese', '3'),
(83, '2015-02-11 14:10:40', 'Agnese', '3'),
(84, '2015-02-11 14:31:06', 'Agnese', '3'),
(85, '2015-02-11 14:31:14', 'Agnese', '5'),
(86, '2015-02-11 14:31:33', 'Agnese', '0'),
(87, '2015-02-11 14:42:26', 'Matteo', '1'),
(88, '2015-02-11 14:42:26', 'Matteo', '5'),
(89, '2015-02-11 14:42:30', 'Matteo', '4'),
(90, '2015-02-11 14:42:33', 'Matteo', '3'),
(91, '2015-02-11 14:42:36', 'Matteo', '3'),
(92, '2015-02-11 14:42:47', 'Matteo', '3'),
(93, '2015-02-11 14:43:23', 'Agnese', '3'),
(94, '2015-02-11 14:43:37', 'Agnese', '0'),
(95, '2015-02-11 14:44:30', 'Matteo', '0'),
(96, '2015-02-11 15:06:59', 'Agnese', '1'),
(97, '2015-02-11 15:06:59', 'Agnese', '5'),
(98, '2015-02-11 15:07:22', 'Agnese', '3'),
(99, '2015-02-11 15:08:17', 'Agnese', '3'),
(100, '2015-02-11 15:08:26', 'Agnese', '3'),
(101, '2015-02-11 15:14:35', 'Agnese', '3'),
(102, '2015-02-11 15:17:24', 'Agnese', '3'),
(103, '2015-02-11 15:17:57', 'Agnese', '3'),
(104, '2015-02-11 15:18:07', 'Agnese', '3'),
(105, '2015-02-11 15:18:13', 'Agnese', '3'),
(106, '2015-02-11 15:18:28', 'Agnese', '5'),
(107, '2015-02-11 15:18:45', 'Agnese', '0');

-- --------------------------------------------------------

--
-- Table structure for table `Posti`
--

CREATE TABLE IF NOT EXISTS `Posti` (
  `IDPosto` int(6) NOT NULL AUTO_INCREMENT,
  `Data` date DEFAULT NULL,
  `Posto` varchar(1) NOT NULL,
  PRIMARY KEY (`IDPosto`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `Posti`
--

INSERT INTO `Posti` (`IDPosto`, `Data`, `Posto`) VALUES
(2, '2015-02-15', ''),
(7, '2015-02-15', 'D');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
