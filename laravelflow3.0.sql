/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : workflow

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-03-06 11:19:22
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wf_flow
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow`;
CREATE TABLE `wf_flow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL COMMENT '流程类别',
  `flow_name` varchar(255) NOT NULL DEFAULT '' COMMENT '流程名称',
  `flow_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `sort_order` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不可用1正常',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='*工作流表';

-- ----------------------------
-- Records of wf_flow
-- ----------------------------
INSERT INTO `wf_flow` VALUES ('1', null, '', '', '0', '0', '0', null, '1551690678', '2019-03-04 09:11:18', '2019-03-04 09:11:18');
INSERT INTO `wf_flow` VALUES ('2', null, '', '', '0', '0', '0', null, '1551690793', '2019-03-04 09:13:13', '2019-03-04 09:13:13');
INSERT INTO `wf_flow` VALUES ('3', 'news', '师德师风', '阿斯顿发送到', '0', '0', '0', null, '1551690808', '2019-03-04 09:13:28', '2019-03-04 18:31:12');
INSERT INTO `wf_flow` VALUES ('4', 'news', 'asdfasdfasdfasdf', 'sasdfasdf', '0', '0', '0', '13', '1551749957', '2019-03-05 09:39:17', '2019-03-05 18:00:17');

-- ----------------------------
-- Table structure for wf_flow_process
-- ----------------------------
DROP TABLE IF EXISTS `wf_flow_process`;
CREATE TABLE `wf_flow_process` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `process_name` varchar(255) NOT NULL DEFAULT '步骤' COMMENT '步骤名称',
  `process_type` char(10) NOT NULL DEFAULT '' COMMENT '步骤类型',
  `process_to` varchar(255) NOT NULL DEFAULT '' COMMENT '转交下一步骤号',
  `child_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'is_child 子流程id有return_step_to结束后继续父流程下一步',
  `child_relation` text COMMENT '[保留功能]父子流程字段映射关系',
  `child_after` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '子流程 结束后动作 0结束并更新父流程节点为结束  1结束并返回父流程步骤',
  `child_back_process` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '子流程结束返回的步骤id',
  `return_sponsor_ids` text COMMENT '[保留功能]主办人 子流程结束后下一步的主办人',
  `return_respon_ids` text COMMENT '[保留功能]经办人 子流程结束后下一步的经办人',
  `write_fields` text COMMENT '这个步骤可写的字段',
  `secret_fields` text COMMENT '这个步骤隐藏的字段',
  `lock_fields` text COMMENT '锁定不能更改宏控件的值',
  `check_fields` text COMMENT '字段验证规则',
  `auto_person` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '本步骤的自动选主办人规则0:为不自动选择1：流程发起人2：本部门主管3指定默认人4上级主管领导5. 一级部门主管6. 指定步骤主办人',
  `auto_unlock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许修改主办人auto_type>0 0不允许 1允许（默认）',
  `auto_sponsor_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人ids',
  `auto_sponsor_text` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人text',
  `auto_respon_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人ids',
  `auto_respon_text` varchar(255) NOT NULL DEFAULT '' COMMENT '3指定步骤主办人text',
  `auto_role_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '制定默认角色ids',
  `auto_role_text` varchar(255) NOT NULL DEFAULT '' COMMENT '制定默认角色 text',
  `auto_process_sponsor` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '[保留功能]指定其中一个步骤的主办人处理',
  `range_user_ids` text COMMENT '本步骤的经办人授权范围ids',
  `range_user_text` text COMMENT '本步骤的经办人授权范围text',
  `range_dept_ids` text COMMENT '本步骤的经办部门授权范围',
  `range_dept_text` text COMMENT '本步骤的经办部门授权范围text',
  `range_role_ids` text COMMENT '本步骤的经办角色授权范围ids',
  `range_role_text` text COMMENT '本步骤的经办角色授权范围text',
  `receive_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0明确指定主办人1先接收者为主办人',
  `is_user_end` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '允许主办人在非最后步骤也可以办结流程',
  `is_userop_pass` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '经办人可以转交下一步',
  `is_sing` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会签选项0禁止会签1允许会签（默认） 2强制会签',
  `sign_look` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会签可见性0总是可见（默认）,1本步骤经办人之间不可见2针对其他步骤不可见',
  `is_back` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许回退0不允许（默认） 1允许退回上一步2允许退回之前步骤',
  `out_condition` text COMMENT '转出条件',
  `setleft` smallint(5) unsigned NOT NULL DEFAULT '100' COMMENT '左 坐标',
  `settop` smallint(5) unsigned NOT NULL DEFAULT '100' COMMENT '上 坐标',
  `style` text COMMENT '样式 序列化',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `wf_mode` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 单一线性，1，转出条件 2，同步模式',
  `wf_action` varchar(255) NOT NULL DEFAULT 'view' COMMENT '对应方法',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_flow_process
-- ----------------------------
INSERT INTO `wf_flow_process` VALUES ('5', '4', '审核', 'is_one', '6', '0', null, '0', '0', null, null, null, null, null, null, '4', '0', '7', '市场部员工1', '', '', '', '', '0', '', '', null, null, null, null, '0', '0', '0', '1', '0', '1', '[]', '100', '100', '{\"width\":\"120\",\"height\":\"38\",\"color\":\"#0e76a8\"}', '0', '1551776977', '0', '0', 'view', '2019-03-05 16:47:07', '2019-03-05 17:09:37');
INSERT INTO `wf_flow_process` VALUES ('6', '4', '发布', 'is_step', '', '0', null, '0', '0', null, null, null, null, null, null, '4', '0', '13', '总经理', '', '', '', '', '0', '', '', null, null, null, null, '0', '0', '0', '2', '0', '1', '[]', '100', '267', '{\"width\":\"120\",\"height\":\"38\",\"color\":\"#0e76a8\"}', '0', '1551776977', '0', '0', 'view', '2019-03-05 17:09:09', '2019-03-05 17:09:37');

-- ----------------------------
-- Table structure for wf_form
-- ----------------------------
DROP TABLE IF EXISTS `wf_form`;
CREATE TABLE `wf_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '表单名称',
  `name` varchar(255) DEFAULT NULL COMMENT '表名',
  `file` varchar(255) DEFAULT NULL COMMENT '生成文件',
  `menu` int(11) NOT NULL DEFAULT '0',
  `flow` int(11) NOT NULL DEFAULT '0',
  `ziduan` longtext,
  `uid` varchar(255) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `status` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_form
