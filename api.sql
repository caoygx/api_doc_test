/*
Navicat MySQL Data Transfer

Source Server         : centos_desk
Source Server Version : 50709
Source Host           : localhost:3306
Source Database       : api

Target Server Type    : MYSQL
Target Server Version : 50709
File Encoding         : 65001

Date: 2016-07-28 16:23:24
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
  `param_json` varchar(1000) DEFAULT NULL,
  `return` text,
  `return_json` text,
  `module` varchar(50) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rrbrr_doc
-- ----------------------------
INSERT INTO `rrbrr_doc` VALUES ('1', '添加用户', 'POST', '/user/add', 'username 用户名 \r\npassword 密码', '{\r\n    \"username\":\"abc\",\r\n    \"password\":\"123456\"\r\n}\r\n', 'code 1 成功\r\nuid 用户id', '{\r\n    \"code\":1,\r\n    \"msg\":\"添加成功\",\r\n    \"data\":{\r\n        \"uid\":1\r\n    }\r\n}\r\n', '用户中心', '1469684787');
