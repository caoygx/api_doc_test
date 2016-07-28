/*
Navicat MySQL Data Transfer

Source Server         : centos_desk
Source Server Version : 50709
Source Host           : localhost:3306
Source Database       : api

Target Server Type    : MYSQL
Target Server Version : 50709
File Encoding         : 65001

Date: 2016-07-28 14:06:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `rrbrr_doc`
-- ----------------------------
DROP TABLE IF EXISTS `rrbrr_doc`;
CREATE TABLE `rrbrr_doc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `param` varchar(1000) DEFAULT NULL,
  `return` text,
  `return_json` text,
  `module` varchar(50) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rrbrr_doc
-- ----------------------------
INSERT INTO `rrbrr_doc` VALUES ('1', '添加用户', 'POST', '/user/add', 'username 用户名 \r\npassword 密码', 'code 1 成功\r\nuid 用户id', '{\r\n    \"code\":1,\r\n    \"msg\":\"添加成功\",\r\n    \"data\":{\r\n        \"uid\":1\r\n    }\r\n}\r\n', '用户中心', '1469684787');

-- ----------------------------
-- Table structure for `rrbrr_test`
-- ----------------------------
DROP TABLE IF EXISTS `rrbrr_test`;
CREATE TABLE `rrbrr_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(16) NOT NULL,
  `url` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `ret_format` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `get` tinyint(1) NOT NULL COMMENT '是否是get',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  `project` varchar(20) NOT NULL DEFAULT 'api' COMMENT '项目',
  `environment` varchar(50) DEFAULT 'test',
  PRIMARY KEY (`id`),
  KEY `s_data` (`project`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rrbrr_test
-- ----------------------------
INSERT INTO `rrbrr_test` VALUES ('1', '添加用户', '/user/add', '{\r\n    \"username\":\"abc\",\r\n    \"password\":\"123456\"\r\n}\r\n', '{\r\n    \"code\":1,\r\n    \"msg\":\"添加成功\",\r\n    \"data\":{\r\n        \"uid\":1\r\n    }\r\n}\r\n', '全局', '0', '1', 'api', 'test');
