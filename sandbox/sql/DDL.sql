-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Hostiteľ: localhost
-- Vygenerované: St 21.Jan 2015, 19:19
-- Verzia serveru: 5.6.12-log
-- Verzia PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáza: `dspv`
--
CREATE DATABASE IF NOT EXISTS `dspv` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `dspv`;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny kľúč entity group',
  `str_group_password` varchar(45) DEFAULT NULL COMMENT 'Heslo k skupine v hash forme',
  `str_group_name` varchar(45) DEFAULT NULL COMMENT 'názov skupiny',
  `id_user` int(11) DEFAULT NULL COMMENT 'používateľ, ktorý vytvoril skupinu',
  `dt_created` datetime DEFAULT NULL COMMENT 'Čas vytvorenia skupiny',
  `str_group_description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_USER_idx` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `class`
--

INSERT INTO `class` (`id`, `str_group_password`, `str_group_name`, `id_user`, `dt_created`, `str_group_description`) VALUES
(1, '123456', 'Prvá skupina', 3, '2014-12-03 22:48:22', 'Prvá');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny kľúč entity task',
  `id_user` int(11) DEFAULT NULL COMMENT 'používateľ, ktorému patrí daný príklad',
  `id_unit` int(11) DEFAULT NULL COMMENT 'jednotka, z ktorej bolo prevádzané na základnú jednotku',
  `dt_created` datetime DEFAULT NULL COMMENT 'Čas vytvorenia príkladu',
  `dt_updated` datetime DEFAULT NULL COMMENT 'Čas update príkladu - zadanie riešenia',
  `nb_value_from` double DEFAULT NULL COMMENT 'Číslo z akého sa premiena, napr. "12.6" ',
  `nb_power_from` int(11) DEFAULT NULL COMMENT 'Mocnina z akej premieňame',
  `nb_value_to` double DEFAULT NULL COMMENT 'Základný tvar čísla, ktorý zadal užívateľ',
  `nb_power_to` int(11) DEFAULT NULL COMMENT 'Mocninu akú užívateľ zadal',
  `fl_correct` varchar(1) DEFAULT NULL COMMENT 'Správnosť vyriešenia príkladu',
  `id_test` int(11) DEFAULT NULL COMMENT 'Cudzí kľúč na test, ak daný príklad patrí k testu',
  `nb_power_base_to` int(11) DEFAULT NULL,
  `nb_hints` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_USER_idx` (`id_user`),
  KEY `FK_UNIT_idx` (`id_unit`),
  KEY `FK_TEST_idx` (`id_test`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny kľúč',
  `id_group` int(11) DEFAULT NULL COMMENT 'Cuzdí kľúč na grupu',
  `nb_level` int(11) DEFAULT NULL COMMENT 'Zadaná obtiažnosť',
  `nb_count` int(11) DEFAULT NULL COMMENT 'Počet príkladov',
  `dt_created` datetime DEFAULT NULL COMMENT 'Čas zadania písomky',
  `dt_closed` datetime DEFAULT NULL COMMENT 'Čas skončenia',
  `fl_closed` varchar(1) DEFAULT NULL COMMENT 'Flaga či je ešte aktívna',
  PRIMARY KEY (`id`),
  KEY `FK_GROUPT_idx` (`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
(91, 'N', 11, 'pm^2', -24, 2, 'Plocha'),
(92, 'N', 11, 'nm^2', -18, 2, 'Plocha'),
(93, 'N', 11, '&mu;m^2', -12, 2, 'Plocha'),
(94, 'N', 11, 'mm^2', -6, 2, 'Plocha'),
(95, 'N', 11, 'cm^2', -4, 2, 'Plocha'),
(96, 'N', 11, 'dm^2', -2, 2, 'Plocha'),
(97, 'A', 11, 'm^2', 0, 2, 'Plocha'),
(98, 'N', 11, 'km^2', 6, 2, 'Plocha'),
(99, 'N', 12, 'pm^3', -36, 2, 'Objem'),
(100, 'N', 12, 'nm^3', -27, 2, 'Objem'),
(101, 'N', 12, '&mu;m^3', -18, 2, 'Objem'),
(102, 'N', 12, 'mm^3', -9, 2, 'Objem'),
(103, 'N', 12, 'cm^3', -6, 2, 'Objem'),
(104, 'N', 12, 'dm^3', -3, 2, 'Objem'),
(105, 'A', 12, 'm^3', 0, 2, 'Objem'),
(106, 'N', 12, 'km^3', 9, 2, 'Objem'),
(107, 'N', 12, 'ml', -6, 2, 'Objem'),
(108, 'N', 12, 'cl', -5, 2, 'Objem'),
(109, 'N', 12, 'dl', -4, 2, 'Objem'),
(110, 'N', 12, 'l', -3, 2, 'Objem');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny klúč entity user',
  `str_name` varchar(45) DEFAULT NULL COMMENT 'Meno usera',
  `str_mail` varchar(45) DEFAULT NULL COMMENT 'E-mail používateľa',
  `str_user_password` varchar(255) DEFAULT NULL COMMENT 'Heslo používateľa v hash forme',
  `str_pass_hash` varchar(255) DEFAULT NULL COMMENT 'Zahashovany string potrebny pri obnove hesla',
  `id_group` int(11) DEFAULT NULL COMMENT 'id_group, kam žiak patrí',
  `fl_user_type` varchar(1) DEFAULT NULL COMMENT 'označenie, či sa jedná o učiteľa/žiaka',
  `dt_registration` datetime DEFAULT NULL COMMENT 'Čas registrácie',
  `dt_login` datetime DEFAULT NULL COMMENT 'Čas posledného loginu',
  PRIMARY KEY (`id`),
  KEY `FK_GROUP_idx` (`id_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Sťahujem dáta pre tabuľku `user`
--

INSERT INTO `user` (`id`, `str_name`, `str_mail`, `str_user_password`, `str_pass_hash`, `id_group`, `fl_user_type`, `dt_registration`, `dt_login`) VALUES
(1, 'MK ADMIN', 'adminko@adminko.adminko', '$2y$10$4NEDQ0AzBDQaqRdZXAiZJOQvnnboF8G7nYD.T7Ut.TGm31/DRR0Fm', NULL, NULL, 'A', '2014-12-03 22:49:53', '2014-12-03 22:54:37'),
(2, 'Janko Hraško', 'student@student.student', '$2y$10$9waHstqYZqZ5uJ2s3wpN5Ot0KD1Yqu7jxdxjDbawIEWlnisDv.gMu', NULL, 1, 'S', '2014-12-03 22:49:24', NULL),
(3, 'Učiteľ Adam', 'teacher@teacher.teacher', '$2y$10$jVMr4ezkjzdzKm3gmqL2puE87JiQixik36oDIqMipkbWFsF0BRlZO', NULL, NULL, 'T', '2014-12-03 22:47:56', '2014-12-03 22:53:15');

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `FK_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Obmedzenie pre tabuľku `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_USER_tsk` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_UNIT` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_TEST` FOREIGN KEY (`id_test`) REFERENCES `test` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Obmedzenie pre tabuľku `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `FK_GROUPT` FOREIGN KEY (`id_group`) REFERENCES `class` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Obmedzenie pre tabuľku `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_GROUP` FOREIGN KEY (`id_group`) REFERENCES `class` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
