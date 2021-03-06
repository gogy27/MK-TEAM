-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Út 13.Jan 2015, 17:28
-- Verzia serveru: 5.6.15-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dspv`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `unit`
--

CREATE TABLE IF NOT EXISTS `unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny kľúč',
  `fl_base_unit` varchar(1) DEFAULT NULL COMMENT 'Flaga, či sa jedná o základnú jednotku',
  `nb_category` int(11) DEFAULT NULL COMMENT 'Kategória, do ktorej patrí veličina',
  `str_unit_name` varchar(45) DEFAULT NULL COMMENT 'Značka jednotky',
  `nb_multiple` int(11) DEFAULT NULL COMMENT 'Násobok jednotky, nemusí sa vzťahovať k základnej jednotke!',
  `nb_level` int(11) DEFAULT NULL COMMENT 'Náročnosť jednotky.',
  `str_unit_description` varchar(45) DEFAULT NULL COMMENT 'Popis jednotky\n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

--
-- Sťahujem dáta pre tabuľku `unit`
--

INSERT INTO `unit` (`id`, `fl_base_unit`, `nb_category`, `str_unit_name`, `nb_multiple`, `nb_level`, `str_unit_description`) VALUES
(1, 'N', 1, 'pJ', -12, 1, 'Práca'),
(2, 'N', 1, 'nJ', -9, 1, 'Práca'),
(3, 'N', 1, '&mu;J', -6, 1, 'Práca'),
(4, 'N', 1, 'mJ', -3, 1, 'Práca'),
(5, 'A', 1, 'J', 0, 1, 'Práca'),
(6, 'N', 1, 'kJ', 3, 1, 'Práca'),
(7, 'N', 1, 'MJ', 6, 1, 'Práca'),
(8, 'N', 1, 'GJ', 9, 1, 'Práca'),
(9, 'N', 1, 'TJ', 12, 1, 'Práca'),
(10, 'N', 2, 'pm', -12, 1, 'Dĺžka'),
(11, 'N', 2, 'nm', -9, 1, 'Dĺžka'),
(12, 'N', 2, '&mu;m', -6, 1, 'Dĺžka'),
(13, 'N', 2, 'mm', -3, 1, 'Dĺžka'),
(14, 'N', 2, 'cm', -2, 1, 'Dĺžka'),
(15, 'N', 2, 'dm', -1, 1, 'Dĺžka'),
(16, 'A', 2, 'm', 0, 1, 'Dĺžka'),
(17, 'N', 2, 'km', 3, 1, 'Dĺžka'),
(18, 'N', 3, 'pA', -12, 1, 'Elektrický prúd'),
(19, 'N', 3, 'nA', -9, 1, 'Elektrický prúd'),
(20, 'N', 3, '&mu;A', -6, 1, 'Elektrický prúd'),
(21, 'N', 3, 'mA', -3, 1, 'Elektrický prúd'),
(22, 'A', 3, 'A', 0, 1, 'Elektrický prúd'),
(23, 'N', 3, 'kA', 3, 1, 'Elektrický prúd'),
(24, 'N', 3, 'MA', 6, 1, 'Elektrický prúd'),
(25, 'N', 3, 'GA', 9, 1, 'Elektrický prúd'),
(26, 'N', 3, 'TA', 12, 1, 'Elektrický prúd'),
(27, 'N', 4, 'pV', -12, 1, 'Elektrické napätie'),
(28, 'N', 4, 'nV', -9, 1, 'Elektrické napätie'),
(29, 'N', 4, '&mu;V', -6, 1, 'Elektrické napätie'),
(30, 'N', 4, 'mV', -3, 1, 'Elektrické napätie'),
(31, 'A', 4, 'V', 0, 1, 'Elektrické napätie'),
(32, 'N', 4, 'kV', 3, 1, 'Elektrické napätie'),
(33, 'N', 4, 'MV', 6, 1, 'Elektrické napätie'),
(34, 'N', 4, 'GV', 9, 1, 'Elektrické napätie'),
(35, 'N', 4, 'TV', 12, 1, 'Elektrické napätie'),
(36, 'N', 5, 'p&Omega;', -12, 1, 'Elektrický odpor'),
(37, 'N', 5, 'n&Omega;', -9, 1, 'Elektrický odpor'),
(38, 'N', 5, '&mu;&Omega;', -6, 1, 'Elektrický odpor'),
(39, 'N', 5, 'm&Omega;', -3, 1, 'Elektrický odpor'),
(40, 'A', 5, '&Omega;', 0, 1, 'Elektrický odpor'),
(41, 'N', 5, 'k&Omega;', 3, 1, 'Elektrický odpor'),
(42, 'N', 5, 'M&Omega;', 6, 1, 'Elektrický odpor'),
(43, 'N', 5, 'G&Omega;', 9, 1, 'Elektrický odpor'),
(44, 'N', 5, 'T&Omega;', 12, 1, 'Elektrický odpor'),
(45, 'N', 6, 'pW', -12, 1, 'Výkon'),
(46, 'N', 6, 'nW', -9, 1, 'Výkon'),
(47, 'N', 6, '&mu;W', -6, 1, 'Výkon'),
(48, 'N', 6, 'mW', -3, 1, 'Výkon'),
(49, 'A', 6, 'W', 0, 1, 'Výkon'),
(50, 'N', 6, 'kW', 3, 1, 'Výkon'),
(51, 'N', 6, 'MW', 6, 1, 'Výkon'),
(52, 'N', 6, 'GW', 9, 1, 'Výkon'),
(53, 'N', 6, 'TW', 12, 1, 'Výkon'),
(54, 'N', 7, 'pg', -15, 1, 'Hmotnosť'),
(55, 'N', 7, 'ng', -12, 1, 'Hmotnosť'),
(56, 'N', 7, '&mu;g', -9, 1, 'Hmotnosť'),
(57, 'N', 7, 'mg', -6, 1, 'Hmotnosť'),
(58, 'N', 7, 'g', -3, 1, 'Hmotnosť'),
(59, 'A', 7, 'kg', 0, 1, 'Hmotnosť'),
(60, 'N', 7, 'tona', 3, 1, 'Hmotnosť'),
(61, 'N', 7, 'kilotona', 6, 1, 'Hmotnosť'),
(62, 'N', 7, 'megatona', 9, 1, 'Hmotnosť'),
(63, 'N', 7, 'gigatona', 12, 1, 'Hmotnosť'),
(64, 'N', 8, 'pN', -12, 1, 'Sila'),
(65, 'N', 8, 'nN', -9, 1, 'Sila'),
(66, 'N', 8, '&mu;N', -6, 1, 'Sila'),
(67, 'N', 8, 'mN', -3, 1, 'Sila'),
(68, 'A', 8, 'N', 0, 1, 'Sila'),
(69, 'N', 8, 'kN', 3, 1, 'Sila'),
(70, 'N', 8, 'MN', 6, 1, 'Sila'),
(71, 'N', 8, 'GN', 9, 1, 'Sila'),
(72, 'N', 8, 'TN', 12, 1, 'Sila'),
(73, 'N', 9, 'pPa', -12, 1, 'Tlak'),
(74, 'N', 9, 'nPa', -9, 1, 'Tlak'),
(75, 'N', 9, '&mu;Pa', -6, 1, 'Tlak'),
(76, 'N', 9, 'mPa', -3, 1, 'Tlak'),
(77, 'A', 9, 'Pa', 0, 1, 'Tlak'),
(78, 'N', 9, 'kPa', 3, 1, 'Tlak'),
(79, 'N', 9, 'MPa', 6, 1, 'Tlak'),
(80, 'N', 9, 'GPa', 9, 1, 'Tlak'),
(81, 'N', 9, 'TPa', 12, 1, 'Tlak'),
(82, 'N', 10, 'pHz', -12, 1, 'Frekvencia'),
(83, 'N', 10, 'nHz', -9, 1, 'Frekvencia'),
(84, 'N', 10, '&mu;Hz', -6, 1, 'Frekvencia'),
(85, 'N', 10, 'mHz', -3, 1, 'Frekvencia'),
(86, 'A', 10, 'Hz', 0, 1, 'Frekvencia'),
(87, 'N', 10, 'kHz', 3, 1, 'Frekvencia'),
(88, 'N', 10, 'MHz', 6, 1, 'Frekvencia'),
(89, 'N', 10, 'GHz', 9, 1, 'Frekvencia'),
(90, 'N', 10, 'THz', 12, 1, 'Frekvencia'),
(91, 'N', 11, 'pm<sup>2</sup>', -24, 2, 'Plocha'),
(92, 'N', 11, 'nm<sup>2</sup>', -18, 2, 'Plocha'),
(93, 'N', 11, '&mu;m<sup>2</sup>', -12, 2, 'Plocha'),
(94, 'N', 11, 'mm<sup>2</sup>', -6, 2, 'Plocha'),
(95, 'N', 11, 'cm<sup>2</sup>', -4, 2, 'Plocha'),
(96, 'N', 11, 'dm<sup>2</sup>', -2, 2, 'Plocha'),
(97, 'A', 11, 'm<sup>2</sup>', 0, 2, 'Plocha'),
(98, 'N', 11, 'km<sup>2</sup>', 6, 2, 'Plocha'),
(99, 'N', 12, 'pm<sup>3</sup>', -36, 2, 'Objem'),
(100, 'N', 12, 'nm<sup>3</sup>', -27, 2, 'Objem'),
(101, 'N', 12, '&mu;m<sup>3</sup>', -18, 2, 'Objem'),
(102, 'N', 12, 'mm<sup>3</sup>', -9, 2, 'Objem'),
(103, 'N', 12, 'cm<sup>3</sup>', -6, 2, 'Objem'),
(104, 'N', 12, 'dm<sup>3</sup>', -3, 2, 'Objem'),
(105, 'A', 12, 'm<sup>3</sup>', 0, 2, 'Objem'),
(106, 'N', 12, 'km<sup>3</sup>', 9, 2, 'Objem'),
(107, 'N', 12, 'ml', -6, 2, 'Objem'),
(108, 'N', 12, 'cl', -5, 2, 'Objem'),
(109, 'N', 12, 'dl', -4, 2, 'Objem'),
(110, 'N', 12, 'l', -3, 2, 'Objem');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
