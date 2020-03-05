-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-03-05 10:09:17
-- 服务器版本： 5.7.28
-- PHP 版本： 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `zentao`
--

-- --------------------------------------------------------

--
-- 表的结构 `zt_product`
--

CREATE TABLE `zt_product` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(90) NOT NULL,
  `code` varchar(45) NOT NULL,
  `line` mediumint(8) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'normal',
  `status` varchar(30) NOT NULL DEFAULT '',
  `subStatus` varchar(30) NOT NULL,
  `desc` text NOT NULL,
  `PO` varchar(30) NOT NULL,
  `QD` varchar(30) NOT NULL,
  `RD` varchar(30) NOT NULL,
  `acl` enum('open','private','custom') NOT NULL DEFAULT 'open',
  `whitelist` text NOT NULL,
  `createdBy` varchar(30) NOT NULL,
  `createdDate` datetime NOT NULL,
  `createdVersion` varchar(20) NOT NULL,
  `order` mediumint(8) UNSIGNED NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `pri` enum('1','2','3','4') NOT NULL DEFAULT '3'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `zt_product`
--
ALTER TABLE `zt_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order` (`order`),
  ADD KEY `acl` (`acl`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `zt_product`
--
ALTER TABLE `zt_product`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
