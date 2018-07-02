SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `doc_api`
-- ----------------------------
DROP TABLE IF EXISTS `doc_api`;
CREATE TABLE `doc_api` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encode_id` varchar(10) NOT NULL COMMENT '加密id',
  `module_id` int(10) NOT NULL COMMENT '模块id',
  `title` varchar(250) NOT NULL COMMENT '接口名',
  `request_method` varchar(20) NOT NULL COMMENT '请求方式',
  `response_format` varchar(20) NOT NULL COMMENT '响应格式',
  `uri` varchar(250) NOT NULL COMMENT '接口地址',
  `header_field` text NOT NULL COMMENT 'header字段，json格式',
  `request_field` text NOT NULL COMMENT '请求字段，json格式',
  `response_field` text NOT NULL COMMENT '响应字段，json格式',
  `remark` varchar(250) NOT NULL DEFAULT '' COMMENT '接口简介',
  `status` tinyint(3) NOT NULL COMMENT '接口状态',
  `sort` int(10) NOT NULL COMMENT '接口排序',
  `creater_id` int(10) NOT NULL COMMENT '创建者id',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`encode_id`),
  UNIQUE KEY `encode_id` (`encode_id`),
  KEY `creater_id` (`creater_id`),
  KEY `module_id` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='项目接口表';

-- ----------------------------
--  Records of `doc_api`
-- ----------------------------
BEGIN;
INSERT INTO `doc_api` VALUES ('1', '7297025857', '1', '获取商品列表', 'post', 'json_object', '/goods/getGoodsList.json', '[{\"name\":\"Content-Type\",\"title\":\"\",\"value\":\"application\\/json;charset=utf-8\",\"remark\":\"\"},{\"name\":\"Accept\",\"title\":\"\",\"value\":\"application\\/json\",\"remark\":\"\"}]', '[{\"name\":\"token\",\"title\":\"令牌\",\"type\":\"string\",\"required\":10,\"default\":\"\",\"remark\":\"\"}]', '[{\"name\":\"code\",\"title\":\"返回状态码\",\"type\":\"integer\",\"mock\":\"\"},{\"name\":\"message\",\"title\":\"返回信息\",\"type\":\"string\",\"mock\":\"\"},{\"name\":\"data\",\"title\":\"数据实体\",\"type\":\"array\",\"mock\":\"\"}]', '', '10', '0', '1', '2018-06-25 02:58:57', '2018-06-25 03:08:47'), ('2', '1415030004', '1', '获取商品详情', 'post', 'json_object', '/goods/getGoodsInfo.json', '[{\"name\":\"Content-Type\",\"value\":\"application/json;charset=utf-8\",\"remark\":\"\",\"level\":\"0\",\"type\":\"string\"},{\"name\":\"Accept\",\"value\":\"application/json\",\"remark\":\"\",\"level\":\"0\",\"type\":\"string\"}]', '[{\"name\":\"id\",\"title\":\"商品id\",\"default\":\"\",\"remark\":\"\",\"level\":\"0\",\"type\":\"integer\",\"required\":\"10\"}]', '[{\"name\":\"code\",\"title\":\"返回状态码\",\"mock\":\"\",\"remark\":\"\",\"level\":\"0\",\"type\":\"integer\"},{\"name\":\"message\",\"title\":\"返回信息\",\"mock\":\"\",\"remark\":\"\",\"level\":\"0\",\"type\":\"string\"},{\"name\":\"data\",\"title\":\"数据实体\",\"mock\":\"\",\"remark\":\"\",\"level\":\"0\",\"type\":\"array\"},{\"name\":\"cover\",\"title\":\"商品封面\",\"mock\":\"\",\"remark\":\"\",\"level\":\"1\",\"type\":\"string\"},{\"name\":\"price\",\"title\":\"商品价格\",\"mock\":\"\",\"remark\":\"\",\"level\":\"1\",\"type\":\"float\"},{\"name\":\"title\",\"title\":\"商品名称\",\"mock\":\"\",\"remark\":\"\",\"level\":\"1\",\"type\":\"string\"}]', '', '10', '0', '1', '2018-06-25 03:00:04', '2018-06-25 03:08:51');
COMMIT;

-- ----------------------------
--  Table structure for `doc_apply`
-- ----------------------------
DROP TABLE IF EXISTS `doc_apply`;
CREATE TABLE `doc_apply` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `user_id` int(10) NOT NULL COMMENT '申请用户id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `checked_at` datetime DEFAULT NULL COMMENT '处理时间',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='申请加入项目表';

-- ----------------------------
--  Table structure for `doc_config`
-- ----------------------------
DROP TABLE IF EXISTS `doc_config`;
CREATE TABLE `doc_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL COMMENT '配置类型',
  `content` text NOT NULL COMMENT '配置内容',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='配置表';

