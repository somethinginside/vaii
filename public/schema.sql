-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-5.7
-- Время создания: Дек 08 2025 г., 00:24
-- Версия сервера: 5.7.44
-- Версия PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `unicorns_world`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Order`
--

CREATE TABLE `Order` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('created','shipped','ready to сollection','done') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Order`
--

INSERT INTO `Order` (`id`, `user_id`, `date`, `total_price`, `status`) VALUES
(2, 1, '2025-12-05 20:34:00', 279.00, 'shipped');

-- --------------------------------------------------------

--
-- Структура таблицы `OrderItem`
--

CREATE TABLE `OrderItem` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `OrderItem`
--

INSERT INTO `OrderItem` (`order_id`, `product_id`, `quantity`, `subtotal`) VALUES
(2, 3, 12, 279.00);

-- --------------------------------------------------------

--
-- Структура таблицы `Product`
--

CREATE TABLE `Product` (
  `id` int(11) NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Product`
--

INSERT INTO `Product` (`id`, `name`, `category`, `price`, `description`, `image`, `stock_quantity`) VALUES
(2, 'dzvadgdfgv', 'aaaab', 13.00, 'TEST VERSION', '/images/11.jpg', 0),
(3, 'fff', 'aaaaaaaafff', 23.25, 'jgcdjyrdjsynjhvckxyse ,ngcjgxfsyhx', '/images/35.jpg', 12);

-- --------------------------------------------------------

--
-- Структура таблицы `Unicorn`
--

CREATE TABLE `Unicorn` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int(11) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `Unicorn`
--

INSERT INTO `Unicorn` (`id`, `name`, `color`, `age`, `description`, `image`, `admin_id`) VALUES
(2, 'ANNA', 'blue', 8, 'TEST', '/images/35.jpg', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `User`
--

INSERT INTO `User` (`id`, `name`, `email`, `password_hash`, `role`, `registration_date`) VALUES
(1, 'Natalia', 'nataliapolikarpova714@gmail.com', '$2y$10$XgVsPBfyqxNk4sH2saYggO/parmKr6zxsvzP8WduoG21g8W9Zu0C2', 'user', '2025-12-02 20:31:53'),
(2, 'admin', 'admin@unicorns.world', '$2y$10$tA3dOuw84rE3TeJHOSLRB.KkSfmJOLd4VbtXo5a7BUBJU5OAQnC2S', 'admin', '2025-12-02 20:45:44');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Order`
--
ALTER TABLE `Order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Индексы таблицы `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Индексы таблицы `Product`
--
ALTER TABLE `Product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `Unicorn`
--
ALTER TABLE `Unicorn`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Индексы таблицы `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id` (`id`) USING BTREE;

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Order`
--
ALTER TABLE `Order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `Product`
--
ALTER TABLE `Product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `Unicorn`
--
ALTER TABLE `Unicorn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Order`
--
ALTER TABLE `Order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `OrderItem`
--
ALTER TABLE `OrderItem`
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Unicorn`
--
ALTER TABLE `Unicorn`
  ADD CONSTRAINT `unicorn_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `User` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
