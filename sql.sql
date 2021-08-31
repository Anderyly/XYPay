/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : cs

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 31/08/2021 14:06:17
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config`  (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `k` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `v` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE,
  INDEX `k`(`k`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of config
-- ----------------------------
INSERT INTO `config` VALUES (1, 'title', '星云免签支付');
INSERT INTO `config` VALUES (2, 'smtitle', '专业支付技术服务商 - 让支付简单、专业、快捷！');
INSERT INTO `config` VALUES (3, 'keywords', '星云免签支付, 支付平台, 免费签约, 扫码支付');
INSERT INTO `config` VALUES (4, 'description', '星云免签支付作为支付宝系统服务商ISV和微信支付服务商，专业为个人可用的微信支付宝支付接口，支持H5支付');
INSERT INTO `config` VALUES (5, 'footer', 'Copyright © 2020 星云免签支付 All rights reserved. 版权所有');
INSERT INTO `config` VALUES (7, 'template', 'default');
INSERT INTO `config` VALUES (8, 'qq', '1807681025');
INSERT INTO `config` VALUES (9, 'defaultMoney', '0');
INSERT INTO `config` VALUES (10, 'geetId', '');
INSERT INTO `config` VALUES (11, 'geetKey', '');

-- ----------------------------
-- Table structure for pay_config
-- ----------------------------
DROP TABLE IF EXISTS `pay_config`;
CREATE TABLE `pay_config`  (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `k` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `v` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`Id`) USING BTREE,
  INDEX `k`(`k`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_config
-- ----------------------------
INSERT INTO `pay_config` VALUES (1, 'title', '星云免签支付');
INSERT INTO `pay_config` VALUES (2, 'smtitle', '专业支付技术服务商 - 让支付简单、专业、快捷！');
INSERT INTO `pay_config` VALUES (3, 'keywords', '星云免签支付, 支付平台, 免费签约, 扫码支付');
INSERT INTO `pay_config` VALUES (4, 'description', '星云免签支付作为支付宝系统服务商ISV和微信支付服务商，专业为个人可用的微信支付宝支付接口，支持H5支付');
INSERT INTO `pay_config` VALUES (5, 'footer', 'Copyright © 2020 星云免签支付 All rights reserved. 版权所有');
INSERT INTO `pay_config` VALUES (7, 'template', 'default');
INSERT INTO `pay_config` VALUES (8, 'qq', '1807681025');
INSERT INTO `pay_config` VALUES (9, 'defaultMoney', '0');
INSERT INTO `pay_config` VALUES (10, 'geetId', '');
INSERT INTO `pay_config` VALUES (11, 'geetKey', '');

-- ----------------------------
-- Table structure for pay_meal
-- ----------------------------
DROP TABLE IF EXISTS `pay_meal`;
CREATE TABLE `pay_meal`  (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `price` decimal(10, 2) NULL DEFAULT 0.00,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `sxf` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手续费',
  PRIMARY KEY (`mid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '套餐' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_meal
-- ----------------------------
INSERT INTO `pay_meal` VALUES (1, '免费版', 0.00, NULL, '2.5');
INSERT INTO `pay_meal` VALUES (2, '体验版', 500.00, NULL, '1.9');
INSERT INTO `pay_meal` VALUES (3, '基础版', 800.00, NULL, '1.5');
INSERT INTO `pay_meal` VALUES (4, '标准版', 1400.00, NULL, '1.1');
INSERT INTO `pay_meal` VALUES (5, '高级版', 2600.00, NULL, '0.7');
INSERT INTO `pay_meal` VALUES (6, '豪华版', 5000.00, NULL, '0.1');