-- ----------------------------
--  Records of `doc_config`
-- ----------------------------
BEGIN;
INSERT INTO `doc_config` VALUES ('1', 'app', '{\"name\":\"PHPRAP接口文档管理系统\",\"keywords\":\"phprap,apidoc,api文档管理\",\"description\":\"PHPRAP，是一个PHP轻量级开源API接口文档管理系统，致力于减少前后端沟通成本，提高团队协作开发效率，打造PHP版的RAP。\",\"copyright\":\"Copyright ©2016-2017 PHPRAP版权所有\",\"email\":\"245629560@qq.com\",\"is_push\":\"0\",\"push_time\":\"10\"}', '2018-05-15 14:08:31', '2018-05-24 21:46:40'), ('3', 'email', '', '2018-05-15 14:08:35', '2018-05-15 14:08:38'), ('4', 'safe', '{\"ip_white_list\":\"\",\"ip_black_list\":\"\",\"email_white_list\":\"\",\"email_black_list\":\"\",\"register_token\":\"\",\"register_captcha\":\"1\",\"login_captcha\":\"1\",\"login_keep_time\":\"24\"}', '2018-05-15 14:08:39', '2018-06-25 02:55:51');
COMMIT;

-- ----------------------------
--  Table structure for `doc_env`
-- ----------------------------
DROP TABLE IF EXISTS `doc_env`;
CREATE TABLE `doc_env` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encode_id` varchar(10) NOT NULL COMMENT '加密id',
  `title` varchar(50) NOT NULL COMMENT '环境名称',
  `name` varchar(10) NOT NULL COMMENT '环境标识',
  `base_url` varchar(250) NOT NULL COMMENT '环境根路径',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '环境排序',
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '环境状态',
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `creater_id` int(10) NOT NULL COMMENT '创建者id',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`encode_id`),
  UNIQUE KEY `encode_id` (`encode_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `doc_env`
-- ----------------------------
BEGIN;
INSERT INTO `doc_env` VALUES ('1', '8913030143', '生产环境', 'product', 'http://www.phprap.com', '0', '10', '1', '1', '2018-06-25 03:01:43', '2018-06-25 03:01:43');
COMMIT;

