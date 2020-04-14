-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-04-14 12:58:59
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
-- 表的结构 `zt_productweekly`
--

CREATE TABLE `zt_productweekly` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '项目周报名称',
  `content` text NOT NULL COMMENT '项目周报内容',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '项目周报日期',
  `product` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zt_userlog`
--

CREATE TABLE `zt_userlog` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日期',
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '状态（草稿、已提交），周报默认1',
  `type` varchar(10) NOT NULL COMMENT '类型（日报、周报）',
  `account` varchar(11) NOT NULL COMMENT '用户'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `zt_productweekly`
--
ALTER TABLE `zt_productweekly`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zt_userlog`
--
ALTER TABLE `zt_userlog`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `zt_productweekly`
--
ALTER TABLE `zt_productweekly`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zt_userlog`
--
ALTER TABLE `zt_userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
