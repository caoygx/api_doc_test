/*
Navicat MySQL Data Transfer

Source Server         : c7
Source Server Version : 50718
Source Host           : 192.168.26.118:3306
Source Database       : doc

Target Server Type    : MYSQL
Target Server Version : 50718
File Encoding         : 65001

Date: 2020-08-27 17:53:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for doc
-- ----------------------------
DROP TABLE IF EXISTS `doc`;
CREATE TABLE `doc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `param` text,
  `param_json` text,
  `return` text,
  `return_json` text,
  `module` varchar(255) DEFAULT NULL,
  `update_time` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `environment` varchar(255) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `sql` text,
  `table` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4;
