-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Hostiteľ: localhost
-- Vygenerované: Út 25.Nov 2014, 22:47
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
CREATE DATABASE IF NOT EXISTS `dspv` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `dspv`;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny kľúč entity group',
  `str_group_password` varchar(45) DEFAULT NULL COMMENT 'Heslo k skupine v hash forme',
  `str_group_name` varchar(45) DEFAULT NULL COMMENT 'názov skupiny',
  `id_user` int(11) DEFAULT NULL COMMENT 'používateľ, ktorý vytvoril skupinu',
  `dt_created` datetime DEFAULT NULL COMMENT 'Čas vytvorenia skupiny',
  `str_group_description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_group`),
  KEY `FK_USER_idx` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Sťahujem dáta pre tabuľku `class`
--

INSERT INTO `class` (`id_group`, `str_group_password`, `str_group_name`, `id_user`, `dt_created`, `str_group_description`) VALUES
(1, '123456', 'Prvá skupina', 1, '2014-11-25 23:46:51', 'Toto je popis prvej skupiny. Je to uplne supa');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id_task` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny kľúč entity task',
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
  PRIMARY KEY (`id_task`),
  KEY `FK_USER_idx` (`id_user`),
  KEY `FK_UNIT_idx` (`id_unit`),
  KEY `FK_TEST_idx` (`id_test`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id_test` int(11) NOT NULL COMMENT 'Primárny kľúč',
  `id_group` int(11) DEFAULT NULL COMMENT 'Cuzdí kľúč na grupu',
  `nb_difficulty` int(11) DEFAULT NULL COMMENT 'Zadaná obtiažnosť',
  `nb_count` int(11) DEFAULT NULL COMMENT 'Počet príkladov',
  `dt_created` datetime DEFAULT NULL COMMENT 'Čas zadania písomky',
  `dt_closed` datetime DEFAULT NULL COMMENT 'Čas skončenia',
  `fl_closed` varchar(1) DEFAULT NULL COMMENT 'Flaga či je ešte aktívna',
  PRIMARY KEY (`id_test`),
  KEY `FK_GROUPT_idx` (`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `unit`
--

CREATE TABLE IF NOT EXISTS `unit` (
  `id_unit` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny kľúč',
  `fl_base_unit` varchar(1) DEFAULT NULL COMMENT 'Flaga, či sa jedná o základnú jednotku',
  `nb_category` int(11) DEFAULT NULL COMMENT 'Kategória, do ktorej patrí veličina',
  `str_unit_name` varchar(45) DEFAULT NULL COMMENT 'Značka jednotky',
  `nb_multiple` int(11) DEFAULT NULL COMMENT 'Násobok jednotky, nemusí sa vzťahovať k základnej jednotke!',
  `nb_level` int(11) DEFAULT NULL COMMENT 'Náročnosť jednotky.',
  `str_unit_description` varchar(45) DEFAULT NULL COMMENT 'Popis jednotky\n',
  PRIMARY KEY (`id_unit`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Sťahujem dáta pre tabuľku `unit`
--

INSERT INTO `unit` (`id_unit`, `fl_base_unit`, `nb_category`, `str_unit_name`, `nb_multiple`, `nb_level`, `str_unit_description`) VALUES
(1, 'A', 1, 'J', 0, 1, 'Práca'),
(2, 'N', 1, 'kJ', 3, 1, 'Práca'),
(3, 'N', 1, 'MJ', 6, 1, 'Práca'),
(4, 'N', 1, 'mJ', -3, 1, 'Práca'),
(5, 'N', 1, 'TJ', 9, 1, 'Práca'),
(6, 'N', 1, 'nJ', -9, 1, 'Práca');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primárny klúč entity user',
  `str_name` varchar(45) DEFAULT NULL COMMENT 'Meno usera',
  `str_mail` varchar(45) DEFAULT NULL COMMENT 'E-mail používateľa',
  `str_user_password` varchar(255) DEFAULT NULL COMMENT 'Heslo používateľa v hash forme',
  `str_pass_hash` varchar(255) DEFAULT NULL COMMENT 'Zahashovany string potrebny pri obnove hesla',
  `id_group` int(11) DEFAULT NULL COMMENT 'id_group, kam žiak patrí',
  `fl_user_type` varchar(1) DEFAULT NULL COMMENT 'označenie, či sa jedná o učiteľa/žiaka',
  `dt_registration` datetime DEFAULT NULL COMMENT 'Čas registrácie',
  `dt_login` datetime DEFAULT NULL COMMENT 'Čas posledného loginu',
  PRIMARY KEY (`id_user`),
  KEY `FK_GROUP_idx` (`id_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Sťahujem dáta pre tabuľku `user`
--

INSERT INTO `user` (`id_user`, `str_name`, `str_mail`, `str_user_password`, `str_pass_hash`, `id_group`, `fl_user_type`, `dt_registration`, `dt_login`) VALUES
(1, 'Teacher Adam', 'teacher@teacher.teacher', '$2y$10$Owf6ind6aDClPlc95/NKH.Vw.DflTz27PzpswLfsR9v04PtlMXDZa', NULL, NULL, 'T', '2014-11-25 23:45:09', '2014-11-25 23:46:09'),
(2, 'Žiak Janko', 'student@student.student', '$2y$10$nY9Up/R68ZMXFiTeOdwH6.Ge1VwqG.PNGn5sXzV9lUgtdZ/WGKSBu', NULL, NULL, 'S', '2014-11-25 23:45:42', NULL);

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `FK_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Obmedzenie pre tabuľku `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_USER_tsk` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_UNIT` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id_unit`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_TEST` FOREIGN KEY (`id_test`) REFERENCES `test` (`id_test`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Obmedzenie pre tabuľku `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `FK_GROUPT` FOREIGN KEY (`id_group`) REFERENCES `class` (`id_group`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Obmedzenie pre tabuľku `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_GROUP` FOREIGN KEY (`id_group`) REFERENCES `class` (`id_group`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
