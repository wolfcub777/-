-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-02-21 07:26:12
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
-- 資料表結構 `帳戶註冊`
--

CREATE TABLE `帳戶註冊` (
  `學號` varchar(50) NOT NULL,
  `身分` varchar(50) NOT NULL,
  `學部` varchar(50) NOT NULL,
  `科別` varchar(100) NOT NULL,
  `學制` varchar(50) NOT NULL,
  `姓名` varchar(100) NOT NULL,
  `電話` varchar(20) NOT NULL,
  `信箱` varchar(100) NOT NULL,
  `身分證` varchar(20) NOT NULL,
  `車牌號碼` varchar(20) DEFAULT NULL,
  `圖片` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `帳戶註冊`
--

INSERT INTO `帳戶註冊` (`學號`, `身分`, `學部`, `科別`, `學制`, `姓名`, `電話`, `信箱`, `身分證`, `車牌號碼`, `圖片`) VALUES
('1', '學生', '五專', '護理科', '日間', '羅育政', '0908661215', '109534127@stu.ukn.edu.tw', 'A131729095', 'MXH-1122', 'uploads/圖片1.png');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `帳戶註冊`
--
ALTER TABLE `帳戶註冊`
  ADD PRIMARY KEY (`學號`),
  ADD UNIQUE KEY `信箱` (`信箱`),
  ADD UNIQUE KEY `身分證` (`身分證`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
