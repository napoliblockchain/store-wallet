-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 03, 2019 alle 09:29
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
-- Struttura della tabella `bolt_tokens_memo`
--

CREATE TABLE `bolt_tokens_memo` (
  `id_tmemo` int(11) NOT NULL,
  `id_token` int(11) NOT NULL,
  `memo` varchar(550) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `bolt_tokens_memo`
--
ALTER TABLE `bolt_tokens_memo`
  ADD PRIMARY KEY (`id_tmemo`),
  ADD KEY `id_tokens` (`id_token`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `bolt_tokens_memo`
--
ALTER TABLE `bolt_tokens_memo`
  MODIFY `id_tmemo` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
