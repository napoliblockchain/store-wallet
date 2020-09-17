-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 06, 2019 alle 09:49
-- Versione del server: 10.1.31-MariaDB
-- Versione PHP: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bolt`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `bolt_contacts`
--

CREATE TABLE `bolt_contacts` (
  `id_contact` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_social` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `bolt_contacts`
--

INSERT INTO `bolt_contacts` (`id_contact`, `id_user`, `id_social`) VALUES
(3, 13, 1),
(8, 14, 4),
(9, 1, 4),
(10, 20, 20),
(12, 20, 17),
(13, 20, 16),
(14, 20, 12),
(15, 20, 13),
(16, 20, 18),
(17, 20, 21),
(18, 20, 22),
(19, 20, 23),
(20, 20, 4),
(21, 20, 5),
(22, 20, 6),
(23, 20, 7),
(25, 20, 14),
(26, 20, 8),
(29, 20, 9),
(30, 14, 9),
(31, 20, 3);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `bolt_contacts`
--
ALTER TABLE `bolt_contacts`
  ADD PRIMARY KEY (`id_contact`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `bolt_contacts`
--
ALTER TABLE `bolt_contacts`
  MODIFY `id_contact` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