-- ----------------------------
INSERT INTO `wf_form` VALUES ('1', '业务测试', 'ywtest', 'all', '0', '0', null, null, '1547513664', '0', null, null);

-- ----------------------------
-- Table structure for wf_form_function
-- ----------------------------
DROP TABLE IF EXISTS `wf_form_function`;
CREATE TABLE `wf_form_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT NULL,
  `sql` longtext,
  `name` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_form_function
-- ----------------------------

-- ----------------------------
-- Table structure for wf_menu
-- ----------------------------
DROP TABLE IF EXISTS `wf_menu`;
CREATE TABLE `wf_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_menu
-- ----------------------------

-- ----------------------------
-- Table structure for wf_news
-- ----------------------------
DROP TABLE IF EXISTS `wf_news`;
CREATE TABLE `wf_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `add_time` int(11) DEFAULT NULL COMMENT '新增时间',
  `new_title` varchar(255) DEFAULT NULL COMMENT '新闻标题',
  `new_type` int(11) DEFAULT NULL COMMENT '新闻类别',
  `new_top` int(11) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `new_con` longtext COMMENT '新闻内容',
  `new_user` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '-1回退修改0 保存中1流程中 2通过',
  `uptime` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_news
-- ----------------------------
INSERT INTO `wf_news` VALUES ('1', '13', '1551777600', '阿斯顿发', '2', '0', '阿斯顿发as阿斯顿发asd		阿斯顿发\r\n	阿斯顿发斯蒂芬阿斯顿发	发斯蒂芬asd	阿斯顿发阿斯顿发阿斯顿发送到发送到asd阿斯顿发送到\r\n			ASD阿斯顿', null, '2', '1551838589', null, null);
INSERT INTO `wf_news` VALUES ('2', '7', '1551837226', '阿斯顿发送到发', '2', '1', '阿发送到发水电费阿斯顿发送到发送到阿斯顿发送到发送到阿斯顿发阿斯顿发阿斯顿发送到发送到发送到发斯蒂芬阿斯顿发送到发送到发送到发送到发送到分发生打发阿斯顿发斯蒂芬阿斯顿发阿斯顿发阿斯顿发送到阿斯顿', null, '2', '1551837471', null, null);
INSERT INTO `wf_news` VALUES ('3', '13', '1551838332', 'asdfasdf', '2', '1', '阿斯顿发送到发', null, '2', '1551838523', null, null);

-- ----------------------------
-- Table structure for wf_news_type
-- ----------------------------
DROP TABLE IF EXISTS `wf_news_type`;
CREATE TABLE `wf_news_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_news_type
-- ----------------------------

