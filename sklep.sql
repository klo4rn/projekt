-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 15 Lis 2024, 13:10
-- Wersja serwera: 10.4.17-MariaDB
-- Wersja PHP: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `sklep`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'whisky'),
(2, 'burbon'),
(3, 'gin');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `konto_uzytkownika`
--

CREATE TABLE `konto_uzytkownika` (
  `id` int(11) NOT NULL,
  `login` varchar(50) DEFAULT NULL,
  `haslo` varchar(255) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `adres` varchar(50) DEFAULT NULL,
  `data_urodzenia` date DEFAULT NULL,
  `id_pytania_pomocniczego` int(11) DEFAULT NULL,
  `odpowiedz` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `konto_uzytkownika`
--

INSERT INTO `konto_uzytkownika` (`id`, `login`, `haslo`, `email`, `adres`, `data_urodzenia`, `id_pytania_pomocniczego`, `odpowiedz`) VALUES
(1, 'admin', '$2y$10$zDrCGDRaKMch.jOq8XCb2exFLbKO8B60VOjzH88YCHBh6vkmBM14W', 'admin12@admin.pl', 'admin', '2024-10-31', 2, 'admin');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('Nowe','W realizacji','Zakończone') DEFAULT 'Nowe',
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_items`
--

CREATE TABLE `order_items` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_payment`
--

CREATE TABLE `order_payment` (
  `order_id` int(11) NOT NULL,
  `payment_method_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_shipping`
--

CREATE TABLE `order_shipping` (
  `order_id` int(11) NOT NULL,
  `shipping_method_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `parameters`
--

CREATE TABLE `parameters` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `parameters`
--

INSERT INTO `parameters` (`id`, `name`) VALUES
(1, 'smak'),
(2, 'kolor'),
(3, 'pochodzenie');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`) VALUES
(1, 'Przelew bankowy'),
(2, 'Karta kredytowa'),
(3, 'Płatność przy odbiorze');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `ilosc` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `products`
--

INSERT INTO `products` (`id`, `name`, `cena`, `ilosc`, `image`) VALUES
(14, 'Jack Daniels', '58.00', 52, 'uploads/img_6737285d62290.jpg'),
(15, 'Jeam Beam', '78.00', 52, 'uploads/img_6737287b16f3f.jpg'),
(16, 'Hendrick\'s', '98.00', 52, 'uploads/img_67372897eccd1.jpg'),
(17, 'Johnnie Walker Blue Label Ice Chalet / 43% / 0,7l', '1500.00', 2, 'uploads/img_67372b32e2dde.jpg'),
(18, 'The Gardener Gin by Brad Pitt / 42% / 0,7l', '500.00', 45, 'uploads/img_67372b8ebf77a.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_categories`
--

CREATE TABLE `product_categories` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `product_categories`
--

INSERT INTO `product_categories` (`product_id`, `category_id`) VALUES
(14, 1),
(15, 2),
(16, 3),
(17, 1),
(18, 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `product_parameters`
--

CREATE TABLE `product_parameters` (
  `product_id` int(11) NOT NULL,
  `parameter_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `product_parameters`
--

INSERT INTO `product_parameters` (`product_id`, `parameter_id`, `value`) VALUES
(14, 1, 'wytrawny'),
(14, 2, 'brązowy'),
(14, 3, 'Ameryka '),
(15, 1, 'słodki'),
(15, 2, 'jaskrawy żółty'),
(15, 3, 'Ameryka'),
(16, 1, 'mocny'),
(16, 2, 'przezroczysty'),
(16, 3, 'Wielka Brytania'),
(17, 1, 'wytrawny'),
(17, 2, 'jaskrawy'),
(17, 3, 'Szokcja'),
(18, 1, 'słodki'),
(18, 2, 'przezroczysty'),
(18, 3, 'Anglia');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `name`, `cost`) VALUES
(1, 'Kurier', '15.00'),
(2, 'Paczkomat', '10.00'),
(3, 'Odbiór osobisty', '0.00');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `konto_uzytkownika`
--
ALTER TABLE `konto_uzytkownika`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `order_payment`
--
ALTER TABLE `order_payment`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `payment_method_id` (`payment_method_id`);

--
-- Indeksy dla tabeli `order_shipping`
--
ALTER TABLE `order_shipping`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `shipping_method_id` (`shipping_method_id`);

--
-- Indeksy dla tabeli `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `parameters`
--
ALTER TABLE `parameters`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indeksy dla tabeli `product_parameters`
--
ALTER TABLE `product_parameters`
  ADD PRIMARY KEY (`product_id`,`parameter_id`),
  ADD KEY `parameter_id` (`parameter_id`);

--
-- Indeksy dla tabeli `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT dla tabeli `konto_uzytkownika`
--
ALTER TABLE `konto_uzytkownika`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT dla tabeli `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT dla tabeli `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `parameters`
--
ALTER TABLE `parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT dla tabeli `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT dla tabeli `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `konto_uzytkownika` (`id`) ON DELETE SET NULL;

--
-- Ograniczenia dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `order_payment`
--
ALTER TABLE `order_payment`
  ADD CONSTRAINT `order_payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_payment_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL;

--
-- Ograniczenia dla tabeli `order_shipping`
--
ALTER TABLE `order_shipping`
  ADD CONSTRAINT `order_shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_shipping_ibfk_2` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`id`) ON DELETE SET NULL;

--
-- Ograniczenia dla tabeli `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `product_parameters`
--
ALTER TABLE `product_parameters`
  ADD CONSTRAINT `product_parameters_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_parameters_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
