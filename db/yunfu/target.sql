-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2019-09-30 14:30:18
-- 服务器版本： 5.7.27
-- PHP 版本： 7.3.9

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
-- 表的结构 `zt_target_category`
--

CREATE TABLE `zt_target_category` (
  `id` int(11) NOT NULL COMMENT '类别ID',
  `name` varchar(20) NOT NULL COMMENT '类别名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zt_target_dataset`
--

CREATE TABLE `zt_target_dataset` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '数据集名称',
  `source` varchar(200) NOT NULL COMMENT '数据集位置',
  `size` varchar(50) NOT NULL COMMENT '数据集大小'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zt_target_experiment`
--

CREATE TABLE `zt_target_experiment` (
  `id` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `rid` varchar(200) DEFAULT NULL,
  `pid` int(11) NOT NULL COMMENT '所属项目'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zt_target_module`
--

CREATE TABLE `zt_target_module` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL COMMENT '模块类别ID',
  `name` varchar(50) NOT NULL COMMENT '模块名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zt_target_performance`
--

CREATE TABLE `zt_target_performance` (
  `id` int(11) NOT NULL,
  `precision_` varchar(20) NOT NULL COMMENT '准确率',
  `recall` varchar(20) NOT NULL COMMENT '召回率',
  `f1` varchar(20) NOT NULL COMMENT 'f1值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zt_target_record`
--

CREATE TABLE `zt_target_record` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL COMMENT '结果id',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '实验日期',
  `solution` varchar(200) NOT NULL COMMENT '方法'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zt_target_target`
--

CREATE TABLE `zt_target_target` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL COMMENT '预期指标id',
  `deadline` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '截止日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转储表的索引
--

--
-- 表的索引 `zt_target_category`
--
ALTER TABLE `zt_target_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zt_target_dataset`
--
ALTER TABLE `zt_target_dataset`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zt_target_experiment`
--
ALTER TABLE `zt_target_experiment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module` (`mid`),
  ADD KEY `dataset` (`did`),
  ADD KEY `target` (`tid`);

--
-- 表的索引 `zt_target_module`
--
ALTER TABLE `zt_target_module`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`cid`);

--
-- 表的索引 `zt_target_performance`
--
ALTER TABLE `zt_target_performance`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `zt_target_record`
--
ALTER TABLE `zt_target_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance2` (`pid`);

--
-- 表的索引 `zt_target_target`
--
ALTER TABLE `zt_target_target`
  ADD PRIMARY KEY (`id`),
  ADD KEY `performance` (`pid`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `zt_target_category`
--
ALTER TABLE `zt_target_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '类别ID';

--
-- 使用表AUTO_INCREMENT `zt_target_dataset`
--
ALTER TABLE `zt_target_dataset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zt_target_experiment`
--
ALTER TABLE `zt_target_experiment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zt_target_module`
--
ALTER TABLE `zt_target_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zt_target_performance`
--
ALTER TABLE `zt_target_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zt_target_record`
--
ALTER TABLE `zt_target_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `zt_target_target`
--
ALTER TABLE `zt_target_target`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 限制导出的表
--

--
-- 限制表 `zt_target_experiment`
--
ALTER TABLE `zt_target_experiment`
  ADD CONSTRAINT `dataset` FOREIGN KEY (`did`) REFERENCES `zt_target_dataset` (`id`),
  ADD CONSTRAINT `module` FOREIGN KEY (`mid`) REFERENCES `zt_target_module` (`id`),
  ADD CONSTRAINT `target` FOREIGN KEY (`tid`) REFERENCES `zt_target_target` (`id`);

--
-- 限制表 `zt_target_module`
--
ALTER TABLE `zt_target_module`
  ADD CONSTRAINT `category` FOREIGN KEY (`cid`) REFERENCES `zt_target_category` (`id`);

--
-- 限制表 `zt_target_record`
--
ALTER TABLE `zt_target_record`
  ADD CONSTRAINT `performance2` FOREIGN KEY (`pid`) REFERENCES `zt_target_performance` (`id`);

--
-- 限制表 `zt_target_target`
--
ALTER TABLE `zt_target_target`
  ADD CONSTRAINT `performance` FOREIGN KEY (`pid`) REFERENCES `zt_target_performance` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
