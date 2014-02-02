-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 02, 2014 at 02:40 PM
-- Server version: 5.5.34
-- PHP Version: 5.3.10-1ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bar_sales`
--

-- --------------------------------------------------------

--
-- Table structure for table `event_record`
--

CREATE TABLE IF NOT EXISTS `event_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type_id` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_type`
--

CREATE TABLE IF NOT EXISTS `event_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `incoming_stock_record`
--

CREATE TABLE IF NOT EXISTS `incoming_stock_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `supplier` varchar(250) NOT NULL,
  `notes` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_record`
--

CREATE TABLE IF NOT EXISTS `sales_record` (
  `event_record_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `number_sold` int(11) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `cost` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `cost_price` decimal(4,2) NOT NULL,
  `retail_price` decimal(4,2) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `stock_in`
--

CREATE TABLE IF NOT EXISTS `stock_in` (
  `incoming_stock_record_id` int(11) NOT NULL,
  `stock_id` int(4) NOT NULL,
  `number_received` int(4) NOT NULL,
  `bought_at` decimal(4,2) NOT NULL,
  KEY `incoming_stock_record_id` (`incoming_stock_record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_level`
--

CREATE TABLE IF NOT EXISTS `stock_level` (
  `stock_take_record_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `shrinkage` int(11) NOT NULL,
  `cost_price` decimal(4,2) NOT NULL,
  `retail_price` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_take_record`
--

CREATE TABLE IF NOT EXISTS `stock_take_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `who` varchar(250) NOT NULL,
  `notes` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
