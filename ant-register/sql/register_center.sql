-- phpMyAdmin SQL Dump
-- version 4.4.15.8
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-01-11 21:05:02
-- 服务器版本： 5.6.33-log
-- PHP Version: 7.0.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `register_center`
--
CREATE DATABASE IF NOT EXISTS `register_center` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `register_center`;

-- --------------------------------------------------------

--
-- 表的结构 `service_list`
--

DROP TABLE IF EXISTS `service_list`;
CREATE TABLE IF NOT EXISTS `service_list` (
  `id` int(11) NOT NULL,
  `name` char(50) NOT NULL COMMENT '服务名称',
  `ip` char(15) NOT NULL COMMENT '服务ip',
  `port` mediumint(8) NOT NULL COMMENT '服务端口',
  `status` tinyint(1) NOT NULL COMMENT '运行状态',
  `rate` smallint(4) NOT NULL COMMENT '权重',
  `registerTime` int(10) NOT NULL COMMENT '注册时间',
  `startTime` int(10) NOT NULL COMMENT '启动时间',
  `dropTime` int(10) NOT NULL COMMENT '停止时间',
  `registerKey` varchar(100) NOT NULL COMMENT '从哪个注册服务器注册的',
  `serverType` smallint(4) NOT NULL DEFAULT '0' COMMENT '服务类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务表';

-- --------------------------------------------------------

--
-- 表的结构 `subscriber`
--

DROP TABLE IF EXISTS `subscriber`;
CREATE TABLE IF NOT EXISTS `subscriber` (
  `id` int(11) NOT NULL,
  `serviceName` varchar(100) NOT NULL COMMENT '服务名',
  `subcriber` varchar(100) NOT NULL COMMENT '订阅者'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `service_list`
--
ALTER TABLE `service_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_ip_port` (`ip`,`port`) USING BTREE,
  ADD KEY `idx_name` (`name`) USING BTREE,
  ADD KEY `registerKey` (`registerKey`);

--
-- Indexes for table `subscriber`
--
ALTER TABLE `subscriber`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serviceName` (`serviceName`),
  ADD KEY `subcriber` (`subcriber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `service_list`
--
ALTER TABLE `service_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `subscriber`
--
ALTER TABLE `subscriber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
