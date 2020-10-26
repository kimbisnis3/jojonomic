/*
 Navicat Premium Data Transfer

 Source Server         : LOCALHOST
 Source Server Type    : MySQL
 Source Server Version : 100414
 Source Host           : localhost:3306
 Source Schema         : jojonomic

 Target Server Type    : MySQL
 Target Server Version : 100414
 File Encoding         : 65001

 Date: 26/10/2020 22:02:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for company
-- ----------------------------
DROP TABLE IF EXISTS `company`;
CREATE TABLE `company`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company
-- ----------------------------
INSERT INTO `company` VALUES (1, 'Stark Industries', 'Stark street');
INSERT INTO `company` VALUES (2, 'Targaryen Company', 'TS street');
INSERT INTO `company` VALUES (3, 'Tyrell Company', 'Tyrell hill');
INSERT INTO `company` VALUES (7, 'rrr', 'rrrr');

-- ----------------------------
-- Table structure for company_budget
-- ----------------------------
DROP TABLE IF EXISTS `company_budget`;
CREATE TABLE `company_budget`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NULL DEFAULT NULL,
  `amount` decimal(19, 2) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of company_budget
-- ----------------------------
INSERT INTO `company_budget` VALUES (1, 1, 2000000000.00);
INSERT INTO `company_budget` VALUES (2, 2, 399997000.00);
INSERT INTO `company_budget` VALUES (3, 3, 1999400.00);

-- ----------------------------
-- Table structure for transaction
-- ----------------------------
DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `user_id` bigint(20) NULL DEFAULT NULL,
  `amount` decimal(19, 2) NULL DEFAULT NULL,
  `date` timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transaction
-- ----------------------------
INSERT INTO `transaction` VALUES (1, 'R', 2, 20000.00, '2020-10-26 17:50:45');
INSERT INTO `transaction` VALUES (2, 'R', 3, 100.00, NULL);
INSERT INTO `transaction` VALUES (3, 'R', 3, 100.00, NULL);
INSERT INTO `transaction` VALUES (4, 'R', 3, 300.00, NULL);
INSERT INTO `transaction` VALUES (5, 'R', 2, 300.00, NULL);
INSERT INTO `transaction` VALUES (6, 'R', 2, 300.00, '0000-00-00 00:00:00');
INSERT INTO `transaction` VALUES (7, 'R', 2, 300.00, NULL);
INSERT INTO `transaction` VALUES (8, 'R', 2, 300.00, '0000-00-00 00:00:00');
INSERT INTO `transaction` VALUES (9, 'R', 2, 300.00, '0000-00-00 00:00:00');
INSERT INTO `transaction` VALUES (10, 'R', 2, 300.00, '0000-00-00 00:00:00');
INSERT INTO `transaction` VALUES (11, 'R', 2, 300.00, '2020-10-26 15:57:35');
INSERT INTO `transaction` VALUES (12, 'R', 2, 300.00, '2020-10-26 15:58:53');
INSERT INTO `transaction` VALUES (13, 'R', 2, 300.00, '2020-10-26 15:59:04');
INSERT INTO `transaction` VALUES (14, 'R', 2, 300.00, '2020-10-26 15:59:12');
INSERT INTO `transaction` VALUES (15, 'C', 2, 300.00, '2020-10-26 16:00:16');
INSERT INTO `transaction` VALUES (16, 'S', 2, 300.00, '2020-10-26 16:00:59');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `account` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `company_id` bigint(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 32 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'Jon', 'Snow', 'jonsnow@got.com', '4488992884234', 1);
INSERT INTO `user` VALUES (2, 'Arya', 'Stark', 'arstark@got.com', '111233144', 2);
INSERT INTO `user` VALUES (3, 'Theon', 'Grejoy', 'tgrey@got.com', '009384827342', 3);
INSERT INTO `user` VALUES (30, 'Loras', 'Tyrell', 'lt@got.com', '34234234234234', 3);

SET FOREIGN_KEY_CHECKS = 1;
