-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2014 at 08:55 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nsca`
--

-- --------------------------------------------------------

--
-- Table structure for table `club`
--

CREATE TABLE IF NOT EXISTS `club` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nscaClubId` int(11) DEFAULT NULL,
  `clubName` varchar(100) DEFAULT NULL,
  UNIQUE KEY `id` (`id`,`nscaClubId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eventshooter`
--

CREATE TABLE IF NOT EXISTS `eventshooter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shootEventId` int(11) DEFAULT NULL,
  `shooterId` int(11) DEFAULT NULL,
  `hoaOption` bit(1) DEFAULT NULL,
  `hicOption` bit(1) DEFAULT NULL,
  `LewisOption` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shootEventId` (`shootEventId`),
  KEY `shooterId` (`shooterId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Keeps' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `eventstation`
--

CREATE TABLE IF NOT EXISTS `eventstation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shootEventId` int(11) DEFAULT NULL,
  `stationNumber` tinyint(2) DEFAULT NULL,
  `maxScore` tinyint(2) DEFAULT NULL,
  `tieBreakerPosition` int(11) DEFAULT NULL,
  `stationDetail` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shootEventId` (`shootEventId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `registeredshoot`
--

CREATE TABLE IF NOT EXISTS `registeredshoot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clubId` int(11) DEFAULT NULL,
  `shootName` varchar(50) DEFAULT NULL,
  `shootDate` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clubId` (`clubId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shooter`
--

CREATE TABLE IF NOT EXISTS `shooter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nscaId` int(11) DEFAULT NULL,
  `firstName` varchar(40) DEFAULT NULL,
  `lastName` varchar(40) DEFAULT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `state` varchar(5) DEFAULT NULL,
  `nscaClass` varchar(3) DEFAULT NULL,
  `nscaConcurrent` varchar(5) DEFAULT NULL,
  `nscaConcurrentLady` bit(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nscaId` (`nscaId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shootereventstationscore`
--

CREATE TABLE IF NOT EXISTS `shootereventstationscore` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `eventShooterId` int(11) DEFAULT NULL,
  `eventStationId` int(11) DEFAULT NULL,
  `targetsBroken` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eventShooterId` (`eventShooterId`),
  KEY `eventStationId` (`eventStationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shootevent`
--

CREATE TABLE IF NOT EXISTS `shootevent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shootId` int(11) DEFAULT NULL,
  `eventType` varchar(10) DEFAULT NULL,
  `registrationCost` smallint(6) DEFAULT NULL,
  `hicCost` smallint(6) DEFAULT NULL,
  `hoaCost` smallint(6) DEFAULT NULL,
  `lewisCost` smallint(6) DEFAULT NULL,
  `lewisGroups` tinyint(2) DEFAULT NULL,
  `stations` int(11) DEFAULT NULL,
  `targets` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shootId` (`shootId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eventshooter`
--
ALTER TABLE `eventshooter`
  ADD CONSTRAINT `eventshooter_ibfk_2` FOREIGN KEY (`shooterId`) REFERENCES `shooter` (`id`),
  ADD CONSTRAINT `eventshooter_ibfk_1` FOREIGN KEY (`shootEventId`) REFERENCES `shootevent` (`id`);

--
-- Constraints for table `eventstation`
--
ALTER TABLE `eventstation`
  ADD CONSTRAINT `eventstation_ibfk_1` FOREIGN KEY (`shootEventId`) REFERENCES `shootevent` (`id`);

--
-- Constraints for table `registeredshoot`
--
ALTER TABLE `registeredshoot`
  ADD CONSTRAINT `registeredshoot_ibfk_1` FOREIGN KEY (`clubId`) REFERENCES `club` (`id`);

--
-- Constraints for table `shootereventstationscore`
--
ALTER TABLE `shootereventstationscore`
  ADD CONSTRAINT `shootereventstationscore_ibfk_2` FOREIGN KEY (`eventStationId`) REFERENCES `eventstation` (`id`),
  ADD CONSTRAINT `shootereventstationscore_ibfk_1` FOREIGN KEY (`eventShooterId`) REFERENCES `eventshooter` (`id`);

--
-- Constraints for table `shootevent`
--
ALTER TABLE `shootevent`
  ADD CONSTRAINT `shootevent_ibfk_1` FOREIGN KEY (`shootId`) REFERENCES `registeredshoot` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
