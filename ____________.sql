-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-09-18 05:32:08
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `停車場巡查使用系統`
--

-- --------------------------------------------------------

--
-- 資料表結構 `停車記錄`
--

CREATE TABLE `停車記錄` (
  `停車記錄ID` int(11) NOT NULL,
  `車牌號碼` varchar(20) NOT NULL,
  `使用者ID` int(11) NOT NULL,
  `停車日期時間` datetime NOT NULL,
  `停車結束時間` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `停車記錄`
--

INSERT INTO `停車記錄` (`停車記錄ID`, `車牌號碼`, `使用者ID`, `停車日期時間`, `停車結束時間`) VALUES
(1, 'ABC-1234', 1, '2024-09-01 08:00:00', '2024-09-01 18:00:00'),
(2, 'DEF-5678', 2, '2024-09-01 09:00:00', '2024-09-01 12:00:00'),
(3, 'GHI-9876', 3, '2024-09-02 07:30:00', '2024-09-02 17:30:00');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `停車記錄`
--
ALTER TABLE `停車記錄`
  ADD PRIMARY KEY (`停車記錄ID`),
  ADD KEY `使用者ID` (`使用者ID`),
  ADD KEY `車牌號碼` (`車牌號碼`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `停車記錄`
--
ALTER TABLE `停車記錄`
  MODIFY `停車記錄ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `停車記錄`
--
ALTER TABLE `停車記錄`
  ADD CONSTRAINT `停車記錄_ibfk_1` FOREIGN KEY (`使用者ID`) REFERENCES `使用者` (`使用者ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `停車記錄_ibfk_2` FOREIGN KEY (`車牌號碼`) REFERENCES `使用者` (`車牌號碼`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