-- ----------------------------
-- Table structure for wf_role
-- ----------------------------
DROP TABLE IF EXISTS `wf_role`;
CREATE TABLE `wf_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '后台组名',
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '是否激活 1：是 0：否',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序权重',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_role
-- ----------------------------
INSERT INTO `wf_role` VALUES ('15', '市场部', '0', '1', '0', '', null, null);
INSERT INTO `wf_role` VALUES ('16', '工程部', '0', '1', '0', '', null, null);
INSERT INTO `wf_role` VALUES ('17', '新闻部', '0', '1', '0', '', null, null);
INSERT INTO `wf_role` VALUES ('18', '新闻部经理', '0', '1', '0', '', null, null);
INSERT INTO `wf_role` VALUES ('19', '工程部经理', '0', '1', '0', '', null, null);
INSERT INTO `wf_role` VALUES ('20', '市场部经理', '0', '1', '0', '', null, null);
INSERT INTO `wf_role` VALUES ('21', '总经理', '0', '1', '0', '', null, null);

-- ----------------------------
-- Table structure for wf_role_user
-- ----------------------------
DROP TABLE IF EXISTS `wf_role_user`;
CREATE TABLE `wf_role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` smallint(6) unsigned NOT NULL,
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_role_user
-- ----------------------------
INSERT INTO `wf_role_user` VALUES ('7', '15');
INSERT INTO `wf_role_user` VALUES ('8', '15');
INSERT INTO `wf_role_user` VALUES ('9', '17');
INSERT INTO `wf_role_user` VALUES ('10', '18');
INSERT INTO `wf_role_user` VALUES ('11', '20');
INSERT INTO `wf_role_user` VALUES ('12', '19');
INSERT INTO `wf_role_user` VALUES ('13', '21');

-- ----------------------------
-- Table structure for wf_run
-- ----------------------------
DROP TABLE IF EXISTS `wf_run`;
CREATE TABLE `wf_run` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'work_run父流转公文ID 值大于0则这个是子流程，完成后或者要返回父流程',
  `from_table` varchar(255) DEFAULT NULL COMMENT '单据表，不带前缀',
  `from_id` int(11) DEFAULT NULL,
  `pid_flow_step` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父pid的flow_id中的第几步骤进入的,取回这个work_flow_step的child_over决定结束子流程的动作',
  `cache_run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '多个子流程时pid无法识别cache所以加这个字段pid>0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程id 正常流程',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程分类ID即公文分类ID',
  `run_name` varchar(255) DEFAULT '' COMMENT '公文名称',
  `run_flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流转到什么流程 最新流程，查询优化，进入子流程时将简化查询，子流程与父流程同步',
  `run_flow_process` varchar(255) DEFAULT NULL COMMENT '流转到第几步',
  `att_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '公文附件ids',
  `endtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，0流程中，1通过,2回退',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `is_sing` int(11) NOT NULL DEFAULT '0',
  `sing_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `pid_flow_step` (`pid_flow_step`),
  KEY `cache_run_id` (`cache_run_id`),
  KEY `uid` (`uid`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run
-- ----------------------------
INSERT INTO `wf_run` VALUES ('1', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551783302', '1', '0', '0', '1551780461', '0', null, '2019-03-05 18:07:41', '2019-03-05 18:55:02');
INSERT INTO `wf_run` VALUES ('2', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551783315', '1', '0', '0', '1551780867', '0', null, '2019-03-05 18:14:27', '2019-03-05 18:55:15');
INSERT INTO `wf_run` VALUES ('3', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551783313', '1', '0', '0', '1551780934', '0', null, '2019-03-05 18:15:34', '2019-03-05 18:55:13');
INSERT INTO `wf_run` VALUES ('4', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551783317', '1', '0', '0', '1551781066', '0', null, '2019-03-05 18:17:46', '2019-03-05 18:55:17');
INSERT INTO `wf_run` VALUES ('5', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551835072', '1', '0', '0', '1551781093', '0', null, '2019-03-05 18:18:13', '2019-03-06 09:17:52');
INSERT INTO `wf_run` VALUES ('6', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551835088', '1', '0', '0', '1551781144', '0', null, '2019-03-05 18:19:04', '2019-03-06 09:18:08');
INSERT INTO `wf_run` VALUES ('7', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838574', '1', '0', '0', '1551781151', '0', null, '2019-03-05 18:19:11', '2019-03-06 10:16:14');
INSERT INTO `wf_run` VALUES ('8', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838579', '1', '0', '0', '1551781168', '0', null, '2019-03-05 18:19:28', '2019-03-06 10:16:19');
INSERT INTO `wf_run` VALUES ('9', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838581', '1', '0', '0', '1551781179', '0', null, '2019-03-05 18:19:39', '2019-03-06 10:16:21');
INSERT INTO `wf_run` VALUES ('10', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838582', '1', '0', '0', '1551781206', '0', null, '2019-03-05 18:20:06', '2019-03-06 10:16:22');
INSERT INTO `wf_run` VALUES ('11', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838584', '1', '0', '0', '1551781220', '0', null, '2019-03-05 18:20:20', '2019-03-06 10:16:24');
INSERT INTO `wf_run` VALUES ('12', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838586', '1', '0', '0', '1551781244', '0', null, '2019-03-05 18:20:44', '2019-03-06 10:16:26');
INSERT INTO `wf_run` VALUES ('13', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838587', '1', '0', '0', '1551781316', '0', null, '2019-03-05 18:21:56', '2019-03-06 10:16:27');
INSERT INTO `wf_run` VALUES ('14', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838589', '1', '0', '0', '1551781340', '1', '2', '2019-03-05 18:22:20', '2019-03-06 10:16:29');
INSERT INTO `wf_run` VALUES ('15', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '6', '', '1551835605', '1', '0', '0', '1551781355', '0', null, '2019-03-05 18:22:35', '2019-03-06 09:26:45');
INSERT INTO `wf_run` VALUES ('16', '0', 'news', '2', '0', '0', '7', '4', '0', '2', '4', '6', '', '1551837471', '1', '0', '0', '1551837245', '0', null, '2019-03-06 09:54:05', '2019-03-06 09:57:51');
INSERT INTO `wf_run` VALUES ('17', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '5', '', '1551838419', '1', '0', '0', '1551838341', '0', null, '2019-03-06 10:12:21', '2019-03-06 10:13:39');
INSERT INTO `wf_run` VALUES ('18', '0', 'news', '3', '0', '0', '10', '4', '0', '3', '4', '6', '', '1551838523', '1', '0', '0', '1551838483', '0', null, '2019-03-06 10:14:43', '2019-03-06 10:15:23');
INSERT INTO `wf_run` VALUES ('19', '0', 'news', '1', '0', '0', '13', '4', '0', '1', '4', '6', '', '1551838565', '1', '0', '0', '1551838537', '0', null, '2019-03-06 10:15:37', '2019-03-06 10:16:05');

-- ----------------------------
-- Table structure for wf_run_cache
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_cache`;
CREATE TABLE `wf_run_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 缓存run工作的全部流程模板步骤等信息,确保修改流程后工作依然不变',
  `form_id` int(10) unsigned NOT NULL DEFAULT '0',
  `flow_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID',
  `run_form` text COMMENT '模板信息',
  `run_flow` text COMMENT '流程信息',
  `run_flow_process` text COMMENT '流程步骤信息 ',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `run_id` (`run_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run_cache
-- ----------------------------
INSERT INTO `wf_run_cache` VALUES ('1', '11', '1', '1', '', '{\"id\":1,\"uid\":13,\"add_time\":1551777600,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\",\"new_type\":2,\"new_top\":0,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1as\\u963f\\u65af\\u987f\\u53d1asd\\t\\t\\u963f\\u65af\\u987f\\u53d1\\r\\n\\t\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\t\\u53d1\\u65af\\u8482\\u82acasd\\t\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230asd\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\r\\n\\t\\t\\tASD\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":0,\"uptime\":null,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551781220', '2019-03-05 18:20:20', '2019-03-05 18:20:20');
INSERT INTO `wf_run_cache` VALUES ('2', '12', '1', '1', '', '{\"id\":1,\"uid\":13,\"add_time\":1551777600,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\",\"new_type\":2,\"new_top\":0,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1as\\u963f\\u65af\\u987f\\u53d1asd\\t\\t\\u963f\\u65af\\u987f\\u53d1\\r\\n\\t\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\t\\u53d1\\u65af\\u8482\\u82acasd\\t\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230asd\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\r\\n\\t\\t\\tASD\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":0,\"uptime\":null,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551781244', '2019-03-05 18:20:44', '2019-03-05 18:20:44');
INSERT INTO `wf_run_cache` VALUES ('3', '13', '1', '1', '', '{\"id\":1,\"uid\":13,\"add_time\":1551777600,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\",\"new_type\":2,\"new_top\":0,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1as\\u963f\\u65af\\u987f\\u53d1asd\\t\\t\\u963f\\u65af\\u987f\\u53d1\\r\\n\\t\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\t\\u53d1\\u65af\\u8482\\u82acasd\\t\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230asd\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\r\\n\\t\\t\\tASD\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":1,\"uptime\":1551781244,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551781316', '2019-03-05 18:21:56', '2019-03-05 18:21:56');
INSERT INTO `wf_run_cache` VALUES ('4', '14', '1', '1', '', '{\"id\":1,\"uid\":13,\"add_time\":1551777600,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\",\"new_type\":2,\"new_top\":0,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1as\\u963f\\u65af\\u987f\\u53d1asd\\t\\t\\u963f\\u65af\\u987f\\u53d1\\r\\n\\t\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\t\\u53d1\\u65af\\u8482\\u82acasd\\t\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230asd\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\r\\n\\t\\t\\tASD\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":1,\"uptime\":1551781316,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551781340', '2019-03-05 18:22:20', '2019-03-05 18:22:20');
INSERT INTO `wf_run_cache` VALUES ('5', '15', '1', '1', '', '{\"id\":1,\"uid\":13,\"add_time\":1551777600,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\",\"new_type\":2,\"new_top\":0,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1as\\u963f\\u65af\\u987f\\u53d1asd\\t\\t\\u963f\\u65af\\u987f\\u53d1\\r\\n\\t\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\t\\u53d1\\u65af\\u8482\\u82acasd\\t\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230asd\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\r\\n\\t\\t\\tASD\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":1,\"uptime\":1551781340,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551781355', '2019-03-05 18:22:35', '2019-03-05 18:22:35');
INSERT INTO `wf_run_cache` VALUES ('6', '16', '2', '2', '', '{\"id\":2,\"uid\":7,\"add_time\":1551837226,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\",\"new_type\":2,\"new_top\":1,\"new_con\":\"\\u963f\\u53d1\\u9001\\u5230\\u53d1\\u6c34\\u7535\\u8d39\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230\\u5206\\u53d1\\u751f\\u6253\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":0,\"uptime\":null,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551837245', '2019-03-06 09:54:05', '2019-03-06 09:54:05');
INSERT INTO `wf_run_cache` VALUES ('7', '17', '1', '1', '', '{\"id\":1,\"uid\":13,\"add_time\":1551777600,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\",\"new_type\":2,\"new_top\":0,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1as\\u963f\\u65af\\u987f\\u53d1asd\\t\\t\\u963f\\u65af\\u987f\\u53d1\\r\\n\\t\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\t\\u53d1\\u65af\\u8482\\u82acasd\\t\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230asd\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\r\\n\\t\\t\\tASD\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":0,\"uptime\":1551835605,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551838341', '2019-03-06 10:12:21', '2019-03-06 10:12:21');
INSERT INTO `wf_run_cache` VALUES ('8', '18', '3', '3', '', '{\"id\":3,\"uid\":13,\"add_time\":1551838332,\"new_title\":\"asdfasdf\",\"new_type\":2,\"new_top\":1,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\",\"new_user\":null,\"status\":0,\"uptime\":null,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551838483', '2019-03-06 10:14:43', '2019-03-06 10:14:43');
INSERT INTO `wf_run_cache` VALUES ('9', '19', '1', '1', '', '{\"id\":1,\"uid\":13,\"add_time\":1551777600,\"new_title\":\"\\u963f\\u65af\\u987f\\u53d1\",\"new_type\":2,\"new_top\":0,\"new_con\":\"\\u963f\\u65af\\u987f\\u53d1as\\u963f\\u65af\\u987f\\u53d1asd\\t\\t\\u963f\\u65af\\u987f\\u53d1\\r\\n\\t\\u963f\\u65af\\u987f\\u53d1\\u65af\\u8482\\u82ac\\u963f\\u65af\\u987f\\u53d1\\t\\u53d1\\u65af\\u8482\\u82acasd\\t\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\u53d1\\u9001\\u5230asd\\u963f\\u65af\\u987f\\u53d1\\u9001\\u5230\\r\\n\\t\\t\\tASD\\u963f\\u65af\\u987f\",\"new_user\":null,\"status\":0,\"uptime\":1551838419,\"created_at\":null,\"updated_at\":null}', '{\"id\":5,\"flow_id\":4,\"process_name\":\"\\u5ba1\\u6838\",\"process_type\":\"is_one\",\"process_to\":\"6\",\"child_id\":0,\"child_relation\":null,\"child_after\":0,\"child_back_process\":0,\"return_sponsor_ids\":null,\"return_respon_ids\":null,\"write_fields\":null,\"secret_fields\":null,\"lock_fields\":null,\"check_fields\":null,\"auto_person\":4,\"auto_unlock\":0,\"auto_sponsor_ids\":\"7\",\"auto_sponsor_text\":\"\\u5e02\\u573a\\u90e8\\u5458\\u5de51\",\"auto_respon_ids\":\"\",\"auto_respon_text\":\"\",\"auto_role_ids\":\"\",\"auto_role_text\":\"\",\"auto_process_sponsor\":0,\"range_user_ids\":\"\",\"range_user_text\":\"\",\"range_dept_ids\":null,\"range_dept_text\":null,\"range_role_ids\":null,\"range_role_text\":null,\"receive_type\":0,\"is_user_end\":0,\"is_userop_pass\":0,\"is_sing\":1,\"sign_look\":0,\"is_back\":1,\"out_condition\":\"[]\",\"setleft\":100,\"settop\":100,\"style\":\"{\\\"width\\\":\\\"120\\\",\\\"height\\\":\\\"38\\\",\\\"color\\\":\\\"#0e76a8\\\"}\",\"is_del\":0,\"updatetime\":1551776977,\"dateline\":0,\"wf_mode\":0,\"wf_action\":\"view\",\"created_at\":\"2019-03-05 16:47:07\",\"updated_at\":\"2019-03-05 17:09:37\"}', '0', '0', '1551838537', '2019-03-06 10:15:37', '2019-03-06 10:15:37');

-- ----------------------------
-- Table structure for wf_run_log
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_log`;
CREATE TABLE `wf_run_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `from_id` int(11) DEFAULT NULL,
  `from_table` varchar(255) DEFAULT NULL,
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID,子流程时区分run step',
  `content` text NOT NULL COMMENT '日志内容',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `btn` varchar(255) DEFAULT NULL,
  `art` longtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run_log
-- ----------------------------
INSERT INTO `wf_run_log` VALUES ('1', '13', '1', 'news', '15', '0', '设分公司的', '1551781355', 'Send', '', '2019-03-05 18:22:35', '2019-03-05 18:22:35');
INSERT INTO `wf_run_log` VALUES ('2', '13', '1', 'news', '1', '0', '编号：13的超级管理员终止了本流程！', '1551783302', 'SupEnd', '', '2019-03-05 18:55:02', '2019-03-05 18:55:02');
INSERT INTO `wf_run_log` VALUES ('3', '13', '1', 'news', '2', '0', '编号：13的超级管理员终止了本流程！', '1551783310', 'SupEnd', '', '2019-03-05 18:55:10', '2019-03-05 18:55:10');
INSERT INTO `wf_run_log` VALUES ('4', '13', '1', 'news', '3', '0', '编号：13的超级管理员终止了本流程！', '1551783313', 'SupEnd', '', '2019-03-05 18:55:13', '2019-03-05 18:55:13');
INSERT INTO `wf_run_log` VALUES ('5', '13', '1', 'news', '2', '0', '编号：13的超级管理员终止了本流程！', '1551783315', 'SupEnd', '', '2019-03-05 18:55:15', '2019-03-05 18:55:15');
INSERT INTO `wf_run_log` VALUES ('6', '13', '1', 'news', '4', '0', '编号：13的超级管理员终止了本流程！', '1551783317', 'SupEnd', '', '2019-03-05 18:55:17', '2019-03-05 18:55:17');
INSERT INTO `wf_run_log` VALUES ('7', '13', '1', 'news', '15', '0', '[管理员代办]测试', '1551783793', 'ok', '', '2019-03-05 19:03:13', '2019-03-05 19:03:13');
INSERT INTO `wf_run_log` VALUES ('8', '13', '1', 'news', '6', '0', '编号：13的超级管理员终止了本流程！', '1551835088', 'SupEnd', '', '2019-03-06 09:18:08', '2019-03-06 09:18:08');
INSERT INTO `wf_run_log` VALUES ('9', '13', '1', 'news', '15', '0', '[管理员代办]阿斯顿发送到', '1551835605', 'Back', '', '2019-03-06 09:26:45', '2019-03-06 09:26:45');
INSERT INTO `wf_run_log` VALUES ('10', '13', '1', 'news', '14', '0', '[管理员代办]请审核', '1551836816', 'Sing', '', '2019-03-06 09:46:56', '2019-03-06 09:46:56');
INSERT INTO `wf_run_log` VALUES ('11', '7', '2', 'news', '16', '0', '发斯蒂芬', '1551837245', 'Send', '', '2019-03-06 09:54:05', '2019-03-06 09:54:05');
INSERT INTO `wf_run_log` VALUES ('12', '7', '2', 'news', '16', '0', '[管理员代办]同意', '1551837420', 'ok', '', '2019-03-06 09:57:00', '2019-03-06 09:57:00');
INSERT INTO `wf_run_log` VALUES ('13', '7', '2', 'news', '16', '0', '[管理员代办]同意发布', '1551837443', 'ok', '', '2019-03-06 09:57:23', '2019-03-06 09:57:23');
INSERT INTO `wf_run_log` VALUES ('14', '7', '2', 'news', '16', '0', '[管理员代办]同意发布', '1551837471', 'ok', '', '2019-03-06 09:57:51', '2019-03-06 09:57:51');
INSERT INTO `wf_run_log` VALUES ('15', '13', '1', 'news', '17', '0', '发斯蒂芬', '1551838341', 'Send', '', '2019-03-06 10:12:21', '2019-03-06 10:12:21');
INSERT INTO `wf_run_log` VALUES ('16', '10', '1', 'news', '17', '0', '[管理员代办]重新', '1551838419', 'Back', '', '2019-03-06 10:13:39', '2019-03-06 10:13:39');
INSERT INTO `wf_run_log` VALUES ('17', '10', '3', 'news', '18', '0', '阿斯顿发', '1551838483', 'Send', '', '2019-03-06 10:14:43', '2019-03-06 10:14:43');
INSERT INTO `wf_run_log` VALUES ('18', '7', '3', 'news', '18', '0', '同意审核', '1551838504', 'ok', '', '2019-03-06 10:15:04', '2019-03-06 10:15:04');
INSERT INTO `wf_run_log` VALUES ('19', '13', '3', 'news', '18', '0', '同意', '1551838523', 'ok', '', '2019-03-06 10:15:23', '2019-03-06 10:15:23');
INSERT INTO `wf_run_log` VALUES ('20', '13', '1', 'news', '19', '0', '阿斯顿发', '1551838537', 'Send', '', '2019-03-06 10:15:37', '2019-03-06 10:15:37');
INSERT INTO `wf_run_log` VALUES ('21', '7', '1', 'news', '19', '0', '发布', '1551838553', 'ok', '', '2019-03-06 10:15:53', '2019-03-06 10:15:53');
INSERT INTO `wf_run_log` VALUES ('22', '13', '1', 'news', '19', '0', '阿斯顿发', '1551838565', 'ok', '', '2019-03-06 10:16:05', '2019-03-06 10:16:05');
INSERT INTO `wf_run_log` VALUES ('23', '13', '1', 'news', '7', '0', '编号：13的超级管理员终止了本流程！', '1551838572', 'SupEnd', '', '2019-03-06 10:16:12', '2019-03-06 10:16:12');
INSERT INTO `wf_run_log` VALUES ('24', '13', '1', 'news', '7', '0', '编号：13的超级管理员终止了本流程！', '1551838574', 'SupEnd', '', '2019-03-06 10:16:14', '2019-03-06 10:16:14');
INSERT INTO `wf_run_log` VALUES ('25', '13', '1', 'news', '8', '0', '编号：13的超级管理员终止了本流程！', '1551838579', 'SupEnd', '', '2019-03-06 10:16:19', '2019-03-06 10:16:19');
INSERT INTO `wf_run_log` VALUES ('26', '13', '1', 'news', '9', '0', '编号：13的超级管理员终止了本流程！', '1551838581', 'SupEnd', '', '2019-03-06 10:16:21', '2019-03-06 10:16:21');
INSERT INTO `wf_run_log` VALUES ('27', '13', '1', 'news', '10', '0', '编号：13的超级管理员终止了本流程！', '1551838582', 'SupEnd', '', '2019-03-06 10:16:22', '2019-03-06 10:16:22');
INSERT INTO `wf_run_log` VALUES ('28', '13', '1', 'news', '11', '0', '编号：13的超级管理员终止了本流程！', '1551838584', 'SupEnd', '', '2019-03-06 10:16:24', '2019-03-06 10:16:24');
INSERT INTO `wf_run_log` VALUES ('29', '13', '1', 'news', '12', '0', '编号：13的超级管理员终止了本流程！', '1551838586', 'SupEnd', '', '2019-03-06 10:16:26', '2019-03-06 10:16:26');
INSERT INTO `wf_run_log` VALUES ('30', '13', '1', 'news', '13', '0', '编号：13的超级管理员终止了本流程！', '1551838588', 'SupEnd', '', '2019-03-06 10:16:28', '2019-03-06 10:16:28');
INSERT INTO `wf_run_log` VALUES ('31', '13', '1', 'news', '14', '0', '编号：13的超级管理员终止了本流程！', '1551838589', 'SupEnd', '', '2019-03-06 10:16:29', '2019-03-06 10:16:29');

-- ----------------------------
-- Table structure for wf_run_process
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_process`;
CREATE TABLE `wf_run_process` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前流转id',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '属于那个流程的id',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `parent_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上一步流程',
  `parent_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '上一步骤号',
  `run_child` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始转入子流程run_id 如果转入子流程，则在这里也记录',
  `remark` text COMMENT '备注',
  `is_receive_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否先接收人为主办人',
  `auto_person` tinyint(4) DEFAULT NULL,
  `sponsor_text` varchar(255) DEFAULT NULL,
  `sponsor_ids` varchar(255) DEFAULT NULL,
  `is_sponsor` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否步骤主办人 0否(默认) 1是',
  `is_singpost` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已会签过',
  `is_back` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '被退回的 0否(默认) 1是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0为未接收（默认），1为办理中 ,2为已转交,3为已结束4为已打回',
  `js_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接收时间',
  `bl_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '办理时间',
  `jj_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '转交时间,最后一步等同办结时间',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `wf_mode` int(11) DEFAULT NULL,
  `wf_action` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`),
  KEY `status` (`status`),
  KEY `is_del` (`is_del`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run_process
-- ----------------------------
INSERT INTO `wf_run_process` VALUES ('1', '13', '9', '4', '5', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1551781179', '0', '0', '0', '0', '1551781179', '0', 'view', '2019-03-05 18:19:39', '2019-03-05 18:19:39');
INSERT INTO `wf_run_process` VALUES ('2', '13', '10', '4', '5', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1551781206', '0', '0', '0', '0', '1551781206', '0', 'view', '2019-03-05 18:20:06', '2019-03-05 18:20:06');
INSERT INTO `wf_run_process` VALUES ('3', '13', '11', '4', '5', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1551781220', '0', '0', '0', '0', '1551781220', '0', 'view', '2019-03-05 18:20:20', '2019-03-05 18:20:20');
INSERT INTO `wf_run_process` VALUES ('4', '13', '12', '4', '5', '0', '0', '0', '', '0', '4', '市场部员工1', '7', '0', '0', '0', '0', '1551781244', '0', '0', '0', '0', '1551781244', '0', 'view', '2019-03-05 18:20:44', '2019-03-05 18:20:44');
INSERT INTO `wf_run_process` VALUES ('5', '13', '13', '4', '5', '0', '0', '0', '编号：13的超级管理员终止了本流程！', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1551781316', '1551838589', '0', '0', '0', '1551781316', '0', 'view', '2019-03-05 18:21:56', '2019-03-06 10:16:29');
INSERT INTO `wf_run_process` VALUES ('6', '13', '14', '4', '5', '0', '0', '0', '[管理员代办]请审核', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1551781340', '1551836816', '0', '0', '0', '1551781340', '0', 'view', '2019-03-05 18:22:20', '2019-03-06 09:46:56');
INSERT INTO `wf_run_process` VALUES ('7', '13', '15', '4', '5', '0', '0', '0', '[管理员代办]测试', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1551781355', '1551783793', '0', '0', '0', '1551781355', '0', 'view', '2019-03-05 18:22:35', '2019-03-05 19:03:13');
INSERT INTO `wf_run_process` VALUES ('8', '13', '15', '4', '6', '0', '0', '0', '[管理员代办]阿斯顿发送到', '0', '4', '总经理', '13', '0', '0', '0', '2', '1551783765', '1551835605', '0', '0', '0', '1551783765', '0', 'view', '2019-03-05 19:02:45', '2019-03-06 09:26:45');
INSERT INTO `wf_run_process` VALUES ('9', '13', '15', '4', '6', '0', '0', '0', '', '0', '4', '总经理', '13', '0', '0', '0', '0', '1551783793', '0', '0', '0', '0', '1551783793', '0', 'view', '2019-03-05 19:03:13', '2019-03-05 19:03:13');
INSERT INTO `wf_run_process` VALUES ('10', '7', '16', '4', '5', '0', '0', '0', '[管理员代办]同意', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1551837245', '1551837420', '0', '0', '0', '1551837245', '0', 'view', '2019-03-06 09:54:05', '2019-03-06 09:57:00');
INSERT INTO `wf_run_process` VALUES ('11', '7', '16', '4', '6', '0', '0', '0', '[管理员代办]同意发布', '0', '4', '总经理', '13', '0', '0', '0', '2', '1551837420', '1551837471', '0', '0', '0', '1551837420', '0', 'view', '2019-03-06 09:57:00', '2019-03-06 09:57:51');
INSERT INTO `wf_run_process` VALUES ('12', '13', '17', '4', '5', '0', '0', '0', '[管理员代办]重新', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1551838341', '1551838419', '0', '0', '0', '1551838341', '0', 'view', '2019-03-06 10:12:21', '2019-03-06 10:13:39');
INSERT INTO `wf_run_process` VALUES ('13', '10', '18', '4', '5', '0', '0', '0', '同意审核', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1551838483', '1551838504', '0', '0', '0', '1551838483', '0', 'view', '2019-03-06 10:14:43', '2019-03-06 10:15:04');
INSERT INTO `wf_run_process` VALUES ('14', '7', '18', '4', '6', '0', '0', '0', '同意', '0', '4', '总经理', '13', '0', '0', '0', '2', '1551838504', '1551838523', '0', '0', '0', '1551838504', '0', 'view', '2019-03-06 10:15:04', '2019-03-06 10:15:23');
INSERT INTO `wf_run_process` VALUES ('15', '13', '19', '4', '5', '0', '0', '0', '发布', '0', '4', '市场部员工1', '7', '0', '0', '0', '2', '1551838537', '1551838553', '0', '0', '0', '1551838537', '0', 'view', '2019-03-06 10:15:37', '2019-03-06 10:15:53');
INSERT INTO `wf_run_process` VALUES ('16', '7', '19', '4', '6', '0', '0', '0', '阿斯顿发', '0', '4', '总经理', '13', '0', '0', '0', '2', '1551838553', '1551838565', '0', '0', '0', '1551838553', '0', 'view', '2019-03-06 10:15:53', '2019-03-06 10:16:05');

-- ----------------------------
-- Table structure for wf_run_sign
-- ----------------------------
DROP TABLE IF EXISTS `wf_run_sign`;
CREATE TABLE `wf_run_sign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `run_id` int(10) unsigned NOT NULL DEFAULT '0',
  `run_flow` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流程ID,子流程时区分run step',
  `run_flow_process` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当前步骤编号',
  `content` text NOT NULL COMMENT '会签内容',
  `is_agree` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核意见：1同意；2不同意',
  `sign_att_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sign_look` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '步骤设置的会签可见性,0总是可见（默认）,1本步骤经办人之间不可见2针对其他步骤不可见',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `run_id` (`run_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wf_run_sign
-- ----------------------------
INSERT INTO `wf_run_sign` VALUES ('1', '10', '14', '4', '6', '', '0', '0', '0', '1551836801', '2019-03-06 09:46:41', '2019-03-06 09:46:41');
INSERT INTO `wf_run_sign` VALUES ('2', '10', '14', '4', '6', '', '0', '0', '0', '1551836816', '2019-03-06 09:46:56', '2019-03-06 09:46:56');

-- ----------------------------
-- Table structure for wf_user
-- ----------------------------
DROP TABLE IF EXISTS `wf_user`;
CREATE TABLE `wf_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `role` smallint(6) unsigned NOT NULL COMMENT '组ID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 1:启用 0:禁止',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `last_login_time` int(11) unsigned NOT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(15) DEFAULT NULL COMMENT '最后登录IP',
  `login_count` int(11) DEFAULT '0',
  `last_location` varchar(100) DEFAULT NULL COMMENT '最后登录位置',
  `add_time` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of wf_user
-- ----------------------------
INSERT INTO `wf_user` VALUES ('7', '市场部员工1', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '15', '0', '1', '1522372036', '127.0.0.1', '0', '新建用户', '1522372036', null, null);
INSERT INTO `wf_user` VALUES ('8', '工程部员工1', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '15', '0', '1', '1522372556', '127.0.0.1', '0', '新建用户', '1522372556', null, null);
INSERT INTO `wf_user` VALUES ('9', '新闻部员工1', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '17', '0', '1', '1522376353', '127.0.0.1', '0', '新建用户', '1522376353', null, null);
INSERT INTO `wf_user` VALUES ('10', '新闻部经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '18', '0', '1', '1522376372', '127.0.0.1', '0', '新建用户', '1522376372', null, null);
INSERT INTO `wf_user` VALUES ('11', '市场部经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '20', '0', '1', '1522376385', '127.0.0.1', '0', '新建用户', '1522376385', null, null);
INSERT INTO `wf_user` VALUES ('12', '工程部经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '19', '0', '1', '1522376401', '127.0.0.1', '0', '新建用户', '1522376401', null, null);
INSERT INTO `wf_user` VALUES ('13', '总经理', 'c4ca4238a0b923820dcc509a6f75849b', '1', '1', '21', '0', '1', '1522376413', '127.0.0.1', '0', '新建用户', '1522376413', null, null);
