-- phpMyAdmin SQL Dump
-- version 4.4.15.8
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-11-14 15:48:52
-- 服务器版本： 5.6.33-log
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `register_center`
--

-- --------------------------------------------------------

--
-- 表的结构 `service_list`
--

CREATE TABLE IF NOT EXISTS `service_list` (
  `id` int(11) NOT NULL,
  `name` char(50) NOT NULL COMMENT '服务名称',
  `ip` char(15) NOT NULL COMMENT '服务ip',
  `port` mediumint(8) NOT NULL COMMENT '服务端口',
  `status` tinyint(1) NOT NULL COMMENT '运行状态',
  `rate` smallint(4) NOT NULL COMMENT '权重',
  `startTime` int(10) NOT NULL COMMENT '启动时间'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='服务表';

--
-- 转存表中的数据 `service_list`
--

INSERT INTO `service_list` (`id`, `name`, `ip`, `port`, `status`, `rate`, `startTime`) VALUES
(1, 'dproxy', '10.94.107.22', 9939, 0, 0, 1477984417);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `service_list`
--
ALTER TABLE `service_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_ip_port` (`ip`,`port`) USING BTREE,
  ADD KEY `idx_name` (`name`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `service_list`
--
ALTER TABLE `service_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
