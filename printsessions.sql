-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 29 2017 г., 16:18
-- Версия сервера: 10.1.21-MariaDB
-- Версия PHP: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `plotters`
--

-- --------------------------------------------------------

--
-- Структура таблицы `printsessions`
--

CREATE TABLE `printsessions` (
  `id` int(11) UNSIGNED NOT NULL,
  `session_id` int(11) UNSIGNED NOT NULL,
  `plotter` tinyint(1) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `stop_datetime` datetime NOT NULL,
  `passes` int(11) UNSIGNED NOT NULL,
  `meters` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `printsessions`
--

INSERT INTO `printsessions` (`id`, `session_id`, `plotter`, `start_datetime`, `stop_datetime`, `passes`, `meters`) VALUES
(1, 1, 1, '2014-10-30 14:54:50', '2014-10-30 14:54:55', 20, 1),
(2, 4, 2, '2014-12-12 14:45:00', '2015-12-12 14:50:00', 100, 1),
(3, 5, 3, '2014-12-12 14:45:00', '2015-12-12 14:50:00', 100, 1),
(4, 6, 5, '2014-12-12 15:15:00', '2015-12-12 15:50:00', 250, 3);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `printsessions`
--
ALTER TABLE `printsessions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `printsessions`
--
ALTER TABLE `printsessions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
