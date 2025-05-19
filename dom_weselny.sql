-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 19, 2025 at 11:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dom_weselny`
--

-- --------------------------------------------------------

--
-- Table structure for table `finanse`
--

CREATE TABLE `finanse` (
  `transakcja_id` int(11) NOT NULL,
  `rezerwacja_id` int(11) NOT NULL,
  `kwota` decimal(10,2) NOT NULL,
  `data_transakcji` datetime NOT NULL DEFAULT current_timestamp(),
  `typ_platnosci` enum('gotowka','karta','przelew') NOT NULL,
  `status_platnosci` enum('zaliczka','pelna','zwrot') NOT NULL,
  `uwagi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `finanse`
--

INSERT INTO `finanse` (`transakcja_id`, `rezerwacja_id`, `kwota`, `data_transakcji`, `typ_platnosci`, `status_platnosci`, `uwagi`) VALUES
(1, 1, 2000.00, '2023-05-12 10:00:00', 'przelew', 'zaliczka', 'Zaliczka za rezerwacje sali Biala'),
(2, 1, 3000.00, '2023-08-01 15:30:00', 'karta', 'pelna', 'Doplata za wesele'),
(3, 2, 4000.00, '2023-06-20 12:15:00', 'przelew', 'zaliczka', 'Zaliczka za rezerwacje sali Zlota'),
(4, 4, 3000.00, '2023-07-25 09:45:00', 'gotowka', 'zaliczka', 'Zaliczka za rezerwacje sali Szafir');

-- --------------------------------------------------------

--
-- Table structure for table `opinie`
--

CREATE TABLE `opinie` (
  `opinia_id` int(11) NOT NULL,
  `uzytkownik_id` int(11) NOT NULL,
  `rezerwacja_id` int(11) NOT NULL,
  `ocena` int(11) NOT NULL,
  `tresc` text DEFAULT NULL,
  `data_dodania` datetime DEFAULT current_timestamp(),
  `zatwierdzona` tinyint(1) DEFAULT 0
) ;

--
-- Dumping data for table `opinie`
--

