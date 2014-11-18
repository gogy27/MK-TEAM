-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Út 18.Nov 2014, 20:12
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
-- Štruktúra tabuľky pre tabuľku `user`
--

DROP TABLE IF EXISTS `user`;
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Sťahujem dáta pre tabuľku `user`
--

INSERT INTO `user` (`id_user`, `str_name`, `str_mail`, `str_user_password`, `str_pass_hash`, `id_group`, `fl_user_type`, `dt_registration`, `dt_login`) VALUES
(3, 'user', NULL, '$2y$10$3N14EpmbIvO9svZ8YEv26eON9zPH6fQtHRVgAQubJoPVHzZ7VfcRC', NULL, NULL, NULL, NULL, NULL);

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_GROUP` FOREIGN KEY (`id_group`) REFERENCES `group` (`id_group`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