-- ----------------------------
-- Table structure for pay_meal_log
-- ----------------------------
DROP TABLE IF EXISTS `pay_meal_log`;
CREATE TABLE `pay_meal_log`  (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `mid` int(11) NOT NULL DEFAULT 0,
  `createTime` int(10) NOT NULL DEFAULT 0,
  `ymd` int(8) NOT NULL DEFAULT 0,
  `ym` int(6) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `mid`(`mid`) USING BTREE,
  INDEX `ymd`(`ymd`) USING BTREE,
  INDEX `ym`(`ym`) USING BTREE,
  INDEX `status`(`status`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '套餐购买记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_meal_log
-- ----------------------------

-- ----------------------------
-- Table structure for pay_order
-- ----------------------------
DROP TABLE IF EXISTS `pay_order`;
CREATE TABLE `pay_order`  (
  `oid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '支付类型 1支付宝 2微信',
  `trade_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '云端订单号',
  `out_trade_no` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '外来商户号',
  `qrCode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '付款二维码',
  `price` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `reallyPrice` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '真实金额',
  `commission` decimal(10, 2) NOT NULL COMMENT '手续费',
  `param` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '附加参数',
  `notifyUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '异步地址',
  `returnUrl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '同步地址',
  `createTime` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `closeTime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '关闭时间',
  `payTime` int(10) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 -1过期 0待支付 1完成 2通知失败 3待通知',
  `is_auto` tinyint(1) NOT NULL DEFAULT 0,
  `ymd` int(8) NOT NULL DEFAULT 0,
  `display` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`oid`, `display`, `uid`, `type`, `trade_no`, `out_trade_no`) USING BTREE,
  INDEX `out_trade_no`(`trade_no`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `type`(`type`) USING BTREE,
  INDEX `payId`(`out_trade_no`) USING BTREE,
  INDEX `price`(`price`) USING BTREE,
  INDEX `reallyPrice`(`reallyPrice`) USING BTREE,
  INDEX `status`(`status`) USING BTREE,
  INDEX `ymd`(`ymd`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_order
-- ----------------------------

-- ----------------------------
-- Table structure for pay_qrcode
-- ----------------------------
DROP TABLE IF EXISTS `pay_qrcode`;
CREATE TABLE `pay_qrcode`  (
  `qid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT 0,
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型 1支付宝 2微信',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `totalAmount` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `createTime` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`qid`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `type`(`type`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '二维码' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_qrcode
-- ----------------------------

-- ----------------------------
-- Table structure for pay_recharge
-- ----------------------------
DROP TABLE IF EXISTS `pay_recharge`;
CREATE TABLE `pay_recharge`  (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NULL DEFAULT NULL,
  `trade_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `out_trade_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `price` decimal(10, 2) NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 1支付 0未支付',
  `createTime` int(10) NOT NULL DEFAULT 0,
  `payTime` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`rid`) USING BTREE,
  INDEX `uid`(`uid`) USING BTREE,
  INDEX `trade_no`(`trade_no`) USING BTREE,
  INDEX `out_trade_no`(`out_trade_no`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户充值记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_recharge
-- ----------------------------

-- ----------------------------
-- Table structure for pay_tmp_price
-- ----------------------------
DROP TABLE IF EXISTS `pay_tmp_price`;
CREATE TABLE `pay_tmp_price`  (
  `uid` int(11) NOT NULL DEFAULT 0,
  `price` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.00',
  `out_trade_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `createTime` int(10) NOT NULL,
  `closeTime` int(10) NOT NULL,
  PRIMARY KEY (`price`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '临时金额' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_tmp_price
-- ----------------------------

-- ----------------------------
-- Table structure for pay_user
-- ----------------------------
DROP TABLE IF EXISTS `pay_user`;
CREATE TABLE `pay_user`  (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '支付区分方式 1 金额递增 2 金额递减',
  `mid` int(11) NOT NULL DEFAULT 1 COMMENT '套餐id',
  `aid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1' COMMENT '推广人id',
  `account` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `sxf` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '6' COMMENT '手续费',
  `money` decimal(12, 2) NOT NULL DEFAULT 0.00,
  `vKey` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '通信密钥',
  `lastHeart` int(10) NULL DEFAULT 0 COMMENT '最后心跳',
  `lastPay` int(10) NULL DEFAULT 0 COMMENT '最后支付',
  `wxpay` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信收款码地址',
  `zfbpay` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付宝收款码地址',
  `close` int(3) NOT NULL DEFAULT 5 COMMENT '订单有效期',
  `jobState` tinyint(1) NOT NULL DEFAULT -1 COMMENT '是否开启监控 -1未绑定 0掉线 1正常',
  `vipTime` int(10) NOT NULL DEFAULT 0,
  `createTime` int(10) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pay_user
-- ----------------------------
INSERT INTO `pay_user` VALUES (1, 1, 6, '1', 'admin', '###8367877b94c6a6df423fc3b21dcc2b36###', '6', 99995597.54, 'e6fc78624b135ecd034eb9e56c204a02', 1624017482, 1623856667, 'wxp://f2f0Z1KSTTPm4oNqHTLHSh02mgUeqMRsxA-8', 'https://qr.alipay.com/fkx19010tsh9emr5buozr1b', 5, 1, 0, 0, 1, '127.0.0.1');

SET FOREIGN_KEY_CHECKS = 1;