INSERT INTO `opinie` (`opinia_id`, `uzytkownik_id`, `rezerwacja_id`, `ocena`, `tresc`, `data_dodania`, `zatwierdzona`) VALUES
(1, 6, 1, 5, 'Wspaniale wesele, doskonala obsluga i piekna sala! Polecam!', '2023-08-20 12:30:00', 1),
(2, 7, 2, 4, 'Bardzo dobra organizacja, tylko jedzenie moglo byc lepsze.', '2023-09-25 14:15:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rezerwacje`
--

CREATE TABLE `rezerwacje` (
  `rezerwacja_id` int(11) NOT NULL,
  `uzytkownik_id` int(11) NOT NULL,
  `sala_id` int(11) NOT NULL,
  `data_rezerwacji` datetime NOT NULL DEFAULT current_timestamp(),
  `data_wydarzenia` date NOT NULL,
  `godzina_rozpoczecia` time NOT NULL,
  `godzina_zakonczenia` time NOT NULL,
  `liczba_gosci` int(11) NOT NULL,
  `status` enum('potwierdzona','anulowana','zrealizowana','oczekujaca') DEFAULT 'oczekujaca',
  `dodatkowe_informacje` text DEFAULT NULL,
  `pracownik_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `rezerwacje`
--

INSERT INTO `rezerwacje` (`rezerwacja_id`, `uzytkownik_id`, `sala_id`, `data_rezerwacji`, `data_wydarzenia`, `godzina_rozpoczecia`, `godzina_zakonczenia`, `liczba_gosci`, `status`, `dodatkowe_informacje`, `pracownik_id`) VALUES
(1, 6, 1, '2023-05-10 14:30:00', '2023-08-15', '16:00:00', '02:00:00', 100, 'zrealizowana', 'Wesele z obsluga kelnerska', 3),
(2, 7, 2, '2023-06-15 10:15:00', '2023-09-20', '17:00:00', '03:00:00', 180, 'potwierdzona', 'Prosimy o ustawienie stolu glownego na srodku', 3),
(3, 6, 3, '2023-07-01 09:00:00', '2023-10-05', '14:00:00', '22:00:00', 70, 'oczekujaca', 'Urodziny - prosimy o przygotowanie miejsca na tort', NULL),
(4, 7, 4, '2023-07-20 16:45:00', '2023-11-11', '18:00:00', '04:00:00', 120, 'potwierdzona', 'Wesele z pokazem fajerwerkow', 2);

-- --------------------------------------------------------

--
-- Table structure for table `sale`
--

CREATE TABLE `sale` (
  `sala_id` int(11) NOT NULL,
  `nazwa_sali` varchar(50) NOT NULL,
  `pojemnosc` int(11) NOT NULL,
  `powierzchnia` decimal(6,2) NOT NULL,
  `cena_podstawowa` decimal(10,2) NOT NULL,
  `opis` text DEFAULT NULL,
  `wyposazenie` text DEFAULT NULL,
  `dostepnosc_weekend` tinyint(1) DEFAULT 1,
  `zdjecie` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`sala_id`, `nazwa_sali`, `pojemnosc`, `powierzchnia`, `cena_podstawowa`, `opis`, `wyposazenie`, `dostepnosc_weekend`, `zdjecie`) VALUES
(1, 'Biala', 120, 150.50, 5000.00, 'Elegancka sala w bialych tonacjach', 'stoly, krzesla, scena, naglosnienie', 1, 'biala.jpg'),
(2, 'Zlota', 200, 220.75, 8000.00, 'Luksusowa sala w zlotych odcieniach', 'stoly, krzesla, scena, naglosnienie, oswietlenie LED', 1, 'zlota.jpg'),
(3, 'Rubin', 80, 100.00, 3500.00, 'Kameralna sala w czerwieni', 'stoly, krzesla, naglosnienie', 0, 'rubin.jpg'),
(4, 'Szafir', 150, 180.25, 6000.00, 'Nowoczesna sala w niebieskich barwach', 'stoly, krzesla, scena, naglosnienie, oswietlenie LED', 1, 'szafir.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `uzytkownik_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `imie` varchar(30) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `telefon` varchar(15) DEFAULT NULL,
  `adres` text DEFAULT NULL,
  `rola` enum('klient','admin','manager','kelner','sprzataczka','kucharz') NOT NULL,
  `data_rejestracji` datetime DEFAULT current_timestamp(),
  `aktywny` tinyint(1) DEFAULT 1,
  `wynagrodzenie` decimal(8,2) DEFAULT NULL,
  `stanowisko` varchar(50) DEFAULT NULL,
  `data_zatrudnienia` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`uzytkownik_id`, `email`, `haslo`, `imie`, `nazwisko`, `telefon`, `adres`, `rola`, `data_rejestracji`, `aktywny`, `wynagrodzenie`, `stanowisko`, `data_zatrudnienia`) VALUES
(1, 'admin@domweselny.pl', '$2y$10$HhidzQo5vA5H5jQ7GJBLX.3U0jY5QZ9XkQZ9XkQZ9XkQZ9XkQZ9Xk', 'Jan', 'Kowalski', '123456789', 'ul. Admina 1, 00-001 Warszawa', 'admin', '2025-05-19 23:37:40', 1, 8000.00, 'Administrator', '2020-01-15'),
(2, 'manager@domweselny.pl', '$2y$10$HhidzQo5vA5H5jQ7GJBLX.3U0jY5QZ9XkQZ9XkQZ9XkQZ9XkQZ9Xk', 'Anna', 'Nowak', '987654321', 'ul. Managerska 2, 00-002 Warszawa', 'manager', '2025-05-19 23:37:40', 1, 6500.00, 'Manager', '2021-03-10'),
(3, 'kelner1@domweselny.pl', '$2y$10$HhidzQo5vA5H5jQ7GJBLX.3U0jY5QZ9XkQZ9XkQZ9XkQZ9XkQZ9Xk', 'Piotr', 'Wisniewski', '555444333', 'ul. Kelnerska 3, 00-003 Warszawa', 'kelner', '2025-05-19 23:37:40', 1, 3500.00, 'Kelner', '2022-05-20'),
(4, 'kucharz@domweselny.pl', '$2y$10$HhidzQo5vA5H5jQ7GJBLX.3U0jY5QZ9XkQZ9XkQZ9XkQZ9XkQZ9Xk', 'Marek', 'Kwiatkowski', '111222333', 'ul. Kucharska 4, 00-004 Warszawa', 'kucharz', '2025-05-19 23:37:40', 1, 5000.00, 'Szef kuchni', '2021-11-15'),
(5, 'sprzataczka@domweselny.pl', '$2y$10$HhidzQo5vA5H5jQ7GJBLX.3U0jY5QZ9XkQZ9XkQZ9XkQZ9XkQZ9Xk', 'Katarzyna', 'Lewandowska', '333222111', 'ul. Sprzatacza 5, 00-005 Warszawa', 'sprzataczka', '2025-05-19 23:37:40', 1, 3000.00, 'Pracownik sprzatajacy', '2023-01-10'),
(6, 'klient1@gmail.com', '$2y$10$HhidzQo5vA5H5jQ7GJBLX.3U0jY5QZ9XkQZ9XkQZ9XkQZ9XkQZ9Xk', 'Michal', 'Zielinski', '777888999', 'ul. Klienta 6, 00-006 Warszawa', 'klient', '2025-05-19 23:37:40', 1, NULL, NULL, NULL),
(7, 'klient2@wp.pl', '$2y$10$HhidzQo5vA5H5jQ7GJBLX.3U0jY5QZ9XkQZ9XkQZ9XkQZ9XkQZ9Xk', 'Alicja', 'Wojcik', '444555666', 'ul. Weselna 7, 00-007 Warszawa', 'klient', '2025-05-19 23:37:40', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zadania`
--

CREATE TABLE `zadania` (
  `zadanie_id` int(11) NOT NULL,
  `tytul` varchar(100) NOT NULL,
  `opis` text DEFAULT NULL,
  `przydzielony_uzytkownik_id` int(11) NOT NULL,
  `data_rozpoczecia` date NOT NULL,
  `data_zakonczenia` date DEFAULT NULL,
  `status` enum('nowe','w trakcie','zakonczone','anulowane') DEFAULT 'nowe',
  `priorytet` enum('niski','sredni','wysoki') DEFAULT 'sredni'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `zadania`
--

INSERT INTO `zadania` (`zadanie_id`, `tytul`, `opis`, `przydzielony_uzytkownik_id`, `data_rozpoczecia`, `data_zakonczenia`, `status`, `priorytet`) VALUES
(1, 'Przygotowanie sali Biala', 'Ustawienie stolow i krzeseł na wesele 15.08', 5, '2023-08-14', '2023-08-14', 'zakonczone', 'wysoki'),
(2, 'Zamowienie kwiatow', 'Zamowic kwiaty na stol glowny na 20.09', 2, '2023-09-10', '2023-09-15', 'zakonczone', 'sredni'),
(3, 'Sprzatanie po imprezie', 'Sprzatanie sali Zlota po weselu', 5, '2023-09-21', NULL, 'nowe', 'wysoki'),
(4, 'Kontrola naglosnienia', 'Sprawdzic system naglosnienia w sali Szafir', 3, '2023-11-01', NULL, 'w trakcie', 'sredni');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `finanse`
--
ALTER TABLE `finanse`
  ADD PRIMARY KEY (`transakcja_id`),
  ADD KEY `rezerwacja_id` (`rezerwacja_id`);

--
-- Indexes for table `opinie`
--
ALTER TABLE `opinie`
  ADD PRIMARY KEY (`opinia_id`),
  ADD KEY `uzytkownik_id` (`uzytkownik_id`),
  ADD KEY `rezerwacja_id` (`rezerwacja_id`);

--
-- Indexes for table `rezerwacje`
--
ALTER TABLE `rezerwacje`
  ADD PRIMARY KEY (`rezerwacja_id`),
  ADD KEY `pracownik_id` (`pracownik_id`),
  ADD KEY `idx_data_wydarzenia` (`data_wydarzenia`),
  ADD KEY `idx_uzytkownik` (`uzytkownik_id`),
  ADD KEY `idx_sala` (`sala_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`sala_id`);

--
-- Indexes for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`uzytkownik_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_rola` (`rola`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `zadania`
--
ALTER TABLE `zadania`
  ADD PRIMARY KEY (`zadanie_id`),
  ADD KEY `przydzielony_uzytkownik_id` (`przydzielony_uzytkownik_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `finanse`
--
ALTER TABLE `finanse`
  MODIFY `transakcja_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `opinie`
--
ALTER TABLE `opinie`
  MODIFY `opinia_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rezerwacje`
--
ALTER TABLE `rezerwacje`
  MODIFY `rezerwacja_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sale`
--
ALTER TABLE `sale`
  MODIFY `sala_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `uzytkownik_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `zadania`
--
ALTER TABLE `zadania`
  MODIFY `zadanie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `finanse`
--
ALTER TABLE `finanse`
  ADD CONSTRAINT `finanse_ibfk_1` FOREIGN KEY (`rezerwacja_id`) REFERENCES `rezerwacje` (`rezerwacja_id`) ON DELETE CASCADE;

--
-- Constraints for table `opinie`
--
ALTER TABLE `opinie`
  ADD CONSTRAINT `opinie_ibfk_1` FOREIGN KEY (`uzytkownik_id`) REFERENCES `uzytkownicy` (`uzytkownik_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opinie_ibfk_2` FOREIGN KEY (`rezerwacja_id`) REFERENCES `rezerwacje` (`rezerwacja_id`) ON DELETE CASCADE;

--
-- Constraints for table `rezerwacje`
--
ALTER TABLE `rezerwacje`
  ADD CONSTRAINT `rezerwacje_ibfk_1` FOREIGN KEY (`uzytkownik_id`) REFERENCES `uzytkownicy` (`uzytkownik_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rezerwacje_ibfk_2` FOREIGN KEY (`sala_id`) REFERENCES `sale` (`sala_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rezerwacje_ibfk_3` FOREIGN KEY (`pracownik_id`) REFERENCES `uzytkownicy` (`uzytkownik_id`) ON DELETE SET NULL;

--
-- Constraints for table `zadania`
--
ALTER TABLE `zadania`
  ADD CONSTRAINT `zadania_ibfk_1` FOREIGN KEY (`przydzielony_uzytkownik_id`) REFERENCES `uzytkownicy` (`uzytkownik_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