-- ----------------------------
--  Table structure for `doc_login_log`
-- ----------------------------
DROP TABLE IF EXISTS `doc_login_log`;
CREATE TABLE `doc_login_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `user_name` varchar(50) NOT NULL COMMENT '用户名称',
  `user_email` varchar(50) NOT NULL COMMENT '用户邮箱',
  `ip` varchar(50) NOT NULL COMMENT '登录ip',
  `location` varchar(255) NOT NULL DEFAULT '' COMMENT '登录地址',
  `created_at` datetime DEFAULT NULL COMMENT '登录时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='登录日志表';

-- ----------------------------
--  Records of `doc_login_log`
-- ----------------------------
BEGIN;
INSERT INTO `doc_login_log` VALUES ('1', '1', 'phprap', 'admin@phprap.com', '111.193.193.17', '中国 北京 北京 ', '2018-06-25 02:56:04', '2018-06-25 02:56:04');
COMMIT;

-- ----------------------------
--  Table structure for `doc_member`
-- ----------------------------
DROP TABLE IF EXISTS `doc_member`;
CREATE TABLE `doc_member` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encode_id` varchar(10) NOT NULL COMMENT '加密id',
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `project_rule` varchar(100) NOT NULL DEFAULT '' COMMENT '项目权限',
  `version_rule` varchar(100) NOT NULL DEFAULT '' COMMENT '版本权限',
  `env_rule` varchar(100) NOT NULL COMMENT '环境权限',
  `module_rule` varchar(100) NOT NULL DEFAULT '' COMMENT '模块权限',
  `api_rule` varchar(100) NOT NULL DEFAULT '' COMMENT '接口权限',
  `member_rule` varchar(100) NOT NULL DEFAULT '' COMMENT '成员权限',
  `creater_id` int(10) NOT NULL COMMENT '创建者id',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`encode_id`),
  UNIQUE KEY `encode_id` (`encode_id`) USING BTREE,
  KEY `user_id_index` (`user_id`) USING BTREE,
  KEY `project_id_index` (`project_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目成员表';

-- ----------------------------
--  Table structure for `doc_module`
-- ----------------------------
DROP TABLE IF EXISTS `doc_module`;
CREATE TABLE `doc_module` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encode_id` varchar(10) NOT NULL COMMENT '加密id',
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `version_id` int(10) NOT NULL COMMENT '版本id',
  `title` varchar(50) NOT NULL COMMENT '模块名称',
  `remark` varchar(250) NOT NULL DEFAULT '' COMMENT '项目描述',
  `status` tinyint(3) NOT NULL COMMENT '模块状态 ',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '模块排序',
  `creater_id` int(10) NOT NULL COMMENT '创建者id',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`encode_id`),
  UNIQUE KEY `encode_id` (`encode_id`),
  KEY `project_id` (`project_id`) USING BTREE,
  KEY `user_id` (`creater_id`) USING BTREE,
  KEY `creater_id` (`creater_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='项目模块表';

-- ----------------------------
--  Records of `doc_module`
-- ----------------------------
BEGIN;
INSERT INTO `doc_module` VALUES ('1', '2518025742', '1', '1', '商品模块', '', '10', '20', '1', '2018-06-25 02:57:42', '2018-06-25 03:07:54');
COMMIT;

-- ----------------------------
--  Table structure for `doc_project`
-- ----------------------------
DROP TABLE IF EXISTS `doc_project`;
CREATE TABLE `doc_project` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encode_id` varchar(10) NOT NULL COMMENT '加密id',
  `title` varchar(250) NOT NULL COMMENT '项目名称',
  `remark` varchar(250) NOT NULL DEFAULT '' COMMENT '项目描述',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '项目排序',
  `type` tinyint(3) NOT NULL COMMENT '项目类型',
  `status` tinyint(3) NOT NULL COMMENT '项目状态',
  `creater_id` int(10) NOT NULL COMMENT '创建者id',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`encode_id`),
  UNIQUE KEY `encode_id` (`encode_id`),
  KEY `user_id` (`creater_id`),
  KEY `creater_id` (`creater_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='项目表';

-- ----------------------------
--  Records of `doc_project`
-- ----------------------------
BEGIN;
INSERT INTO `doc_project` VALUES ('1', '2844025653', '演示项目', '这是一个演示项目', '1', '20', '10', '1', '2018-06-25 02:56:53', '2018-06-25 02:56:53');
COMMIT;

-- ----------------------------
--  Table structure for `doc_project_log`
-- ----------------------------
DROP TABLE IF EXISTS `doc_project_log`;
CREATE TABLE `doc_project_log` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `module_id` int(10) NOT NULL DEFAULT '0' COMMENT '模块id',
  `api_id` int(10) NOT NULL DEFAULT '0' COMMENT '接口id',
  `user_id` int(10) NOT NULL COMMENT '操作人id',
  `user_name` varchar(50) NOT NULL COMMENT '操作人昵称',
  `user_email` varchar(50) NOT NULL COMMENT '操作人邮箱',
  `version_id` int(10) NOT NULL DEFAULT '0' COMMENT '操作版本id',
  `version_name` varchar(255) NOT NULL DEFAULT '' COMMENT '操作版本号',
  `method` varchar(10) NOT NULL COMMENT '操作方式',
  `object_name` varchar(20) NOT NULL COMMENT '操作对象',
  `object_id` int(10) NOT NULL COMMENT '操作对象id',
  `content` text NOT NULL COMMENT '操作内容',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `object_id` (`object_id`),
  KEY `project_id` (`project_id`) USING BTREE,
  KEY `version_id` (`version_id`),
  KEY `module_id` (`module_id`),
  KEY `api_id` (`api_id`)
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8 COMMENT='项目日志表';

-- ----------------------------
--  Table structure for `doc_template`
-- ----------------------------
DROP TABLE IF EXISTS `doc_template`;
CREATE TABLE `doc_template` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encode_id` varchar(10) NOT NULL COMMENT '加密id',
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `header_field` text NOT NULL COMMENT 'header参数，json格式',
  `request_field` text NOT NULL COMMENT '请求参数，json格式',
  `response_field` text NOT NULL COMMENT '响应参数，json格式',
  `status` tinyint(3) NOT NULL COMMENT '模板状态',
  `creater_id` int(10) NOT NULL COMMENT '创建者id',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`encode_id`),
  UNIQUE KEY `project_id` (`project_id`),
  UNIQUE KEY `encode_id` (`encode_id`),
  KEY `creater_id` (`creater_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `doc_template`
-- ----------------------------
BEGIN;
INSERT INTO `doc_template` VALUES ('34', '2198095118', '42', '[{\"name\":\"Content-Type\",\"title\":\"\",\"value\":\"application\\/json;charset=utf-8\",\"remark\":\"\"},{\"name\":\"Accept\",\"title\":\"\",\"value\":\"application\\/json\",\"remark\":\"\"}]', '[{\"name\":\"token\",\"title\":\"令牌\",\"type\":\"string\",\"required\":10,\"default\":\"\",\"remark\":\"\"}]', '[{\"name\":\"code\",\"title\":\"返回状态码\",\"type\":\"integer\",\"mock\":\"\"},{\"name\":\"message\",\"title\":\"返回信息\",\"type\":\"string\",\"mock\":\"\"},{\"name\":\"data\",\"title\":\"数据实体\",\"type\":\"array\",\"mock\":\"\"}]', '10', '7', '2018-06-13 09:51:18', '2018-06-13 09:51:18'), ('35', '4859101638', '43', '[{\"name\":\"Content-Type\",\"title\":\"\",\"value\":\"application\\/json;charset=utf-8\",\"remark\":\"\"},{\"name\":\"Accept\",\"title\":\"\",\"value\":\"application\\/json\",\"remark\":\"\"}]', '[{\"name\":\"token\",\"title\":\"令牌\",\"type\":\"string\",\"required\":10,\"default\":\"\",\"remark\":\"\"}]', '[{\"name\":\"code\",\"title\":\"返回状态码\",\"type\":\"integer\",\"mock\":\"\"},{\"name\":\"message\",\"title\":\"返回信息\",\"type\":\"string\",\"mock\":\"\"},{\"name\":\"data\",\"title\":\"数据实体\",\"type\":\"array\",\"mock\":\"\"}]', '10', '7', '2018-06-13 10:16:38', '2018-06-13 10:16:38'), ('36', '2265111411', '44', '[{\"name\":\"Content-Type\",\"title\":\"\",\"value\":\"application\\/json;charset=utf-8\",\"remark\":\"\"},{\"name\":\"Accept\",\"title\":\"\",\"value\":\"application\\/json\",\"remark\":\"\"}]', '[{\"name\":\"token\",\"title\":\"令牌\",\"type\":\"string\",\"required\":10,\"default\":\"\",\"remark\":\"\"}]', '[{\"name\":\"code\",\"title\":\"返回状态码\",\"type\":\"integer\",\"mock\":\"\"},{\"name\":\"message\",\"title\":\"返回信息\",\"type\":\"string\",\"mock\":\"\"},{\"name\":\"data\",\"title\":\"数据实体\",\"type\":\"array\",\"mock\":\"\"}]', '10', '17', '2018-06-21 11:14:11', '2018-06-21 11:14:11'), ('37', '6969025653', '1', '[{\"name\":\"Content-Type\",\"title\":\"\",\"value\":\"application\\/json;charset=utf-8\",\"remark\":\"\"},{\"name\":\"Accept\",\"title\":\"\",\"value\":\"application\\/json\",\"remark\":\"\"}]', '[{\"name\":\"token\",\"title\":\"令牌\",\"type\":\"string\",\"required\":10,\"default\":\"\",\"remark\":\"\"}]', '[{\"name\":\"code\",\"title\":\"返回状态码\",\"type\":\"integer\",\"mock\":\"\"},{\"name\":\"message\",\"title\":\"返回信息\",\"type\":\"string\",\"mock\":\"\"},{\"name\":\"data\",\"title\":\"数据实体\",\"type\":\"array\",\"mock\":\"\"}]', '10', '1', '2018-06-25 02:56:53', '2018-06-25 02:56:53');
COMMIT;

-- ----------------------------
--  Table structure for `doc_user`
-- ----------------------------
DROP TABLE IF EXISTS `doc_user`;
CREATE TABLE `doc_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '登录邮箱',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `password_hash` varchar(250) NOT NULL DEFAULT '' COMMENT '密码',
  `auth_key` varchar(250) NOT NULL,
  `type` tinyint(3) NOT NULL DEFAULT '10' COMMENT '用户类型，10:普通用户 20:管理员',
  `status` tinyint(3) NOT NULL COMMENT '会员状态',
  `ip` varchar(250) NOT NULL DEFAULT '' COMMENT '注册ip',
  `location` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员表';

-- ----------------------------
--  Records of `doc_user`
-- ----------------------------
BEGIN;
INSERT INTO `doc_user` VALUES ('1', 'admin@phprap.com', '演示账号', '$2y$13$FJsNsfUR79id5FYu0CMxnO8n5RvMAPiTLEMJgcN6YK5FqiiLCes3m', 'G9tH8w1vnF5JlFgSAMZXwjFniy_GYLln', '10', '10', '111.193.193.17', '中国 北京 北京 ', '2018-06-25 02:56:04', '2018-06-25 02:56:04');
COMMIT;

-- ----------------------------
--  Table structure for `doc_version`
-- ----------------------------
DROP TABLE IF EXISTS `doc_version`;
CREATE TABLE `doc_version` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `encode_id` varchar(10) NOT NULL COMMENT '加密id',
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '父级版本id',
  `creater_id` int(10) NOT NULL COMMENT '版本创建者id',
  `name` varchar(10) NOT NULL COMMENT '版本号',
  `remark` varchar(250) NOT NULL DEFAULT '' COMMENT '备注信息',
  `status` tinyint(3) NOT NULL COMMENT '版本状态',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`encode_id`),
  UNIQUE KEY `encode_id` (`encode_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='项目版本表';

-- ----------------------------
--  Records of `doc_version`
-- ----------------------------
BEGIN;
INSERT INTO `doc_version` VALUES ('1', '1071025653', '1', '0', '1', '1.0', '初始版本', '10', '2018-06-25 02:56:53', '2018-06-25 02:56:53');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
