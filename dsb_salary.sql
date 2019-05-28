# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 101.200.220.244 (MySQL 5.6.24)
# Database: dsb_salary
# Generation Time: 2019-05-28 09:15:51 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table dsb_company
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dsb_company`;

CREATE TABLE `dsb_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `company_name` varchar(255) DEFAULT NULL COMMENT '公司名称',
  `address` varchar(255) DEFAULT NULL COMMENT '公司地址',
  `contact` varchar(255) DEFAULT NULL COMMENT '联系人',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `license` varchar(255) DEFAULT NULL COMMENT '营业执照',
  `status` tinyint(4) unsigned DEFAULT '1' COMMENT '状态：1.待审核 2.审核通过',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dsb_detail
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dsb_detail`;

CREATE TABLE `dsb_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `detail_name` varchar(255) DEFAULT NULL COMMENT '详情名称',
  PRIMARY KEY (`id`),
  KEY `idx_detail_name` (`detail_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dsb_employee
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dsb_employee`;

CREATE TABLE `dsb_employee` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '公司ID',
  `real_name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `idcard` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `status` tinyint(4) unsigned DEFAULT '1' COMMENT '员工状态: 0.离职 1.在职 ',
  PRIMARY KEY (`id`),
  KEY `idx_ci` (`company_id`,`idcard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dsb_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dsb_history`;

CREATE TABLE `dsb_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `company_id` int(11) unsigned DEFAULT NULL COMMENT '公司ID',
  `month` int(11) unsigned DEFAULT NULL COMMENT '月份',
  `batch` varchar(255) DEFAULT NULL COMMENT '批次',
  `num` int(11) unsigned DEFAULT NULL COMMENT '员工总数',
  `salary` decimal(14,2) unsigned DEFAULT NULL COMMENT '实发总计',
  `tax` decimal(14,2) unsigned DEFAULT NULL COMMENT '个税总计',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `type` tinyint(4) unsigned DEFAULT NULL COMMENT '发送方式: 1.短信 2.邮件 3.短信+邮件',
  `content` varchar(255) DEFAULT NULL COMMENT '内容',
  `status` tinyint(4) unsigned DEFAULT NULL COMMENT '状态: 1.显示 2.隐藏',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dsb_salary
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dsb_salary`;

CREATE TABLE `dsb_salary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `company_id` int(11) unsigned DEFAULT NULL COMMENT '公司ID',
  `employee_id` int(11) unsigned DEFAULT NULL COMMENT '员工ID',
  `month` int(11) unsigned DEFAULT NULL COMMENT '月份',
  `batch` int(11) unsigned DEFAULT NULL COMMENT '批次',
  `did` int(11) unsigned DEFAULT NULL COMMENT '明细ID',
  `cid` int(11) unsigned DEFAULT NULL COMMENT '分类ID',
  `value` varchar(255) DEFAULT '' COMMENT '值',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` tinyint(4) unsigned DEFAULT NULL COMMENT '状态: 1.显示 2.隐藏',
  PRIMARY KEY (`id`),
  KEY `idx_cemb` (`company_id`,`employee_id`,`month`,`batch`),
  KEY `idx_edc` (`employee_id`,`did`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dsb_sms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dsb_sms`;

CREATE TABLE `dsb_sms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `code` varchar(255) DEFAULT NULL COMMENT '验证码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dsb_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dsb_user`;

CREATE TABLE `dsb_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `company_id` int(11) unsigned DEFAULT NULL COMMENT '公司ID',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `status` tinyint(4) unsigned DEFAULT NULL COMMENT '状态: 1.启用 2.禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
