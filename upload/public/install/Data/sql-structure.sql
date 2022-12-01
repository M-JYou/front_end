DROP TABLE IF EXISTS `qs_ad`;
CREATE TABLE `qs_ad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_display` TINYINT(1) NOT NULL DEFAULT 1,
  `cid` INT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `imageid` INT UNSIGNED NOT NULL,
  `imageurl` VARCHAR(255) NOT NULL,
  `explain` VARCHAR(255) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `starttime` INT UNSIGNED NOT NULL,
  `deadline` INT NOT NULL DEFAULT 0,
  `target` TINYINT(1) UNSIGNED NOT NULL,
  `link_url` VARCHAR(255) NOT NULL,
  `inner_link` VARCHAR(30) NOT NULL,
  `inner_link_params` INT NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_ad||-_-|| */

DROP TABLE IF EXISTS `qs_ad_category`;
CREATE TABLE `qs_ad_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `width` INT NOT NULL,
  `height` INT NOT NULL,
  `ad_num` INT UNSIGNED NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `platform` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_ad_category||-_-|| */

DROP TABLE IF EXISTS `qs_admin`;
CREATE TABLE `qs_admin` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(15) NOT NULL,
  `password` VARCHAR(32) NOT NULL,
  `pwd_hash` VARCHAR(10) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `last_login_time` INT UNSIGNED NOT NULL,
  `last_login_ip` VARCHAR(30) NOT NULL,
  `last_login_ipaddress` VARCHAR(30) NOT NULL,
  `openid` VARCHAR(50) NOT NULL DEFAULT '',
  `is_sc` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '是否是销售',
  `qy_userid` VARCHAR(50) NOT NULL DEFAULT '',
  `qy_openid` VARCHAR(50) NOT NULL DEFAULT '',
  `bind_qywx` TINYINT(1) NOT NULL DEFAULT 0,
  `bind_qywx_time` INT NOT NULL DEFAULT 0,
  `mobile` CHAR(11) NOT NULL DEFAULT '',
  `avatar` VARCHAR(200) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_admin||-_-|| */

DROP TABLE IF EXISTS `qs_admin_log`;
CREATE TABLE `qs_admin_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED NOT NULL,
  `admin_name` VARCHAR(30) NOT NULL,
  `content` TEXT NOT NULL,
  `is_login` TINYINT(1) UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `ip` VARCHAR(30) NOT NULL,
  `ip_addr` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `index_fulltext_index` (`content`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_admin_log||-_-|| */

DROP TABLE IF EXISTS `qs_admin_role`;
CREATE TABLE `qs_admin_role` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(15) NOT NULL,
  `access` TEXT NOT NULL,
  `access_mobile` TEXT NOT NULL,
  `access_export` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `access_delete` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `access_set_service` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_admin_role||-_-|| */

DROP TABLE IF EXISTS `qs_ali_axb`;
CREATE TABLE `qs_ali_axb` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `a` VARCHAR(15) NOT NULL,
  `b` VARCHAR(15) NOT NULL,
  `x` VARCHAR(15) NOT NULL DEFAULT '',
  `sub_id` VARCHAR(20) NOT NULL DEFAULT '',
  `addtime` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_ali_axb||-_-|| */

DROP TABLE IF EXISTS `qs_article`;
CREATE TABLE `qs_article` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `cid` INT UNSIGNED NOT NULL COMMENT '分类id',
  `create` INT UNSIGNED NOT NULL COMMENT '创建者id',
  `price` INT NOT NULL DEFAULT 0 COMMENT '价格',
  `category_district` INT UNSIGNED DEFAULT 0 COMMENT '归属地',
  `title` VARCHAR(100) NOT NULL '标题',
  `content` LONGTEXT NOT NULL '正文',
  `attach` TEXT NOT NULL COMMENT '文件[{name:"",url:""}]',
  `other` TEXT NOT NULL COMMENT '其他json',
  `thumb` CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
  `link_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '转载的url',
  `seo_keywords` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'seo关键字',
  `seo_description` VARCHAR(200) NOT NULL DEFAULT '' COMMENT 'seo说明',
  `click` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览次数',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `source` TINYINT(1) UNSIGNED NOT NULL COMMENT '是否转载',
  `fno` CHAR(255) NOT NULL DEFAULT '' COMMENT '文号',
  `exetime` CHAR(255) NOT NULL DEFAULT '' COMMENT '执行时间',

  PRIMARY KEY (`id`),
  KEY `index_click` (`click`),
  KEY `index_addtime` (`addtime`),
  KEY `index_cid_sort_id` (`cid`,`sort_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_article||-_-|| */

DROP TABLE IF EXISTS `qs_article_category`;
CREATE TABLE `qs_article_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `seo_description` VARCHAR(200) NOT NULL DEFAULT '',
  `seo_keywords` VARCHAR(100) NOT NULL DEFAULT '',
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_article_category||-_-|| */

DROP TABLE IF EXISTS `qs_attention_company`;
CREATE TABLE `qs_attention_company` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_uid` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `comid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_company_uid` (`company_uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_attention_company||-_-|| */

DROP TABLE IF EXISTS `qs_category`;
CREATE TABLE `qs_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `name` VARCHAR(30) NOT NULL,
  `sort_id` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `index_c_alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_category||-_-|| */

DROP TABLE IF EXISTS `qs_category_district`;
CREATE TABLE `qs_category_district` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` INT UNSIGNED NOT NULL COMMENT '父级id',
  `name` VARCHAR(64) NOT NULL COMMENT '名称',
  `spell` VARCHAR(128) NOT NULL COMMENT '拼音',
  `alias` VARCHAR(32) NOT NULL COMMENT '拼音首字母',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `level` TINYINT(1) UNSIGNED NOT NULL COMMENT '等级',
  `img` CHAR(255) DEFAULT '' COMMENT '城市封面',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_category_district||-_-|| */

DROP TABLE IF EXISTS `qs_category_group`;
CREATE TABLE `qs_category_group` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `name` VARCHAR(30) NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_category_group||-_-|| */

DROP TABLE IF EXISTS `qs_category_job`;
CREATE TABLE `qs_category_job` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT UNSIGNED NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `content` TEXT NOT NULL,
  `spell` VARCHAR(200) NOT NULL,
  `level` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_parentid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_category_job||-_-|| */

DROP TABLE IF EXISTS `qs_category_major`;
CREATE TABLE `qs_category_major` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `level` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_category_major||-_-|| */

DROP TABLE IF EXISTS `qs_company`;
CREATE TABLE `qs_company` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` INT UNSIGNED NOT NULL COMMENT '创建者id',
  `companyname` VARCHAR(60) NOT NULL COMMENT '企业名称',
  `short_name` VARCHAR(60) NOT NULL COMMENT '企业简称',
  `nature` INT UNSIGNED NOT NULL COMMENT '行业类型id;表Category alias=QS_company_type',
  `trade` INT UNSIGNED NOT NULL COMMENT '行业类型id;表Category alias=QS_trade',
  `district1` INT UNSIGNED NOT NULL COMMENT '地址1级id;表CategoryDistrict',
  `district2` INT UNSIGNED NOT NULL COMMENT '地址2级id;表CategoryDistrict',
  `district3` INT UNSIGNED NOT NULL COMMENT '地址3级id;表CategoryDistrict',
  `district` INT UNSIGNED NOT NULL COMMENT '地址id;表CategoryDistrict',
  `scale` INT UNSIGNED NOT NULL COMMENT '企业规模;表Category alias=QS_scale',
  `registered` VARCHAR(15) NOT NULL COMMENT '注册资金',
  `currency` TINYINT(1) UNSIGNED NOT NULL COMMENT '资金单位(0:万人民币,1:万美元)',
  `tag` VARCHAR(100) NOT NULL COMMENT '企业福利;表Category alias=QS_jobtag',
  `map_lat` decimal(9 COMMENT '',6) NOT NULL COMMENT '纬度',
  `map_lng` decimal(9 COMMENT '',6) NOT NULL COMMENT '经度',
  `map_zoom` TINYINT(3) UNSIGNED NOT NULL COMMENT '缩放等级',
  `audit` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0未审核,1审核通过',
  `logo` INT UNSIGNED NOT NULL COMMENT '企业logo',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `refreshtime` INT UNSIGNED NOT NULL COMMENT '刷新时间',
  `updatetime` INT UNSIGNED NOT NULL COMMENT '更新时间',
  `click` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT '点击量',
  `robot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否机器人',
  `user_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '用户状态',
  `cs_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '',
  `platform` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '发布来源',
  `setmeal_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '职聊套餐id;MemberSetmeal',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company||-_-|| */

DROP TABLE IF EXISTS `qs_company_auth`;
CREATE TABLE `qs_company_auth` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `legal_person_idcard_front` INT UNSIGNED NOT NULL,
  `legal_person_idcard_back` INT UNSIGNED NOT NULL,
  `license` INT UNSIGNED NOT NULL,
  `proxy` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_comid_uid` (`comid`,`uid`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_auth||-_-|| */

DROP TABLE IF EXISTS `qs_company_auth_log`;
CREATE TABLE `qs_company_auth_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `reason` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_comid_uid` (`comid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_auth_log||-_-|| */

DROP TABLE IF EXISTS `qs_company_contact`;
CREATE TABLE `qs_company_contact` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comid` INT UNSIGNED NOT NULL COMMENT '企业id',
  `uid` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `contact` VARCHAR(30) NOT NULL COMMENT '联系人',
  `mobile` VARCHAR(11) NOT NULL COMMENT '电话',
  `weixin` VARCHAR(15) NOT NULL COMMENT '微信',
  `telephone` VARCHAR(20) NOT NULL COMMENT '座机',
  `qq` VARCHAR(15) NOT NULL COMMENT 'QQ号',
  `email` VARCHAR(30) NOT NULL COMMENT '邮箱',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_comid_uid` (`comid`,`uid`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_contact||-_-|| */

DROP TABLE IF EXISTS `qs_company_down_resume`;
CREATE TABLE `qs_company_down_resume` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `comid` INT UNSIGNED NOT NULL,
  `resume_id` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `platform` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_down_resume||-_-|| */

DROP TABLE IF EXISTS `qs_company_img`;
CREATE TABLE `qs_company_img` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `comid` INT UNSIGNED NOT NULL,
  `img` INT NOT NULL,
  `title` VARCHAR(20) CHARACTER SET utf8 NOT NULL,
  `addtime` INT(100) UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`),
  KEY `index_comid_uid` (`comid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_img||-_-|| */

DROP TABLE IF EXISTS `qs_company_info`;
CREATE TABLE `qs_company_info` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `website` VARCHAR(200) NOT NULL,
  `short_desc` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_comid_uid` (`comid`,`uid`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_info||-_-|| */

DROP TABLE IF EXISTS `qs_company_interview`;
CREATE TABLE `qs_company_interview` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comid` INT UNSIGNED NOT NULL,
  `companyname` VARCHAR(100) NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `jobname` VARCHAR(30) NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `resume_id` INT UNSIGNED NOT NULL,
  `fullname` VARCHAR(30) NOT NULL,
  `interview_time` INT UNSIGNED NOT NULL,
  `contact` VARCHAR(30) NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  `tel` VARCHAR(20) NOT NULL,
  `note` VARCHAR(100) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `is_look` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_interview||-_-|| */

DROP TABLE IF EXISTS `qs_company_interview_video`;
CREATE TABLE `qs_company_interview_video` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comid` INT UNSIGNED NOT NULL,
  `companyname` VARCHAR(100) NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `jobname` VARCHAR(30) NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `resume_id` INT UNSIGNED NOT NULL,
  `fullname` VARCHAR(30) NOT NULL,
  `interview_time` INT UNSIGNED NOT NULL,
  `contact` VARCHAR(30) NOT NULL,
  `tel` VARCHAR(20) NOT NULL,
  `deadline` INT UNSIGNED NOT NULL,
  `note` VARCHAR(100) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `is_look` TINYINT(1) UNSIGNED NOT NULL,
  `company_donotice_time` INT UNSIGNED NOT NULL DEFAULT 0,
  `personal_donotice_time` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_interview_video||-_-|| */

DROP TABLE IF EXISTS `qs_company_report`;
CREATE TABLE `qs_company_report` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '会员id',
  `company_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '公司id',
  `corporate` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '企业法人',
  `com_type` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '主体类型',
  `reg_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创立时间',
  `reg_capital` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册资金',
  `reg_address` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '注册地址',
  `office_address` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '办公地址',
  `registrar` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '登记机关',
  `scope` TEXT NOT NULL COMMENT '经营范围',
  `office_area` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '办公面积',
  `office_env` TINYINT(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '办公环境（1一般2良好3优美）',
  `workplace` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '办公场所',
  `number` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '员工人数',
  `sex_ratio` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '男女比例',
  `average_age` INT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '平均年龄',
  `route` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '乘车路线',
  `img` TEXT COMMENT '企业照片',
  `evaluation` TEXT NOT NULL COMMENT '评价',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '认证时间',
  `certifier` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '认证师',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_report||-_-|| */

DROP TABLE IF EXISTS `qs_company_service_emergency`;
CREATE TABLE `qs_company_service_emergency` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `days` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL,
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_service_emergency||-_-|| */

DROP TABLE IF EXISTS `qs_company_service_points`;
CREATE TABLE `qs_company_service_points` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `points` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_service_points||-_-|| */

DROP TABLE IF EXISTS `qs_company_service_refresh_job_package`;
CREATE TABLE `qs_company_service_refresh_job_package` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `times` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL,
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_service_refresh_job_package||-_-|| */

DROP TABLE IF EXISTS `qs_company_service_im`;
CREATE TABLE `qs_company_service_im` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` VARCHAR(30) NOT NULL COMMENT '名称',
  `recommend` TINYINT(1) UNSIGNED NOT NULL COMMENT '推荐1是2否',
  `times` INT UNSIGNED NOT NULL COMMENT '职聊次数',
  `expense` decimal(10,2) UNSIGNED NOT NULL COMMENT '价格',
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL COMMENT '可积分抵扣0否1可2部',
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL COMMENT '可部分抵扣最大额',
  `is_display` TINYINT(1) UNSIGNED NOT NULL COMMENT '是否显示1是2否',
  `sort_id` INT UNSIGNED NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_service_im||-_-|| */

DROP TABLE IF EXISTS `qs_company_service_resume_package`;
CREATE TABLE `qs_company_service_resume_package` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `download_resume_point` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL,
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_service_resume_package||-_-|| */

DROP TABLE IF EXISTS `qs_company_service_stick`;
CREATE TABLE `qs_company_service_stick` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `days` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL,
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_company_service_stick||-_-|| */

DROP TABLE IF EXISTS `qs_config`;
CREATE TABLE `qs_config` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `is_frontend` TINYINT(1) UNSIGNED NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `note` VARCHAR(100) NOT NULL,
  `is_secret` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_config||-_-|| */

DROP TABLE IF EXISTS `qs_coupon`;
CREATE TABLE `qs_coupon` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `face_value` double(10,2) UNSIGNED NOT NULL,
  `bind_setmeal_id` INT UNSIGNED NOT NULL,
  `days` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_coupon||-_-|| */

DROP TABLE IF EXISTS `qs_coupon_log`;
CREATE TABLE `qs_coupon_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `coupon_id` TEXT NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `admin_name` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_coupon_log||-_-|| */

DROP TABLE IF EXISTS `qs_coupon_record`;
CREATE TABLE `qs_coupon_record` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_id` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `coupon_name` VARCHAR(30) NOT NULL,
  `coupon_face_value` double(10,2) UNSIGNED NOT NULL,
  `coupon_bind_setmeal_id` INT UNSIGNED NOT NULL,
  `deadline` INT UNSIGNED NOT NULL,
  `usetime` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_log_id` (`log_id`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_coupon_record||-_-|| */

DROP TABLE IF EXISTS `qs_cron`;
CREATE TABLE `qs_cron` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `action` VARCHAR(30) NOT NULL,
  `weekday` TINYINT(1) NOT NULL,
  `day` TINYINT(2) NOT NULL,
  `hour` TINYINT(2) NOT NULL,
  `minute` VARCHAR(10) NOT NULL,
  `next_execute_time` INT UNSIGNED NOT NULL,
  `last_execute_time` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL,
  `disable_edit` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cron||-_-|| */

DROP TABLE IF EXISTS `qs_cron_log`;
CREATE TABLE `qs_cron_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cron_id` INT UNSIGNED NOT NULL,
  `cron_name` VARCHAR(30) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `seconds` double(10,4) UNSIGNED NOT NULL,
  `is_auto` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cron_log||-_-|| */

DROP TABLE IF EXISTS `qs_customer_service`;
CREATE TABLE `qs_customer_service` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `photo` INT UNSIGNED NOT NULL,
  `mobile` VARCHAR(11) NOT NULL,
  `tel` VARCHAR(30) NOT NULL,
  `weixin` VARCHAR(30) NOT NULL,
  `qq` VARCHAR(20) NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  `wx_qrcode` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_customer_service||-_-|| */

DROP TABLE IF EXISTS `qs_customer_service_complaint`;
CREATE TABLE `qs_customer_service_complaint` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `cs_id` INT UNSIGNED NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_customer_service_complaint||-_-|| */

DROP TABLE IF EXISTS `qs_entrust`;
CREATE TABLE `qs_entrust` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `days` INT UNSIGNED NOT NULL,
  `deadline` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_entrust||-_-|| */

DROP TABLE IF EXISTS `qs_explain`;
CREATE TABLE `qs_explain` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `attach` TEXT NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `link_url` VARCHAR(200) NOT NULL DEFAULT '',
  `seo_keywords` VARCHAR(100) NOT NULL DEFAULT '',
  `seo_description` VARCHAR(200) NOT NULL DEFAULT '',
  `addtime` INT UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `index_addtime` (`addtime`),
  KEY `index_sort_id` (`sort_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_explain||-_-|| */

DROP TABLE IF EXISTS `qs_fav_job`;
CREATE TABLE `qs_fav_job` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_uid` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_company_uid` (`company_uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_fav_job||-_-|| */

DROP TABLE IF EXISTS `qs_fav_resume`;
CREATE TABLE `qs_fav_resume` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_uid` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `resume_id` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_company_uid` (`company_uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_fav_resume||-_-|| */

DROP TABLE IF EXISTS `qs_feedback`;
CREATE TABLE `qs_feedback` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` TINYINT(1) UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_feedback||-_-|| */

DROP TABLE IF EXISTS `qs_field_rule`;
CREATE TABLE `qs_field_rule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_name` VARCHAR(30) NOT NULL,
  `field_name` VARCHAR(30) NOT NULL,
  `is_require` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `enable_close` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `field_cn` VARCHAR(10) NOT NULL,
  `is_custom` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_field_rule||-_-|| */

DROP TABLE IF EXISTS `qs_help`;
CREATE TABLE `qs_help` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` INT UNSIGNED NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `seo_keywords` VARCHAR(100) NOT NULL DEFAULT '',
  `seo_description` VARCHAR(200) NOT NULL DEFAULT '',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `index_cid_sort_id` (`cid`,`sort_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_help||-_-|| */

DROP TABLE IF EXISTS `qs_help_category`;
CREATE TABLE `qs_help_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_help_category||-_-|| */

DROP TABLE IF EXISTS `qs_hotword`;
CREATE TABLE `qs_hotword` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `word` VARCHAR(120) NOT NULL,
  `hot` INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `index_word` (`word`),
  KEY `index_hot` (`hot`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_hotword||-_-|| */

DROP TABLE IF EXISTS `qs_hrtool`;
CREATE TABLE `qs_hrtool` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` INT UNSIGNED NOT NULL,
  `filename` VARCHAR(200) NOT NULL,
  `fileurl` VARCHAR(200) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_hrtool||-_-|| */

DROP TABLE IF EXISTS `qs_hrtool_category`;
CREATE TABLE `qs_hrtool_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(80) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `describe` VARCHAR(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_hrtool_category||-_-|| */

DROP TABLE IF EXISTS `qs_im_quickmsg`;
CREATE TABLE `qs_im_quickmsg` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` VARCHAR(100) NOT NULL,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_im_quickmsg||-_-|| */

DROP TABLE IF EXISTS `qs_im_token`;
CREATE TABLE `qs_im_token` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `im_userid` VARCHAR(50) NOT NULL,
  `token` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_im_token||-_-|| */

DROP TABLE IF EXISTS `qs_im_forbid`;
CREATE TABLE `qs_im_forbid` (
  `uid` INT NOT NULL COMMENT 'uid',
  `addtime` INT UNSIGNED NOT NULL COMMENT '最后登录时间',
  `reason` VARCHAR(30) NOT NULL COMMENT '最后登录ip'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_im_forbid||-_-|| */

DROP TABLE IF EXISTS `qs_im_rule`;
CREATE TABLE `qs_im_rule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `value` VARCHAR(100) NOT NULL,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `note` VARCHAR(30) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_im_rule||-_-|| */

DROP TABLE IF EXISTS `qs_job`;
CREATE TABLE `qs_job` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL COMMENT '用户id',
  `sort` INT COMMENT '排序',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业id;表Company id',
  `jobname` VARCHAR(50) NOT NULL COMMENT '岗位名称',
  `emergency` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '加急招聘id;表CompanyServiceEmergency',
  `stick` TINYINT(1) NOT NULL COMMENT '置顶服务id;表CompanyServiceStick',
  `nature` INT UNSIGNED NOT NULL COMMENT '职位类别;1全职,2兼职',
  `sex` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别;1男,2女',
  `category1` INT UNSIGNED NOT NULL COMMENT '职位1级id;表CategoryJob',
  `category2` INT UNSIGNED NOT NULL COMMENT '职位2级id;表CategoryJob',
  `category3` INT UNSIGNED NOT NULL COMMENT '职位3级id;表CategoryJob',
  `category` INT UNSIGNED NOT NULL COMMENT '职位id;表CategoryJob',
  `minwage` INT NOT NULL COMMENT '薪资最低值',
  `maxwage` INT NOT NULL COMMENT '薪资最高值',
  `negotiable` TINYINT(1) UNSIGNED NOT NULL COMMENT '薪资面议为1',
  `education` INT UNSIGNED NOT NULL COMMENT '学历;1=初中,2=高中,3=中技,4=中专,5=大专,6=本科,7=硕士,8=博士,9=博后,',
  `experience` INT UNSIGNED NOT NULL COMMENT '经验;1=应届生,2=1年,3=2年,4=3年,5=3-5年,6=5-10年,7=10年以上',
  `content` TEXT NOT NULL COMMENT '职位描述',
  `tag` VARCHAR(100) NOT NULL COMMENT '标签id 多个逗号连接;表Category alias=QS_jobtag',
  `amount` smallint(5) UNSIGNED NOT NULL COMMENT '招聘人数',
  `department` VARCHAR(15) NOT NULL COMMENT '部门',
  `minage` TINYINT(2) UNSIGNED NOT NULL COMMENT '最小年龄',
  `maxage` TINYINT(2) UNSIGNED NOT NULL COMMENT '最大年龄',
  `age_na` TINYINT(1) UNSIGNED NOT NULL COMMENT '不限年龄为1',
  `district1` INT UNSIGNED NOT NULL COMMENT '地址1级id;表CategoryDistrict',
  `district2` INT UNSIGNED NOT NULL COMMENT '地址2级id;表CategoryDistrict',
  `district3` INT UNSIGNED NOT NULL COMMENT '地址3级id;表CategoryDistrict',
  `district` INT UNSIGNED NOT NULL COMMENT '地址id;表CategoryDistrict',
  `address` VARCHAR(200) NOT NULL COMMENT '地址详情',
  `addtime` INT UNSIGNED NOT NULL COMMENT '添加时间',
  `refreshtime` INT UNSIGNED NOT NULL COMMENT '刷新时间',
  `updatetime` INT UNSIGNED NOT NULL COMMENT '更新时间',
  `setmeal_id` INT UNSIGNED NOT NULL COMMENT '职聊套餐id;MemberSetmeal',
  `audit` TINYINT(1) UNSIGNED NOT NULL COMMENT '状态:表Config name like audit_% 值id',
  `is_display` TINYINT(1) UNSIGNED NOT NULL COMMENT '是否显示',
  `click` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '点击次数',
  `user_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '用户状态',
  `robot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否机器人',
  `map_lat` decimal(9 COMMENT '',6) NOT NULL COMMENT '纬度',
  `map_lng` decimal(9 COMMENT '',6) NOT NULL COMMENT '经度',
  `map_zoom` TINYINT(3) UNSIGNED NOT NULL COMMENT '缩放等级',
  `custom_field_1` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '自定义字段1',
  `custom_field_2` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '自定义字段2',
  `custom_field_3` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '自定义字段3',
  `platform` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '发布来源',
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_job||-_-|| */

DROP TABLE IF EXISTS `qs_job_apply`;
CREATE TABLE `qs_job_apply` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `comid` INT UNSIGNED NOT NULL,
  `companyname` VARCHAR(100) NOT NULL,
  `company_uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `jobname` VARCHAR(30) NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `resume_id` INT UNSIGNED NOT NULL,
  `fullname` VARCHAR(30) NOT NULL,
  `note` VARCHAR(100) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `is_look` TINYINT(1) UNSIGNED NOT NULL,
  `handle_status` TINYINT(1) UNSIGNED NOT NULL,
  `source` TINYINT(1) UNSIGNED NOT NULL COMMENT '0自主投递 1委托投递',
  `platform` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `index_company_uid` (`company_uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_job_apply||-_-|| */

DROP TABLE IF EXISTS `qs_job_audit_log`;
CREATE TABLE `qs_job_audit_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jobid` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `reason` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_job_audit_log||-_-|| */

DROP TABLE IF EXISTS `qs_job_contact`;
CREATE TABLE `qs_job_contact` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `contact` VARCHAR(30) NOT NULL,
  `mobile` VARCHAR(11) NOT NULL,
  `weixin` VARCHAR(15) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL,
  `qq` VARCHAR(15) NOT NULL,
  `email` VARCHAR(30) NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `use_company_contact` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_jid_uid` (`jid`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_job_contact||-_-|| */

DROP TABLE IF EXISTS `qs_job_search_key`;
CREATE TABLE `qs_job_search_key` (
  `id` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `company_nature_id` INT UNSIGNED NOT NULL,
  `emergency` TINYINT(1) UNSIGNED NOT NULL,
  `license` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `stick` TINYINT(1) NOT NULL,
  `setmeal_id` INT UNSIGNED NOT NULL,
  `nature` INT UNSIGNED NOT NULL,
  `category1` INT UNSIGNED NOT NULL,
  `category2` INT UNSIGNED NOT NULL,
  `category3` INT UNSIGNED NOT NULL,
  `category` INT UNSIGNED NOT NULL,
  `trade` INT UNSIGNED NOT NULL,
  `scale` INT UNSIGNED NOT NULL,
  `district1` INT UNSIGNED NOT NULL,
  `district2` INT UNSIGNED NOT NULL,
  `district3` INT UNSIGNED NOT NULL,
  `district` INT UNSIGNED NOT NULL,
  `tag` VARCHAR(100) NOT NULL,
  `education` INT UNSIGNED NOT NULL,
  `experience` INT UNSIGNED NOT NULL,
  `minwage` INT NOT NULL,
  `maxwage` INT NOT NULL,
  `refreshtime` INT UNSIGNED NOT NULL,
  `map_lat` decimal(9,6) NOT NULL COMMENT '纬度',
  `map_lng` decimal(9,6) NOT NULL COMMENT '经度',
  `jobname` VARCHAR(50) NOT NULL,
  `companyname` VARCHAR(50) NOT NULL,
  `company_nature` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`),
  KEY `index_company_id` (`company_id`),
  FULLTEXT KEY `index_jobname` (`jobname`) /*!50100 WITH PARSER `ngram` */ ,
  FULLTEXT KEY `index_companyname` (`companyname`) /*!50100 WITH PARSER `ngram` */ ,
  FULLTEXT KEY `index_fulltext_index` (`jobname`,`companyname`,`company_nature`) /*!50100 WITH PARSER `ngram` */ ,
  FULLTEXT KEY `index_company_nature` (`company_nature`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_job_search_key||-_-|| */

DROP TABLE IF EXISTS `qs_job_search_rtime`;
CREATE TABLE `qs_job_search_rtime` (
  `id` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `company_nature_id` INT UNSIGNED NOT NULL,
  `emergency` TINYINT(1) UNSIGNED NOT NULL,
  `license` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `stick` TINYINT(1) NOT NULL,
  `setmeal_id` INT UNSIGNED NOT NULL,
  `nature` INT UNSIGNED NOT NULL,
  `category1` INT UNSIGNED NOT NULL,
  `category2` INT UNSIGNED NOT NULL,
  `category3` INT UNSIGNED NOT NULL,
  `category` INT UNSIGNED NOT NULL,
  `trade` INT UNSIGNED NOT NULL,
  `scale` INT UNSIGNED NOT NULL,
  `district1` INT UNSIGNED NOT NULL,
  `district2` INT UNSIGNED NOT NULL,
  `district3` INT UNSIGNED NOT NULL,
  `district` INT UNSIGNED NOT NULL,
  `tag` VARCHAR(100) NOT NULL,
  `education` INT UNSIGNED NOT NULL,
  `experience` INT UNSIGNED NOT NULL,
  `minwage` INT NOT NULL,
  `maxwage` INT NOT NULL,
  `refreshtime` INT UNSIGNED NOT NULL,
  `map_lat` decimal(9,6) NOT NULL COMMENT '纬度',
  `map_lng` decimal(9,6) NOT NULL COMMENT '经度',
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`),
  KEY `index_stick_rtime` (`stick`,`refreshtime`),
  KEY `index_rtime` (`refreshtime`),
  KEY `index_company_id` (`company_id`),
  KEY `index_emergency_rtime` (`emergency`,`refreshtime`),
  KEY `index_category1` (`category1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_job_search_rtime||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair`;
CREATE TABLE `qs_jobfair` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `title` VARCHAR(200) NOT NULL,
  `introduction` LONGTEXT NOT NULL,
  `address` VARCHAR(200) NOT NULL,
  `contact` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(100) NOT NULL,
  `bus` VARCHAR(200) NOT NULL,
  `sponsor` VARCHAR(255) NOT NULL COMMENT '主办方',
  `holddate_start` INT UNSIGNED NOT NULL,
  `holddate_end` INT UNSIGNED NOT NULL,
  `predetermined_start` INT NOT NULL,
  `predetermined_end` INT NOT NULL,
  `number` VARCHAR(200) NOT NULL,
  `addtime` INT NOT NULL,
  `ordid` INT UNSIGNED NOT NULL DEFAULT 0,
  `click` INT UNSIGNED NOT NULL DEFAULT 1,
  `participants_object` TEXT NOT NULL,
  `price` TEXT NOT NULL,
  `registration_method` TEXT NOT NULL,
  `thumb` INT NOT NULL,
  `intro_img` INT NOT NULL,
  `position_img` VARCHAR(255) NOT NULL,
  `map_lng` decimal(9,6) NOT NULL,
  `map_lat` decimal(9,6) NOT NULL,
  `map_zoom` TINYINT(3) UNSIGNED NOT NULL,
  `tpl_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_area`;
CREATE TABLE `qs_jobfair_area` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jobfair_id` INT UNSIGNED NOT NULL,
  `area` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair_area||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_exhibitors`;
CREATE TABLE `qs_jobfair_exhibitors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `audit` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `etype` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `uid` INT UNSIGNED NOT NULL DEFAULT 0,
  `companyname` VARCHAR(200) NOT NULL,
  `company_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `company_addtime` INT UNSIGNED NOT NULL DEFAULT 0,
  `eaddtime` INT UNSIGNED NOT NULL,
  `jobfair_id` INT UNSIGNED NOT NULL,
  `jobfair_title` VARCHAR(200) NOT NULL,
  `jobfair_addtime` INT UNSIGNED NOT NULL,
  `position_id` INT UNSIGNED NOT NULL,
  `position` VARCHAR(10) NOT NULL,
  `note` VARCHAR(200) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `contact` VARCHAR(30) NOT NULL DEFAULT '',
  `mobile` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `etypr` (`etype`),
  KEY `uid` (`uid`),
  KEY `jobfairid` (`jobfair_id`),
  KEY `eaddtime` (`eaddtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair_exhibitors||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_position`;
CREATE TABLE `qs_jobfair_position` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jobfair_id` INT UNSIGNED NOT NULL,
  `area_id` VARCHAR(10) NOT NULL,
  `position` VARCHAR(10) NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `company_uid` INT UNSIGNED NOT NULL,
  `company_name` VARCHAR(30) NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL COMMENT '0可预订 1已预订 2审核中 3暂停预定',
  `orderid` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair_position||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_position_tpl`;
CREATE TABLE `qs_jobfair_position_tpl` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(30) NOT NULL,
  `area` TEXT NOT NULL,
  `position` TEXT NOT NULL,
  `position_img` TEXT NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair_position_tpl||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_retrospect`;
CREATE TABLE `qs_jobfair_retrospect` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `img` INT NOT NULL,
  `jobfair_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair_retrospect||-_-|| */

DROP TABLE IF EXISTS `qs_link`;
CREATE TABLE `qs_link` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `change_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
  `name` VARCHAR(32) NOT NULL COMMENT '名称',
  `link_url` VARCHAR(255) NOT NULL COMMENT '跳转地址',
  `link_ico` VARCHAR(255) NOT NULL COMMENT '图片地址',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `notes` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '简介',
  `content` TEXT NOT NULL COMMENT '正文',
  `type` TINYINT NOT NULL DEFAULT 1 COMMENT '类型',
  PRIMARY KEY (`id`),
  KEY `index_show_order` (`sort_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_link||-_-|| */
DROP TABLE IF EXISTS `qs_link_type`;
CREATE TABLE `qs_link_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否系统',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_link_type||-_-|| */

DROP TABLE IF EXISTS `qs_mail_tpl`;
CREATE TABLE `qs_mail_tpl` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `title` TEXT NOT NULL,
  `value` TEXT NOT NULL,
  `variate` TEXT NOT NULL,
  `status` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_mail_tpl||-_-|| */

DROP TABLE IF EXISTS `qs_market_queue`;
CREATE TABLE `qs_market_queue` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `message` TINYINT(1) UNSIGNED NOT NULL,
  `mobile` VARCHAR(15) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `weixin_openid` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_market_queue||-_-|| */

DROP TABLE IF EXISTS `qs_market_task`;
CREATE TABLE `qs_market_task` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(30) NOT NULL,
  `content` TEXT NOT NULL,
  `send_type` VARCHAR(30) NOT NULL,
  `target` VARCHAR(30) NOT NULL,
  `condition` TEXT NOT NULL,
  `total` INT UNSIGNED NOT NULL,
  `success` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_market_task||-_-|| */

DROP TABLE IF EXISTS `qs_market_tpl`;
CREATE TABLE `qs_market_tpl` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `content` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_market_tpl||-_-|| */

DROP TABLE IF EXISTS `qs_member`;
CREATE TABLE `qs_member` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `rid` INT UNSIGNED DEFAULT 0 COMMENT '转换用户id',
  `utype` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `mobile` VARCHAR(11) NOT NULL,
  `username` VARCHAR(30) NOT NULL DEFAULT '',
  `email` VARCHAR(30) NOT NULL,
  `password` VARCHAR(100) NOT NULL DEFAULT '',
  `pwd_hash` VARCHAR(30) NOT NULL,
  `reg_time` INT UNSIGNED NOT NULL,
  `reg_ip` VARCHAR(30) NOT NULL,
  `reg_address` VARCHAR(30) NOT NULL,
  `last_login_time` INT UNSIGNED NOT NULL,
  `last_login_ip` VARCHAR(30) NOT NULL,
  `last_login_address` VARCHAR(30) NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `avatar` INT UNSIGNED NOT NULL,
  `robot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `platform` VARCHAR(30) NOT NULL DEFAULT '',
  `nologin_notice_counter` INT UNSIGNED NOT NULL DEFAULT 0,
  `disable_im` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '禁用职聊',
  `account` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '个人资金',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_username` (`username`),
  UNIQUE KEY `index_mobile` (`mobile`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member||-_-|| */

DROP TABLE IF EXISTS `qs_member_appeal`;
CREATE TABLE `qs_member_appeal` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `realname` VARCHAR(30) NOT NULL,
  `mobile` VARCHAR(11) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_appeal||-_-|| */

DROP TABLE IF EXISTS `qs_member_bind`;
CREATE TABLE `qs_member_bind` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `type` VARCHAR(30) NOT NULL,
  `openid` VARCHAR(50) NOT NULL,
  `unionid` VARCHAR(50) NOT NULL,
  `nickname` VARCHAR(30) NOT NULL,
  `avatar` VARCHAR(255) NOT NULL,
  `bindtime` INT UNSIGNED NOT NULL,
  `is_subscribe` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `qianfanyunapp_uid` INT NOT NULL DEFAULT 0 COMMENT '千帆app用户id',
  `magapp_uid` INT NOT NULL DEFAULT 0 COMMENT '马甲app用户id',
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_bind||-_-|| */

DROP TABLE IF EXISTS `qs_member_cancel_apply`;
CREATE TABLE `qs_member_cancel_apply` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `handlertime` INT UNSIGNED NOT NULL,
  `regtime` VARCHAR(30) NOT NULL,
  `companyname` VARCHAR(100) NOT NULL,
  `mobile` VARCHAR(11) NOT NULL,
  `contact` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_cancel_apply||-_-|| */

DROP TABLE IF EXISTS `qs_member_points`;
CREATE TABLE `qs_member_points` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `points` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_points||-_-|| */

DROP TABLE IF EXISTS `qs_member_points_log`;
CREATE TABLE `qs_member_points_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `op` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1增加 2减少',
  `points` INT UNSIGNED NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_points_log||-_-|| */

DROP TABLE IF EXISTS `qs_member_setmeal`;
CREATE TABLE `qs_member_setmeal` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `expired` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `setmeal_id` INT UNSIGNED NOT NULL,
  `deadline` INT UNSIGNED NOT NULL,
  `download_resume_point` INT UNSIGNED NOT NULL DEFAULT 0,
  `jobs_meanwhile` INT UNSIGNED NOT NULL DEFAULT 0,
  `refresh_jobs_free_perday` INT UNSIGNED NOT NULL,
  `download_resume_max_perday` INT UNSIGNED NOT NULL DEFAULT 0,
  `service_added_discount` double(2,1) UNSIGNED NOT NULL,
  `enable_video_interview` TINYINT(1) UNSIGNED NOT NULL,
  `enable_poster` TINYINT(1) UNSIGNED NOT NULL,
  `show_apply_contact` TINYINT(1) UNSIGNED NOT NULL,
  `im_max_perday` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '每天最多可发起聊天次数',
  `im_total` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '一共可发起聊天次数',
  `purchase_resume_point` INT NOT NULL DEFAULT 0 COMMENT '购买增值简历包',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`),
  KEY `index_setmeal_id` (`setmeal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_setmeal||-_-|| */

DROP TABLE IF EXISTS `qs_member_setmeal_log`;
CREATE TABLE `qs_member_setmeal_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_setmeal_log||-_-|| */

DROP TABLE IF EXISTS `qs_message`;
CREATE TABLE `qs_message` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `type` INT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `inner_link` VARCHAR(30) NOT NULL,
  `inner_link_params` INT UNSIGNED NOT NULL,
  `spe_link_params` VARCHAR(100) NOT NULL DEFAULT '',
  `addtime` INT UNSIGNED NOT NULL,
  `is_readed` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_message||-_-|| */

DROP TABLE IF EXISTS `qs_microposte_tpl`;
CREATE TABLE `qs_microposte_tpl` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jobnum` TINYINT(1) NOT NULL,
  `name` VARCHAR(30) NOT NULL,
  `thumb` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_microposte_tpl||-_-|| */

DROP TABLE IF EXISTS `qs_mobile_index_menu`;
CREATE TABLE `qs_mobile_index_menu` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `title` VARCHAR(30) NOT NULL,
  `custom_title` VARCHAR(30) NOT NULL,
  `icon` INT UNSIGNED NOT NULL,
  `link_url` VARCHAR(255) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_mobile_index_menu||-_-|| */

DROP TABLE IF EXISTS `qs_mobile_index_module`;
CREATE TABLE `qs_mobile_index_module` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `plan_id` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_mobile_index_module||-_-|| */

DROP TABLE IF EXISTS `qs_navigation`;
CREATE TABLE `qs_navigation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `title` VARCHAR(15) NOT NULL,
  `link_type` TINYINT(1) UNSIGNED NOT NULL,
  `page` VARCHAR(30) NOT NULL,
  `url` VARCHAR(200) NOT NULL,
  `target` VARCHAR(10) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_navigation||-_-|| */

DROP TABLE IF EXISTS `qs_notice`;
CREATE TABLE `qs_notice` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `attach` TEXT NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `link_url` VARCHAR(200) NOT NULL DEFAULT '',
  `seo_keywords` VARCHAR(100) NOT NULL DEFAULT '',
  `seo_description` VARCHAR(200) NOT NULL DEFAULT '',
  `click` INT UNSIGNED NOT NULL DEFAULT 0,
  `addtime` INT UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `index_sort_id_addtime` (`sort_id`,`addtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_notice||-_-|| */

DROP TABLE IF EXISTS `qs_notify_log`;
CREATE TABLE `qs_notify_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `alias` VARCHAR(30) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid_time` (`uid`,`addtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_notify_log||-_-|| */

DROP TABLE IF EXISTS `qs_notify_rule`;
CREATE TABLE `qs_notify_rule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `type` INT UNSIGNED NOT NULL,
  `title` VARCHAR(30) NOT NULL,
  `content` TEXT NOT NULL,
  `inner_link` VARCHAR(30) NOT NULL,
  `open_message` TINYINT(1) NOT NULL,
  `open_sms` TINYINT(1) NOT NULL,
  `open_email` TINYINT(1) NOT NULL,
  `open_push` TINYINT(1) NOT NULL,
  `max_time_per_day` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_notify_rule||-_-|| */

DROP TABLE IF EXISTS `qs_order`;
CREATE TABLE `qs_order` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `oid` VARCHAR(50) NOT NULL,
  `service_type` VARCHAR(30) NOT NULL,
  `service_name` VARCHAR(30) NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL COMMENT '最终支付金额',
  `service_amount` decimal(10,2) UNSIGNED NOT NULL COMMENT '服务价格',
  `service_amount_after_discount` decimal(10,2) UNSIGNED NOT NULL COMMENT '折后价格',
  `deduct_amount` decimal(10,2) UNSIGNED NOT NULL COMMENT '抵扣掉的金额',
  `deduct_points` INT UNSIGNED NOT NULL COMMENT '抵扣积分数',
  `payment` VARCHAR(20) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `paytime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  `extra` TEXT NOT NULL,
  `note` VARCHAR(200) NOT NULL,
  `add_platform` VARCHAR(30) NOT NULL DEFAULT '',
  `pay_platform` VARCHAR(30) NOT NULL DEFAULT '',
  `service_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `return_url` VARCHAR(255) NOT NULL,
  `deadline` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_order||-_-|| */

DROP TABLE IF EXISTS `qs_order_tmp`;
CREATE TABLE `qs_order_tmp` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `oid` VARCHAR(50) NOT NULL,
  `service_type` VARCHAR(30) NOT NULL,
  `service_name` VARCHAR(30) NOT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL COMMENT '最终支付金额',
  `service_amount` decimal(10,2) UNSIGNED NOT NULL COMMENT '服务价格',
  `service_amount_after_discount` decimal(10,2) UNSIGNED NOT NULL COMMENT '折后价格',
  `deduct_amount` decimal(10,2) UNSIGNED NOT NULL COMMENT '抵扣掉的金额',
  `deduct_points` INT UNSIGNED NOT NULL COMMENT '抵扣积分数',
  `payment` VARCHAR(20) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `paytime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  `extra` TEXT NOT NULL,
  `note` VARCHAR(200) NOT NULL,
  `add_platform` VARCHAR(30) NOT NULL DEFAULT '',
  `pay_platform` VARCHAR(30) NOT NULL DEFAULT '',
  `service_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `return_url` VARCHAR(255) NOT NULL,
  `deadline` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_order_tmp||-_-|| */

DROP TABLE IF EXISTS `qs_page`;
CREATE TABLE `qs_page` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `alias` VARCHAR(30) NOT NULL,
  `enable_cache` TINYINT(1) UNSIGNED NOT NULL,
  `expire` INT NOT NULL,
  `seo_title` VARCHAR(100) NOT NULL,
  `seo_keywords` VARCHAR(100) NOT NULL,
  `seo_description` VARCHAR(200) NOT NULL,
  `params` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_page||-_-|| */

DROP TABLE IF EXISTS `qs_personal_service_stick`;
CREATE TABLE `qs_personal_service_stick` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `days` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL,
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_personal_service_stick||-_-|| */

DROP TABLE IF EXISTS `qs_personal_service_tag`;
CREATE TABLE `qs_personal_service_tag` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `days` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL,
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_personal_service_tag||-_-|| */

DROP TABLE IF EXISTS `qs_refresh_job_log`;
CREATE TABLE `qs_refresh_job_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `platform` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_refresh_job_log||-_-|| */

DROP TABLE IF EXISTS `qs_refresh_resume_log`;
CREATE TABLE `qs_refresh_resume_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `platform` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_refresh_resume_log||-_-|| */

DROP TABLE IF EXISTS `qs_refreshjob_queue`;
CREATE TABLE `qs_refreshjob_queue` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `execute_time` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_refreshjob_queue||-_-|| */

DROP TABLE IF EXISTS `qs_resume`;
CREATE TABLE `qs_resume` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `high_quality` TINYINT(1) UNSIGNED NOT NULL COMMENT '高质量',
  `display_name` TINYINT(1) UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL COMMENT '审核通过为1',
  `stick` TINYINT(1) UNSIGNED NOT NULL,
  `service_tag` VARCHAR(15) NOT NULL DEFAULT '',
  `fullname` VARCHAR(15) NOT NULL COMMENT '名称',
  `sex` TINYINT(1) UNSIGNED NOT NULL COMMENT '性别;1男;2女',
  `birthday` VARCHAR(15) NOT NULL COMMENT '生日;1998-01',
  `residence` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '现居住地',
  `height` VARCHAR(5) NOT NULL COMMENT '身高',
  `marriage` TINYINT(1) UNSIGNED NOT NULL COMMENT '婚姻;0保密;1未婚;2已婚',
  `education` INT UNSIGNED NOT NULL COMMENT '学历 1初中-9博后',
  `enter_job_time` INT UNSIGNED NOT NULL COMMENT '开始工作时间',
  `householdaddress` VARCHAR(30) NOT NULL DEFAULT COMMENT '籍贯',
  `major1` INT UNSIGNED NOT NULL COMMENT '专业id;category_major',
  `major2` INT UNSIGNED NOT NULL COMMENT '专业id;category_major',
  `major` INT UNSIGNED NOT NULL COMMENT '专业id;category_major',
  `tag` VARCHAR(100) NOT NULL COMMENT '特长标签 表category->type 为"QS_resumetag" 的 id 组合,多个使用逗号分割',
  `idcard` VARCHAR(18) NOT NULL COMMENT '身份证号',
  `specialty` TEXT NOT NULL COMMENT '自我描述',
  `photo_img` INT NOT NULL COMMENT '头像id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `refreshtime` INT UNSIGNED NOT NULL COMMENT '刷新时间',
  `updatetime` INT UNSIGNED NOT NULL COMMENT '更新时间',
  `current` INT UNSIGNED NOT NULL COMMENT '求职意向json',
  `click` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT '查看次数',
  `tpl` VARCHAR(30) NOT NULL,
  `custom_field_1` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_field_2` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `custom_field_3` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '自定义字段',
  `platform` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '注册点',
  `remark` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '备注',
  `comment` VARCHAR(200) NOT NULL DEFAULT '',
  `is_live` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '是否是直播间创建简历[0:否;1:是]',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`),
  KEY `index_refreshtime` (`refreshtime`),
  KEY `index_addtime` (`addtime`),
  KEY `index_audit` (`audit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume||-_-|| */

DROP TABLE IF EXISTS `qs_resume_audit_log`;
CREATE TABLE `qs_resume_audit_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `resumeid` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `reason` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_audit_log||-_-|| */
/* 简历证书 */
DROP TABLE IF EXISTS `qs_resume_certificate`;
CREATE TABLE `qs_resume_certificate` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `name` VARCHAR(30) NOT NULL COMMENT '名称',
  `cover` CHAR(255) NOT NULL COMMENT '封面url',
  `obtaintime` INT NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_certificate||-_-|| */

DROP TABLE IF EXISTS `qs_resume_complete`;
CREATE TABLE `qs_resume_complete` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `basic` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `intention` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `specialty` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `education` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `work` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `training` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `project` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `certificate` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `language` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `tag` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `img` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_rid_uid` (`rid`,`uid`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_complete||-_-|| */

DROP TABLE IF EXISTS `qs_resume_contact`;
CREATE TABLE `qs_resume_contact` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `rid` INT UNSIGNED NOT NULL,
  `mobile` VARCHAR(11) NOT NULL,
  `email` VARCHAR(30) NOT NULL DEFAULT '',
  `qq` VARCHAR(30) NOT NULL DEFAULT '',
  `weixin` VARCHAR(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_rid_uid` (`rid`,`uid`),
  UNIQUE KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_contact||-_-|| */
/* 教育经历 */
DROP TABLE IF EXISTS `qs_resume_education`;
CREATE TABLE `qs_resume_education` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL COMMENT '表Resume的id',
  `uid` INT UNSIGNED NOT NULL COMMENT '用户id',
  `starttime` INT UNSIGNED NOT NULL COMMENT '教育开始时间',
  `endtime` INT UNSIGNED NOT NULL COMMENT '教育结束时间',
  `todate` INT UNSIGNED NOT NULL COMMENT '至今 则为1',
  `school` VARCHAR(30) NOT NULL COMMENT '教育经历-学校',
  `major` VARCHAR(20) NOT NULL COMMENT '教育经历-专业',
  `education` INT UNSIGNED NOT NULL '教育等级;1小学-9博后',
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_education||-_-|| */

DROP TABLE IF EXISTS `qs_resume_img`;
CREATE TABLE `qs_resume_img` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `img` INT UNSIGNED NOT NULL,
  `title` VARCHAR(20) NOT NULL DEFAULT '',
  `addtime` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_img||-_-|| */
/* 期望工作 */
DROP TABLE IF EXISTS `qs_resume_intention`;
CREATE TABLE `qs_resume_intention` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `nature` INT UNSIGNED NOT NULL COMMENT '工作性质;1全职,2兼职,3实习',
  `category1` INT UNSIGNED NOT NULL COMMENT '表category_job 1级id',
  `category2` INT UNSIGNED NOT NULL COMMENT '表category_job 2级id',
  `category3` INT UNSIGNED NOT NULL COMMENT '表category_job 3级id',
  `category` INT UNSIGNED NOT NULL  COMMENT '职位;表category_job最终id',
  `district1` INT UNSIGNED NOT NULL COMMENT '表category_district 1级id',
  `district2` INT UNSIGNED NOT NULL COMMENT '表category_district 2级id',
  `district3` INT UNSIGNED NOT NULL COMMENT '表category_district 3级id',
  `district` INT UNSIGNED NOT NULL  COMMENT '地址表category_district最终id',
  `minwage` INT NOT NULL COMMENT '最低工资',
  `maxwage` INT NOT NULL COMMENT '最高工资',
  `trade` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '期望行业;表category alias=QS_trade id',
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_trade` (`trade`),
  KEY `index_wage` (`minwage`,`maxwage`),
  KEY `index_district` (`district`),
  KEY `index_category` (`category`),
  KEY `index_uid` (`uid`),
  KEY `index_category1` (`category1`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_intention||-_-|| */
/* 语言能力 */
DROP TABLE IF EXISTS `qs_resume_language`;
CREATE TABLE `qs_resume_language` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `language` INT UNSIGNED NOT NULL COMMENT '语种能力 category alias=QS_language id',
  `level` INT UNSIGNED NOT NULL COMMENT '语种等级 category alias=QS_language_level id',
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_language||-_-|| */
/* 简历模块 */
DROP TABLE IF EXISTS `qs_resume_module`;
CREATE TABLE `qs_resume_module` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_name` VARCHAR(30) NOT NULL,
  `module_cn` VARCHAR(30) NOT NULL,
  `score` INT UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `enable_close` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_module_name` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_module||-_-|| */
/* 项目经历 */
DROP TABLE IF EXISTS `qs_resume_project`;
CREATE TABLE `qs_resume_project` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `starttime` INT UNSIGNED NOT NULL COMMENT '开始时间',
  `endtime` INT UNSIGNED NOT NULL COMMENT '结束时间',
  `todate` INT UNSIGNED NOT NULL COMMENT '至今为1',
  `projectname` VARCHAR(30) NOT NULL COMMENT '项目名称',
  `role` VARCHAR(30) NOT NULL COMMENT '担任角色',
  `description` TEXT NOT NULL COMMENT '项目描述',
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_project||-_-|| */

DROP TABLE IF EXISTS `qs_resume_search_key`;
CREATE TABLE `qs_resume_search_key` (
  `id` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `high_quality` TINYINT(1) UNSIGNED NOT NULL,
  `photo` TINYINT(1) UNSIGNED NOT NULL,
  `stick` TINYINT(1) UNSIGNED NOT NULL,
  `sex` TINYINT(1) UNSIGNED NOT NULL,
  `birthyear` smallint(4) UNSIGNED NOT NULL,
  `education` INT UNSIGNED NOT NULL,
  `enter_job_time` INT UNSIGNED NOT NULL,
  `major1` INT UNSIGNED NOT NULL,
  `major2` INT UNSIGNED NOT NULL,
  `major` INT UNSIGNED NOT NULL,
  `tag` VARCHAR(50) NOT NULL,
  `intention_jobs` VARCHAR(255) NOT NULL,
  `fulltext_key` TEXT NOT NULL,
  `refreshtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`),
  FULLTEXT KEY `index_intention_jobs` (`intention_jobs`) /*!50100 WITH PARSER `ngram` */ ,
  FULLTEXT KEY `index_fulltext_index` (`intention_jobs`,`fulltext_key`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_search_key||-_-|| */

DROP TABLE IF EXISTS `qs_resume_search_rtime`;
CREATE TABLE `qs_resume_search_rtime` (
  `id` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `high_quality` TINYINT(1) UNSIGNED NOT NULL,
  `photo` TINYINT(1) UNSIGNED NOT NULL,
  `stick` TINYINT(1) UNSIGNED NOT NULL,
  `sex` TINYINT(1) UNSIGNED NOT NULL,
  `birthyear` smallint(4) UNSIGNED NOT NULL,
  `education` INT UNSIGNED NOT NULL,
  `enter_job_time` INT UNSIGNED NOT NULL,
  `major1` INT UNSIGNED NOT NULL,
  `major2` INT UNSIGNED NOT NULL,
  `major` INT UNSIGNED NOT NULL,
  `tag` VARCHAR(50) NOT NULL,
  `refreshtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uid` (`uid`),
  KEY `index_stick_rtime` (`stick`,`refreshtime`),
  KEY `index_rtime` (`refreshtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_search_rtime||-_-|| */
/* 简历-培训经历 */
DROP TABLE IF EXISTS `qs_resume_training`;
CREATE TABLE `qs_resume_training` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `starttime` INT UNSIGNED NOT NULL COMMENT '开始时间',
  `endtime` INT UNSIGNED NOT NULL COMMENT '结束时间',
  `todate` INT UNSIGNED NOT NULL COMMENT '至今为1',
  `agency` VARCHAR(30) NOT NULL COMMENT '培训机构',
  `course` VARCHAR(30) NOT NULL COMMENT '培训课程',
  `description` TEXT NOT NULL COMMENT '培训内容',
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_training||-_-|| */

DROP TABLE IF EXISTS `qs_resume_work`;
CREATE TABLE `qs_resume_work` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rid` INT UNSIGNED NOT NULL COMMENT '表Resume的id',
  `uid` INT UNSIGNED NOT NULL COMMENT '用户id',
  `starttime` INT UNSIGNED NOT NULL COMMENT '开始时间',
  `endtime` INT UNSIGNED NOT NULL COMMENT '结束时间',
  `todate` INT UNSIGNED NOT NULL COMMENT '至今 则为1',

  `companyname` VARCHAR(30) NOT NULL COMMENT '工作经历-公司名称',
  `jobname` VARCHAR(30) NOT NULL COMMENT '工作经历-职位名称',
  `duty` TEXT NOT NULL COMMENT '工作经历-职责',
  PRIMARY KEY (`id`),
  KEY `index_rid_uid` (`rid`,`uid`),
  KEY `index_uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_resume_work||-_-|| */

DROP TABLE IF EXISTS `qs_service_queue`;
CREATE TABLE `qs_service_queue` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(30) NOT NULL,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `pid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `deadline` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_service_queue||-_-|| */

DROP TABLE IF EXISTS `qs_setmeal`;
CREATE TABLE `qs_setmeal` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `icon` INT UNSIGNED NOT NULL,
  `expense` decimal(10,2) UNSIGNED NOT NULL,
  `days` INT UNSIGNED NOT NULL DEFAULT 0,
  `preferential_open` TINYINT(1) UNSIGNED NOT NULL,
  `preferential_expense` decimal(10,2) UNSIGNED NOT NULL,
  `preferential_expense_start` INT UNSIGNED NOT NULL,
  `preferential_expense_end` INT UNSIGNED NOT NULL,
  `download_resume_point` INT UNSIGNED NOT NULL DEFAULT 0,
  `gift_point` INT UNSIGNED NOT NULL,
  `jobs_meanwhile` INT UNSIGNED NOT NULL DEFAULT 0,
  `refresh_jobs_free_perday` INT UNSIGNED NOT NULL,
  `download_resume_max_perday` INT UNSIGNED NOT NULL DEFAULT 0,
  `service_added_discount` double(2,1) UNSIGNED NOT NULL,
  `enable_video_interview` TINYINT(1) UNSIGNED NOT NULL,
  `enable_poster` TINYINT(1) UNSIGNED NOT NULL,
  `show_apply_contact` TINYINT(1) UNSIGNED NOT NULL,
  `note` VARCHAR(100) NOT NULL,
  `recommend` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `is_apply` TINYINT(1) UNSIGNED NOT NULL,
  `im_max_perday` INT UNSIGNED NOT NULL DEFAULT 0,
  `im_total` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_setmeal||-_-|| */

DROP TABLE IF EXISTS `qs_shield`;
CREATE TABLE `qs_shield` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_uid` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_uid` (`company_uid`),
  KEY `personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_shield||-_-|| */

DROP TABLE IF EXISTS `qs_sms_tpl`;
CREATE TABLE `qs_sms_tpl` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(30) NOT NULL,
  `code` VARCHAR(30) NOT NULL,
  `alisms_tplcode` VARCHAR(30) NOT NULL,
  `params` VARCHAR(100) NOT NULL,
  `content` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_sms_tpl||-_-|| */

DROP TABLE IF EXISTS `qs_stat_view_job`;
CREATE TABLE `qs_stat_view_job` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_uid` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_comuid_jobid` (`company_uid`,`jobid`),
  KEY `index_peruid_jobid_time` (`personal_uid`,`jobid`,`addtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_stat_view_job||-_-|| */

DROP TABLE IF EXISTS `qs_subscribe_job`;
CREATE TABLE `qs_subscribe_job` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `category1` INT UNSIGNED NOT NULL,
  `category2` INT UNSIGNED NOT NULL,
  `category3` INT UNSIGNED NOT NULL,
  `category` INT UNSIGNED NOT NULL,
  `district1` INT UNSIGNED NOT NULL,
  `district2` INT UNSIGNED NOT NULL,
  `district3` INT UNSIGNED NOT NULL,
  `district` INT UNSIGNED NOT NULL,
  `minwage` INT UNSIGNED NOT NULL,
  `maxwage` INT UNSIGNED NOT NULL,
  `pushtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_subscribe_job||-_-|| */

DROP TABLE IF EXISTS `qs_task`;
CREATE TABLE `qs_task` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `alias` VARCHAR(30) NOT NULL,
  `name` VARCHAR(30) NOT NULL,
  `points` INT UNSIGNED NOT NULL,
  `daily` TINYINT(1) NOT NULL,
  `max_perday` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_task||-_-|| */

DROP TABLE IF EXISTS `qs_task_record`;
CREATE TABLE `qs_task_record` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `alias` VARCHAR(30) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `points` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_task_record||-_-|| */

DROP TABLE IF EXISTS `qs_tipoff`;
CREATE TABLE `qs_tipoff` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `target_id` INT UNSIGNED NOT NULL,
  `type` TINYINT(1) UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `reason` TINYINT(1) UNSIGNED NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `img` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_tipoff||-_-|| */

DROP TABLE IF EXISTS `qs_uploadfile`;
CREATE TABLE `qs_uploadfile` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `save_path` VARCHAR(255) NOT NULL,
  `platform` VARCHAR(20) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上传者id',
  `utype` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '上传者类型: 0:用户; 1:管理者',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_uploadfile||-_-|| */

DROP TABLE IF EXISTS `qs_view_job`;
CREATE TABLE `qs_view_job` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_uid` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `jobid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_company_uid` (`company_uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_view_job||-_-|| */

DROP TABLE IF EXISTS `qs_view_resume`;
CREATE TABLE `qs_view_resume` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_uid` INT UNSIGNED NOT NULL,
  `personal_uid` INT UNSIGNED NOT NULL,
  `resume_id` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_company_uid` (`company_uid`),
  KEY `index_personal_uid` (`personal_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_view_resume||-_-|| */

DROP TABLE IF EXISTS `qs_wechat_fans`;
CREATE TABLE `qs_wechat_fans` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `openid` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_wechat_fans||-_-|| */

DROP TABLE IF EXISTS `qs_wechat_keyword`;
CREATE TABLE `qs_wechat_keyword` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `word` VARCHAR(30) NOT NULL,
  `return_text` VARCHAR(255) NOT NULL,
  `return_img` VARCHAR(255) NOT NULL,
  `return_img_mediaid` VARCHAR(100) NOT NULL,
  `return_link` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_wechat_keyword||-_-|| */

DROP TABLE IF EXISTS `qs_wechat_menu`;
CREATE TABLE `qs_wechat_menu` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT UNSIGNED NOT NULL DEFAULT 0,
  `title` VARCHAR(30) NOT NULL,
  `key` VARCHAR(30) NOT NULL,
  `type` VARCHAR(30) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `pagepath` VARCHAR(100) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_wechat_menu||-_-|| */

DROP TABLE IF EXISTS `qs_wechat_notify_rule`;
CREATE TABLE `qs_wechat_notify_rule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `title` VARCHAR(30) NOT NULL,
  `is_open` TINYINT(1) NOT NULL,
  `tpl_name` VARCHAR(30) NOT NULL,
  `tpl_number` VARCHAR(50) NOT NULL,
  `tpl_trade` VARCHAR(30) NOT NULL,
  `tpl_id` VARCHAR(50) NOT NULL,
  `tpl_data` VARCHAR(200) NOT NULL,
  `tpl_param` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_wechat_notify_rule||-_-|| */

DROP TABLE IF EXISTS `qs_wechat_share`;
CREATE TABLE `qs_wechat_share` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(30) NOT NULL,
  `name` VARCHAR(30) NOT NULL,
  `content` VARCHAR(100) NOT NULL,
  `img` VARCHAR(30) NOT NULL,
  `img_self_cn` VARCHAR(30) NOT NULL,
  `explain` VARCHAR(100) NOT NULL,
  `params` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_wechat_share||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_online`;
CREATE TABLE `qs_jobfair_online` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `thumb` INT UNSIGNED NOT NULL,
  `starttime` INT UNSIGNED NOT NULL,
  `endtime` INT UNSIGNED NOT NULL,
  `content` LONGTEXT NOT NULL,
  `enable_setmeal_id` VARCHAR(100) NOT NULL,
  `must_company_audit` TINYINT(1) UNSIGNED NOT NULL,
  `min_complete_percent` INT UNSIGNED NOT NULL,
  `click` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `qrcode` INT UNSIGNED NOT NULL,
  `pc_header_logo` INT NOT NULL COMMENT 'pc头部图片id',
  `mobile_header_logo` INT NOT NULL COMMENT '触屏头部图片id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair_online||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_online_participate`;
CREATE TABLE `qs_jobfair_online_participate` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jobfair_id` INT UNSIGNED NOT NULL,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `qrcode` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `source` TINYINT(1) UNSIGNED NOT NULL,
  `stick` TINYINT(1) UNSIGNED NOT NULL,
  `note` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_jobfair_online_participate||-_-|| */

DROP TABLE IF EXISTS `qs_scene_qrcode`;
CREATE TABLE `qs_scene_qrcode` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(32) NOT NULL,
  `title` VARCHAR(30) NOT NULL,
  `type` VARCHAR(30) NOT NULL,
  `deadline` INT UNSIGNED NOT NULL,
  `platform` TINYINT(1) UNSIGNED NOT NULL,
  `paramid` INT UNSIGNED NOT NULL,
  `qrcode_src` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_scene_qrcode||-_-|| */

DROP TABLE IF EXISTS `qs_scene_qrcode_reg_log`;
CREATE TABLE `qs_scene_qrcode_reg_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_scene_qrcode_reg_log||-_-|| */

DROP TABLE IF EXISTS `qs_scene_qrcode_scan_log`;
CREATE TABLE `qs_scene_qrcode_scan_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_scene_qrcode_scan_log||-_-|| */

DROP TABLE IF EXISTS `qs_scene_qrcode_subscribe_log`;
CREATE TABLE `qs_scene_qrcode_subscribe_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_scene_qrcode_subscribe_log||-_-|| */

DROP TABLE IF EXISTS `qs_badword`;
CREATE TABLE `qs_badword` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `replace_text` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_badword||-_-|| */

DROP TABLE IF EXISTS `qs_tweets_label`;
CREATE TABLE `qs_tweets_label` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `value` TEXT NOT NULL,
  `type` TINYINT(3) NOT NULL DEFAULT 2 COMMENT '1-头部底部；2-主体',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_tweets_label||-_-|| */

DROP TABLE IF EXISTS `qs_tweets_template`;
CREATE TABLE `qs_tweets_template` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `temname` VARCHAR(255) NOT NULL,
  `title` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `footer` TEXT NOT NULL,
  `addtime` INT DEFAULT NULL,
  `is_sys` TINYINT(3) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_tweets_template||-_-|| */

DROP TABLE IF EXISTS `qs_identity_token`;
CREATE TABLE `qs_identity_token` (
  `mdtoken` VARCHAR(32) NOT NULL COMMENT 'token',
  `updatetime` INT UNSIGNED NOT NULL COMMENT '更新时间',
  `uid` INT(11) NOT NULL DEFAULT 0 COMMENT '用户uid',
  `expire` INT UNSIGNED NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`mdtoken`),
  KEY `index_token` (`mdtoken`) USING BTREE,
  KEY `index_uid` (`uid`) USING BTREE,
  KEY `index_expire` (`expire`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='登录凭证记录表';
/* ||-_-||qs_identity_token||-_-|| */

DROP TABLE IF EXISTS `qs_campus_ad`;
CREATE TABLE `qs_campus_ad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_display` TINYINT(1) NOT NULL DEFAULT 1,
  `cid` INT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `imageid` INT UNSIGNED NOT NULL,
  `imageurl` VARCHAR(255) NOT NULL,
  `explain` VARCHAR(255) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `starttime` INT UNSIGNED NOT NULL,
  `deadline` INT NOT NULL DEFAULT 0,
  `target` TINYINT(1) UNSIGNED NOT NULL,
  `link_url` VARCHAR(255) NOT NULL,
  `inner_link` VARCHAR(30) NOT NULL,
  `inner_link_params` INT NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_campus_ad||-_-|| */

DROP TABLE IF EXISTS `qs_campus_ad_category`;
CREATE TABLE `qs_campus_ad_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `width` INT NOT NULL,
  `height` INT NOT NULL,
  `ad_num` INT UNSIGNED NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `platform` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_campus_ad_category||-_-|| */

DROP TABLE IF EXISTS `qs_campus_election`;
CREATE TABLE `qs_campus_election` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` INT UNSIGNED NOT NULL,
  `subject` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `display` INT NOT NULL DEFAULT 1,
  `address` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `starttime` INT UNSIGNED NOT NULL,
  `endtime` INT UNSIGNED NOT NULL,
  `introduction` LONGTEXT,
  `company_count` INT UNSIGNED NOT NULL,
  `graduate_count` INT UNSIGNED NOT NULL,
  `click` INT NOT NULL DEFAULT 0,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_campus_election||-_-|| */

DROP TABLE IF EXISTS `qs_campus_notice`;
CREATE TABLE `qs_campus_notice` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `link_url` VARCHAR(200) NOT NULL DEFAULT '',
  `seo_keywords` VARCHAR(100) NOT NULL DEFAULT '',
  `seo_description` VARCHAR(200) NOT NULL DEFAULT '',
  `click` INT UNSIGNED NOT NULL DEFAULT 0,
  `addtime` INT UNSIGNED NOT NULL,
  `holddate_start` INT UNSIGNED NOT NULL,
  `holddate_end` INT UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `index_sort_id_addtime` (`sort_id`,`addtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_campus_notice||-_-|| */

DROP TABLE IF EXISTS `qs_campus_preach`;
CREATE TABLE `qs_campus_preach` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` INT UNSIGNED NOT NULL,
  `subject` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `display` INT NOT NULL DEFAULT 1,
  `address` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `starttime` INT UNSIGNED NOT NULL,
  `introduction` LONGTEXT,
  `click` INT NOT NULL DEFAULT 0,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_campus_preach||-_-|| */

DROP TABLE IF EXISTS `qs_campus_school`;
CREATE TABLE `qs_campus_school` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `logo` VARCHAR(255) CHARACTER SET utf8 NOT NULL,
  `display` INT NOT NULL DEFAULT 1,
  `district1` INT UNSIGNED NOT NULL COMMENT '所在地区',
  `district2` INT UNSIGNED NOT NULL,
  `district3` INT UNSIGNED NOT NULL,
  `district` INT UNSIGNED NOT NULL,
  `level` TINYINT(4) UNSIGNED NOT NULL COMMENT '院校层次',
  `type` TINYINT(4) UNSIGNED NOT NULL COMMENT '院校类型',
  `introduction` LONGTEXT,
  `address` VARCHAR(255) CHARACTER SET utf8 NOT NULL COMMENT '地址',
  `tel` VARCHAR(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '电话',
  `click` INT NOT NULL DEFAULT 0,
  `addtime` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_campus_school||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_ad`;
CREATE TABLE `qs_cityinfo_ad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_display` TINYINT(1) NOT NULL DEFAULT 1,
  `cid` INT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `imageid` INT UNSIGNED NOT NULL,
  `imageurl` VARCHAR(255) NOT NULL,
  `explain` VARCHAR(255) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `starttime` INT UNSIGNED NOT NULL,
  `deadline` INT NOT NULL DEFAULT 0,
  `target` TINYINT(1) UNSIGNED NOT NULL,
  `link_url` VARCHAR(255) NOT NULL,
  `inner_link` VARCHAR(30) NOT NULL,
  `inner_link_params` INT NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_ad||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_ad_category`;
CREATE TABLE `qs_cityinfo_ad_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `width` INT NOT NULL,
  `height` INT NOT NULL,
  `ad_num` INT UNSIGNED NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `platform` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_ad_category||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_article`;
CREATE TABLE `qs_cityinfo_article` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `linkman` VARCHAR(20) NOT NULL DEFAULT '',
  `mobile` VARCHAR(11) NOT NULL DEFAULT '',
  `type_id` INT(11) NOT NULL DEFAULT 0,
  `is_recommend` TINYINT(4) NOT NULL DEFAULT 0,
  `is_top` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0未置顶,1置顶',
  `is_public` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1展示,0隐藏',
  `endtime` INT(11) NOT NULL DEFAULT 0 COMMENT '到期日期',
  `audit` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1已审核,0未审核,2未通过',
  `lat` double NOT NULL DEFAULT 0 COMMENT '纬度',
  `lon` double NOT NULL DEFAULT 0 COMMENT '经度',
  `address_detail` VARCHAR(30) NOT NULL DEFAULT '',
  `desc` VARCHAR(200) NOT NULL DEFAULT '',
  `imgs` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '逗号分隔的图片id',
  `view_times` INT(11) NOT NULL DEFAULT 0,
  `share_times` INT(11) NOT NULL DEFAULT 0,
  `title` VARCHAR(50) NOT NULL DEFAULT '',
  `refreshtime` INT(11) NOT NULL DEFAULT 0,
  `addtime` INT(11) NOT NULL DEFAULT 0,
  `updatetime` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `type_id_refreshtime` (`type_id`,`audit`,`is_top`,`endtime`,`refreshtime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_article||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_article_audit_log`;
CREATE TABLE `qs_cityinfo_article_audit_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `article_id` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `reason` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_article_audit_log||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_article_body`;
CREATE TABLE `qs_cityinfo_article_body` (
  `article_id` INT(11) NOT NULL,
  `content` TEXT NOT NULL,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_article_body||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_feedback`;
CREATE TABLE `qs_cityinfo_feedback` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` INT NOT NULL,
  `mobile` CHAR(11) NOT NULL DEFAULT '',
  `uid` INT UNSIGNED NOT NULL,
  `content` VARCHAR(255) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1已处理0未处理',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_feedback||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_order`;
CREATE TABLE `qs_cityinfo_order` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_sn` VARCHAR(32) NOT NULL,
  `desc` VARCHAR(100) DEFAULT '',
  `title` VARCHAR(30) NOT NULL,
  `mobile` CHAR(11) NOT NULL,
  `uid` INT(11) NOT NULL,
  `type` TINYINT(4) NOT NULL COMMENT '订单类型:1发信息,2看信息,3推广,4,发信息+推广,5刷新',
  `amount` INT(11) NOT NULL COMMENT '单位:分',
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0初始订单,1支付完成',
  `pay_type` VARCHAR(10) NOT NULL DEFAULT '',
  `third_order_id` VARCHAR(50) NOT NULL DEFAULT '',
  `paytime` INT(11) NOT NULL DEFAULT 0,
  `item_id` INT(11) NOT NULL COMMENT '信息id',
  `param1` INT(11) NOT NULL DEFAULT 0 COMMENT '额外参数',
  `param2` INT(11) NOT NULL DEFAULT 0 COMMENT '额外参数',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`),
  KEY `uid_item_id` (`uid`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_order||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_phone_book`;
CREATE TABLE `qs_cityinfo_phone_book` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `lat` double NOT NULL DEFAULT 0 COMMENT '纬度',
  `lon` double NOT NULL DEFAULT 0 COMMENT '经度',
  `address_detail` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '手输详细地址',
  `type_id` INT(11) NOT NULL,
  `mobile` VARCHAR(11) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL DEFAULT '',
  `weixin` VARCHAR(30) NOT NULL DEFAULT '',
  `qrcode` INT(11) NOT NULL DEFAULT 0,
  `audit` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '1审核通过,0未审核,2未通过',
  `sort_id` INT(11) NOT NULL DEFAULT 0,
  `ip` VARCHAR(15) NOT NULL DEFAULT '' COMMENT 'ip 地址',
  `is_sys` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1系统,0外部添加',
  `logo` INT(11) NOT NULL DEFAULT 0,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  `publish_tel` VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_phone_book||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_phone_book_audit_log`;
CREATE TABLE `qs_cityinfo_phone_book_audit_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone_book_id` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `reason` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `phone_book_id` (`phone_book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_phone_book_audit_log||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_phone_book_type`;
CREATE TABLE `qs_cityinfo_phone_book_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(20) NOT NULL,
  `sort_id` INT(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `is_display` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '是否展示',
  `logo` INT(11) NOT NULL DEFAULT 0,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_phone_book_type||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_refresh_log`;
CREATE TABLE `qs_cityinfo_refresh_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` CHAR(10) NOT NULL,
  `refresh_times` smallint(6) NOT NULL,
  `uid` INT(11) NOT NULL,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `date_uid` (`date`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_refresh_log||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_search_article`;
CREATE TABLE `qs_cityinfo_search_article` (
  `article_id` INT(11) NOT NULL,
  `content` TEXT NOT NULL,
  `endtime` INT(11) NOT NULL DEFAULT 0,
  `refreshtime` INT(11) NOT NULL,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`article_id`),
  KEY `refreshtime` (`refreshtime`),
  FULLTEXT KEY `content` (`content`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_search_article||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_type`;
CREATE TABLE `qs_cityinfo_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT(11) NOT NULL DEFAULT 0,
  `title` VARCHAR(20) NOT NULL,
  `sort_id` INT(11) NOT NULL DEFAULT 0,
  `pay_for_create` VARCHAR(500) NOT NULL,
  `need_pay_for_create` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '发布信息是否收费',
  `pay_for_mobile` INT(11) NOT NULL DEFAULT 0,
  `need_pay_for_mobile` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '查看联系方式是否收费',
  `logo` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_sys` TINYINT(4) NOT NULL DEFAULT 1,
  `is_display` TINYINT(4) NOT NULL DEFAULT 1,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='同城-分类表';
/* ||-_-||qs_cityinfo_type||-_-|| */

DROP TABLE IF EXISTS `qs_cityinfo_view_log`;
CREATE TABLE `qs_cityinfo_view_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `article_id` INT(11) NOT NULL,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `addtime_article_id` (`addtime`,`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_cityinfo_view_log||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_ad`;
CREATE TABLE `qs_freelance_ad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_display` TINYINT(1) NOT NULL DEFAULT 1,
  `cid` INT NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `imageid` INT UNSIGNED NOT NULL,
  `imageurl` VARCHAR(255) NOT NULL,
  `explain` VARCHAR(255) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `starttime` INT UNSIGNED NOT NULL,
  `deadline` INT NOT NULL DEFAULT 0,
  `target` TINYINT(1) UNSIGNED NOT NULL,
  `link_url` VARCHAR(255) NOT NULL,
  `inner_link` VARCHAR(30) NOT NULL,
  `inner_link_params` INT NOT NULL,
  `company_id` INT UNSIGNED NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_ad||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_ad_category`;
CREATE TABLE `qs_freelance_ad_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `width` INT NOT NULL,
  `height` INT NOT NULL,
  `ad_num` INT UNSIGNED NOT NULL,
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `platform` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_ad_category||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_order`;
CREATE TABLE `qs_freelance_order` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_sn` VARCHAR(32) NOT NULL,
  `desc` VARCHAR(100) DEFAULT '',
  `title` VARCHAR(30) NOT NULL,
  `mobile` CHAR(11) NOT NULL,
  `uid` INT(11) NOT NULL,
  `type` TINYINT(4) NOT NULL COMMENT '订单类型:1简历发布,2项目发布,3简历套餐,4,项目套餐',
  `amount` INT(11) NOT NULL COMMENT '单位:分',
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0初始订单,1支付完成',
  `pay_type` VARCHAR(10) NOT NULL DEFAULT '',
  `third_order_id` VARCHAR(50) NOT NULL DEFAULT '',
  `paytime` INT(11) NOT NULL DEFAULT 0,
  `item_id` INT(11) NOT NULL COMMENT 'type=1表示简历,2表示项目,',
  `param` INT(11) NOT NULL DEFAULT 0 COMMENT '额外参数',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`order_sn`),
  KEY `uid_item_id` (`uid`,`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_order||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_project`;
CREATE TABLE `qs_freelance_project` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `startdate` INT(11) NOT NULL COMMENT '开始日期',
  `enddate` INT NOT NULL COMMENT '结束日期,0至今',
  `name` VARCHAR(50) NOT NULL COMMENT '目项名',
  `role` VARCHAR(50) NOT NULL COMMENT '角色 ',
  `description` TEXT NOT NULL COMMENT '述描',
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='个人项目表';
/* ||-_-||qs_freelance_project||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_resume`;
CREATE TABLE `qs_freelance_resume` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `avatar` INT(11) NOT NULL DEFAULT 0 COMMENT '头像',
  `age` INT(11) NOT NULL DEFAULT 0,
  `gender` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '1男,2女,0未知',
  `education` INT(11) NOT NULL DEFAULT 0 COMMENT '学历',
  `is_public` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '是否公开,1公开,0关闭',
  `view_times` INT(11) NOT NULL DEFAULT 0 COMMENT '览浏次数',
  `brief_intro` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '简介',
  `professional_title` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '职称,分隔',
  `start_work_date` VARCHAR(11) NOT NULL DEFAULT '' COMMENT '开始工作时间',
  `living_city` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '现居地',
  `mobile` VARCHAR(11) NOT NULL DEFAULT '' COMMENT '联系电话',
  `weixin` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '微信',
  `name` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '姓名',
  `hide_name` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '是否保密显示姓名,1是,0否',
  `audit` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0未审核,1已审核,2审核未通过',
  `is_published` TINYINT(4) NOT NULL COMMENT '0未发布,1已发布',
  `is_top` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '是否置顶',
  `addtime` INT(11) NOT NULL,
  `refreshtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `view_times` (`view_times`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='简历';
/* ||-_-||qs_freelance_resume||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_resume_audit_log`;
CREATE TABLE `qs_freelance_resume_audit_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `resumeid` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `reason` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_resume_audit_log||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_search_resume`;
CREATE TABLE `qs_freelance_search_resume` (
  `resume_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` TEXT NOT NULL,
  `refreshtime` INT(11) NOT NULL DEFAULT 0,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`resume_id`),
  FULLTEXT KEY `content` (`content`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_search_resume||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_search_subject`;
CREATE TABLE `qs_freelance_search_subject` (
  `subject_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` TEXT NOT NULL,
  `refreshtime` INT(11) NOT NULL DEFAULT 0,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`subject_id`),
  FULLTEXT KEY `content` (`content`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_search_subject||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_service`;
CREATE TABLE `qs_freelance_service` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `title` VARCHAR(50) NOT NULL,
  `price` INT(11) NOT NULL DEFAULT 0 COMMENT '服务价格,0面议',
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务';
/* ||-_-||qs_freelance_service||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_skill`;
CREATE TABLE `qs_freelance_skill` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `level` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0入门,熟悉1,精通2',
  `custom_name` VARCHAR(30) NOT NULL DEFAULT '',
  `skill_id` INT(11) NOT NULL,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_skill||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_skill_type`;
CREATE TABLE `qs_freelance_skill_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT(11) NOT NULL DEFAULT 0 COMMENT '父id',
  `title` VARCHAR(50) NOT NULL COMMENT '技能',
  `sort_id` INT(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='技能类型';
/* ||-_-||qs_freelance_skill_type||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_subject`;
CREATE TABLE `qs_freelance_subject` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `title` VARCHAR(100) NOT NULL COMMENT '项目名称',
  `price` INT(11) NOT NULL COMMENT '单位:分',
  `endtime` INT(11) NOT NULL COMMENT '结束时间',
  `period` INT(11) NOT NULL COMMENT '工期,单位:天',
  `desc` TEXT NOT NULL COMMENT '项目描述',
  `linkman` VARCHAR(20) NOT NULL,
  `mobile` VARCHAR(11) NOT NULL,
  `is_public` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1开启,0关闭',
  `view_times` INT(11) NOT NULL DEFAULT 0 COMMENT '览浏次数',
  `is_recommend` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '荐推',
  `is_top` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '置顶',
  `audit` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0未审核,1审核通过,2审核失败',
  `is_published` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0未发布,1发布成功',
  `weixin` VARCHAR(30) NOT NULL,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  `refreshtime` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `view_times` (`view_times`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='项目表';
/* ||-_-||qs_freelance_subject||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_subject_audit_log`;
CREATE TABLE `qs_freelance_subject_audit_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `subjectid` INT UNSIGNED NOT NULL,
  `audit` TINYINT(1) UNSIGNED NOT NULL,
  `reason` VARCHAR(200) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subjectid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_subject_audit_log||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_visit_history`;
CREATE TABLE `qs_freelance_visit_history` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `type` TINYINT(4) NOT NULL,
  `item_id` INT(11) NOT NULL COMMENT '如果type是1,item_id是简历,type是2,item_id是项目',
  `addtime` INT(11) NOT NULL COMMENT '访问时间',
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid_type` (`uid`,`type`,`updatetime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_freelance_visit_history||-_-|| */

DROP TABLE IF EXISTS `qs_freelance_works`;
CREATE TABLE `qs_freelance_works` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '作品名字',
  `uid` INT UNSIGNED NOT NULL,
  `img` INT(11) NOT NULL DEFAULT 0,
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='作品表';
/* ||-_-||qs_freelance_works||-_-|| */

DROP TABLE IF EXISTS `qs_member_action_log`;
CREATE TABLE `qs_member_action_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `utype` TINYINT(1) UNSIGNED NOT NULL,
  `uid` INT UNSIGNED NOT NULL,
  `content` VARCHAR(100) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `ip` VARCHAR(30) NOT NULL,
  `ip_addr` VARCHAR(30) NOT NULL,
  `platform` VARCHAR(30) NOT NULL DEFAULT '',
  `is_login` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  FULLTEXT KEY `index_content` (`content`) /*!50100 WITH PARSER `ngram` */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_action_log||-_-|| */

DROP TABLE IF EXISTS `qs_page_mobile`;
CREATE TABLE `qs_page_mobile` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) NOT NULL,
  `alias` VARCHAR(30) NOT NULL,
  `enable_cache` TINYINT(1) UNSIGNED NOT NULL,
  `expire` INT NOT NULL,
  `seo_title` VARCHAR(100) NOT NULL,
  `seo_keywords` VARCHAR(100) NOT NULL,
  `seo_description` VARCHAR(200) NOT NULL,
  `params` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_page_mobile||-_-|| */

DROP TABLE IF EXISTS `qs_service_ol`;
CREATE TABLE `qs_service_ol` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` VARCHAR(15) NOT NULL COMMENT '姓名',
  `mobile` VARCHAR(20) NOT NULL COMMENT '手机',
  `weixin` INT NOT NULL COMMENT '微信图片',
  `qq` VARCHAR(30) NOT NULL COMMENT 'QQ',
  `sort` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_service_ol||-_-|| */

DROP TABLE IF EXISTS `qs_category_job_template`;
CREATE TABLE `qs_category_job_template` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` INT UNSIGNED NOT NULL COMMENT 'pid',
  `title` VARCHAR(30) NOT NULL COMMENT '标题',
  `content` TEXT NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='职位分类描述模板表';
/* ||-_-||qs_category_job_template||-_-|| */

DROP TABLE IF EXISTS `qs_admin_scan_cert`;
CREATE TABLE `qs_admin_scan_cert` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` VARCHAR(32) NOT NULL,
  `info` TEXT NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_admin_scan_cert||-_-|| */

DROP TABLE IF EXISTS `qs_tpl`;
CREATE TABLE `qs_tpl` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(30) NOT NULL,
  `alias` VARCHAR(30) NOT NULL,
  `type` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_tpl||-_-|| */

DROP TABLE IF EXISTS `qs_member_setmeal_open_log`;
CREATE TABLE `qs_member_setmeal_open_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `setmeal_id` INT UNSIGNED NOT NULL,
  `setmeal_name` VARCHAR(30) NOT NULL COMMENT '套餐名称',
  `addtime` INT UNSIGNED NOT NULL,
  `type` TINYINT(1) UNSIGNED NOT NULL COMMENT '开通方式 1注册赠送 2自主开通 3后台开通',
  `type_cn` VARCHAR(30) NOT NULL,
  `order_id` INT UNSIGNED NOT NULL COMMENT '订单id',
  `admin_username` VARCHAR(30) NOT NULL,
  `admin_id` INT UNSIGNED NOT NULL COMMENT '管理员id',
  PRIMARY KEY (`id`),
  KEY `setmeal_id` (`setmeal_id`),
  KEY `admin_id` (`admin_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_member_setmeal_open_log||-_-|| */

DROP TABLE IF EXISTS `qs_sms_blacklist`;
CREATE TABLE `qs_sms_blacklist` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` VARCHAR(11) NOT NULL,
  `addtime` INT UNSIGNED NOT NULL,
  `note` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_sms_blacklist||-_-|| */

DROP TABLE IF EXISTS `qs_sv_ad`;
CREATE TABLE `qs_sv_ad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_display` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
  `cid` INT NOT NULL COMMENT '分类id',
  `title` VARCHAR(100) NOT NULL COMMENT '标题',
  `imageid` INT UNSIGNED NOT NULL COMMENT '图片id',
  `imageurl` VARCHAR(255) NOT NULL COMMENT '图片地址',
  `explain` VARCHAR(255) NOT NULL COMMENT '图片文字说明 ',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `starttime` INT UNSIGNED NOT NULL COMMENT '开始时间',
  `deadline` INT NOT NULL DEFAULT 0 COMMENT '结束时间',
  `target` TINYINT(1) UNSIGNED NOT NULL COMMENT '跳转链接类型',
  `link_url` VARCHAR(255) NOT NULL COMMENT '跳转地址',
  `inner_link` VARCHAR(30) NOT NULL COMMENT '跳转内链地址',
  `inner_link_params` INT NOT NULL COMMENT '跳转内链参数',
  `company_id` INT UNSIGNED NOT NULL COMMENT '企业id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='视频招聘广告表';
/* ||-_-||qs_sv_ad||-_-|| */

DROP TABLE IF EXISTS `qs_sv_ad_category`;
CREATE TABLE `qs_sv_ad_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(50) NOT NULL COMMENT '调用名称',
  `name` VARCHAR(100) NOT NULL COMMENT '广告位名称',
  `width` INT NOT NULL COMMENT '建议宽度',
  `height` INT NOT NULL COMMENT '建议高度',
  `ad_num` INT UNSIGNED NOT NULL COMMENT '广告数量',
  `is_sys` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是系统自带，1是，0否',
  `platform` VARCHAR(30) NOT NULL COMMENT '所属平台',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='视频招聘广告分类表';
/* ||-_-||qs_sv_ad_category||-_-|| */

DROP TABLE IF EXISTS `qs_sv_collect`;
CREATE TABLE `qs_sv_collect` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL,
  `vid` INT(11) NOT NULL COMMENT '视频id',
  `type` TINYINT(4) NOT NULL COMMENT '视频类型,1企业,2个人',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uid_type_vid` (`uid`,`type`,`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='赞记录表';
/* ||-_-||qs_sv_collect||-_-|| */

DROP TABLE IF EXISTS `qs_sv_company_video`;
CREATE TABLE `qs_sv_company_video` (
  `id` INT(11) NOT NULL COMMENT 'id<10万是未审核,',
  `is_public` TINYINT(4) NOT NULL DEFAULT 2 COMMENT '1不公开，2公开',
  `audit` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1未审核,2审核通过,3审核失败',
  `uid` INT(11) NOT NULL,
  `title` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '标题',
  `filesize` INT(11) NOT NULL DEFAULT 0 COMMENT '视频大小',
  `fid` INT(11) NOT NULL DEFAULT 0 COMMENT '文件id',
  `view_count` INT(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `lon` double NOT NULL DEFAULT 0 COMMENT '经度',
  `lat` double NOT NULL DEFAULT 0 COMMENT '纬度',
  `address` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '当前地址',
  `like` INT(11) NOT NULL DEFAULT 0 COMMENT '赞的数量',
  `real_id` INT(11) NOT NULL DEFAULT 0,
  `reason` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '审核未通过理由',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `updatetime` INT(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`),
  KEY `view_count` (`view_count`),
  KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业视频表';
/* ||-_-||qs_sv_company_video||-_-|| */

DROP TABLE IF EXISTS `qs_sv_personal_video`;
CREATE TABLE `qs_sv_personal_video` (
  `id` INT(11) NOT NULL COMMENT 'id<10万是未审核,',
  `is_public` TINYINT(4) NOT NULL DEFAULT 2 COMMENT '1不公开，2公开',
  `audit` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '1未审核,2审核通过,3审核失败',
  `uid` INT(11) NOT NULL,
  `title` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '标题',
  `filesize` INT(11) NOT NULL DEFAULT 0 COMMENT '视频大小',
  `fid` INT(11) NOT NULL DEFAULT 0 COMMENT '文件id',
  `view_count` INT(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `lon` double NOT NULL DEFAULT 0 COMMENT '经度',
  `lat` double NOT NULL DEFAULT 0 COMMENT '纬度',
  `address` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '当前地址',
  `like` INT(11) NOT NULL DEFAULT 0 COMMENT '赞的数量',
  `real_id` INT(11) NOT NULL DEFAULT 0,
  `reason` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '审核未通过理由',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `updatetime` INT(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `addtime` (`addtime`),
  KEY `view_count` (`view_count`),
  KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='个人视频表';
/* ||-_-||qs_sv_personal_video||-_-|| */

DROP TABLE IF EXISTS `qs_fast_job`;
CREATE TABLE `qs_fast_job` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `audit` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '审核 0待审核；1已通过；2未通过',
  `is_top` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '置顶  0否；1-是',
  `is_recommend` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '推荐 0否；1-是',
  `jobname` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '职位名字',
  `comname` VARCHAR(100) NOT NULL COMMENT '企业名称',
  `contact` VARCHAR(100) NOT NULL COMMENT '联系人',
  `telephone` VARCHAR(20) NOT NULL COMMENT '联系电话',
  `address` VARCHAR(100) NOT NULL COMMENT '联系地址',
  `content` TEXT NOT NULL COMMENT '具体描述',
  `valid` INT NOT NULL COMMENT '有效期：7；15；30；0',
  `adminpwd` VARCHAR(100) NOT NULL COMMENT '管理密码',
  `addtime` INT NOT NULL COMMENT '添加时间',
  `refreshtime` INT NOT NULL COMMENT '刷新时间',
  `endtime` INT NOT NULL COMMENT '到期时间',
  `click` INT NOT NULL DEFAULT 0 COMMENT '点击量',
  PRIMARY KEY (`id`),
  KEY `refreshtime` (`refreshtime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='快招职位表';
/* ||-_-||qs_fast_job||-_-|| */

DROP TABLE IF EXISTS `qs_fast_job_auth_log`;
CREATE TABLE `qs_fast_job_auth_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `jobid` INT UNSIGNED NOT NULL COMMENT '职位id',
  `audit` TINYINT(1) UNSIGNED NOT NULL COMMENT '审核状态',
  `reason` VARCHAR(200) NOT NULL COMMENT '原因',
  `addtime` INT UNSIGNED NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `jobid` (`jobid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='快招职位审核日志表';
/* ||-_-||qs_fast_job_auth_log||-_-|| */

DROP TABLE IF EXISTS `qs_fast_resume`;
CREATE TABLE `qs_fast_resume` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `audit` TINYINT(3) DEFAULT 0 COMMENT '审核  0待审核；1已通过；2未通过',
  `is_top` TINYINT(1) DEFAULT 0 COMMENT '置顶 0否；1-是',
  `is_recommend` TINYINT(1) DEFAULT 0 COMMENT '推荐 0否；1-是',
  `fullname` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` TINYINT(2) NOT NULL COMMENT '性别',
  `experience` INT NOT NULL DEFAULT 0 COMMENT '工作经验',
  `wantjob` VARCHAR(100) NOT NULL COMMENT '期望职位',
  `telephone` VARCHAR(20) NOT NULL COMMENT '联系电话',
  `content` TEXT NOT NULL COMMENT '具体描述',
  `valid` INT NOT NULL COMMENT '有效期：7；15；30；0',
  `adminpwd` VARCHAR(100) NOT NULL COMMENT '管理密码',
  `addtime` INT NOT NULL COMMENT '添加时间',
  `refreshtime` INT NOT NULL COMMENT '刷新时间',
  `endtime` INT NOT NULL COMMENT '到期时间',
  `click` INT NOT NULL DEFAULT 0 COMMENT '点击量',
  PRIMARY KEY (`id`),
  KEY `refreshtime` (`refreshtime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='快招简历表';
/* ||-_-||qs_fast_resume||-_-|| */

DROP TABLE IF EXISTS `qs_fast_resume_auth_log`;
CREATE TABLE `qs_fast_resume_auth_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `resumeid` INT UNSIGNED NOT NULL COMMENT '简历id',
  `audit` TINYINT(1) UNSIGNED NOT NULL COMMENT '审核状态',
  `reason` VARCHAR(200) NOT NULL COMMENT '原因',
  `addtime` INT UNSIGNED NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `resumeid` (`resumeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='快招简历审核日志表';
/* ||-_-||qs_fast_resume_auth_log||-_-|| */

DROP TABLE IF EXISTS `qs_crm_config`;
CREATE TABLE `qs_crm_config` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(20) NOT NULL COMMENT '类型名称',
  `name` VARCHAR(20) NOT NULL COMMENT '子类型名称',
  `sort_id` INT(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `param` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '外额参数',
  `is_sys` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '是否是系统自带配置',
  `remark` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm配置表';
/* ||-_-||qs_crm_config||-_-|| */

DROP TABLE IF EXISTS `qs_crm_customer`;
CREATE TABLE `qs_crm_customer` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL DEFAULT 0 COMMENT '主系统uid',
  `comid` INT(11) NOT NULL DEFAULT 0 COMMENT '主系统公司id',
  `company_name` VARCHAR(40) NOT NULL DEFAULT 0 COMMENT '企业注册名称',
  `sales_consultant` INT(11) NOT NULL DEFAULT 0 COMMENT '销售顾问',
  `title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '客户名称',
  `level` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '等级',
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '状态',
  `labels` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '标签',
  `com_addr` VARCHAR(40) NOT NULL DEFAULT '' COMMENT '客户地址',
  `master_linkman` INT(11) NOT NULL DEFAULT 0 COMMENT '主要联系人',
  `bind_change_time` INT(11) NOT NULL DEFAULT 0 COMMENT '绑定状态变化时间',
  `last_pre_visit_id` INT(11) NOT NULL DEFAULT 0 COMMENT '预回访id',
  `last_visit_id` INT(11) NOT NULL DEFAULT 0 COMMENT '最近跟进id',
  `last_visit_time` INT(11) NOT NULL DEFAULT 0 COMMENT '最近跟进时间',
  `updatetime` INT(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `deletetime` INT(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `realdelete` INT(11) NOT NULL DEFAULT 0 COMMENT '硬删除时间',
  `remark` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `comid` (`comid`),
  KEY `sales_consultant` (`sales_consultant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm客户表';
/* ||-_-||qs_crm_customer||-_-|| */

DROP TABLE IF EXISTS `qs_crm_get_log`;
CREATE TABLE `qs_crm_get_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `get_time` INT(11) NOT NULL COMMENT '领取时间',
  `cid` INT(11) NOT NULL DEFAULT 0 COMMENT '客户id',
  `sc_id` INT(11) NOT NULL COMMENT '领取人id',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `from_sc_id` INT(11) NOT NULL DEFAULT 0 COMMENT '0表示从公海领取',
  `remark` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '备注',
  `op_id` INT(11) NOT NULL DEFAULT 0 COMMENT '操作者id',
  `action_type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0新增 1领取 2转交',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `sc_id` (`sc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm客户领取记录表';
/* ||-_-||qs_crm_get_log||-_-|| */

DROP TABLE IF EXISTS `qs_crm_level_log`;
CREATE TABLE `qs_crm_level_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` INT(11) NOT NULL COMMENT '客户id',
  `level` INT(11) NOT NULL COMMENT '变化后的等级',
  `sc_id` INT(11) NOT NULL COMMENT '销售id',
  `addtime` INT(11) NOT NULL,
  `from_level` INT(11) NOT NULL COMMENT '原等级',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`addtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm客户等级变化记录表';
/* ||-_-||qs_crm_level_log||-_-|| */

DROP TABLE IF EXISTS `qs_crm_linkman`;
CREATE TABLE `qs_crm_linkman` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` INT(11) NOT NULL DEFAULT 0 COMMENT '客户id',
  `name` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '联系人名字',
  `gender` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '性别',
  `position` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '职务',
  `appellation` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '称呼',
  `mobile` CHAR(11) NOT NULL DEFAULT '' COMMENT '手机',
  `telephone` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '座机',
  `email` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `qq` VARCHAR(20) NOT NULL DEFAULT '' COMMENT 'qq号',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `updatetime` INT(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm联系人表';
/* ||-_-||qs_crm_linkman||-_-|| */

DROP TABLE IF EXISTS `qs_crm_order`;
CREATE TABLE `qs_crm_order` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `crm_title` VARCHAR(30) NOT NULL DEFAULT '',
  `amount` INT(11) NOT NULL DEFAULT 0 COMMENT '金额,单位:分',
  `cid` INT(11) NOT NULL COMMENT '客户id',
  `ordertype` INT(11) NOT NULL DEFAULT 0 COMMENT '服务类型',
  `paytype` VARCHAR(20) NOT NULL DEFAULT 0 COMMENT '支付方式',
  `sc_id` INT(11) NOT NULL DEFAULT 0 COMMENT '售销id',
  `status` INT(11) NOT NULL DEFAULT 0 COMMENT '核算状态',
  `remark` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注',
  `sys_order_id` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '对应的主系统订单号',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `paytime` INT(11) NOT NULL DEFAULT 0 COMMENT '成交时间',
  PRIMARY KEY (`id`),
  KEY `sys_order_id` (`sys_order_id`),
  KEY `sc_id` (`sc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm业绩表';
/* ||-_-||qs_crm_order||-_-|| */

DROP TABLE IF EXISTS `qs_crm_reserve_visit`;
CREATE TABLE `qs_crm_reserve_visit` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` INT(11) NOT NULL COMMENT '户客id',
  `pre_time` INT(11) NOT NULL COMMENT '预约时间',
  `content` VARCHAR(100) NOT NULL COMMENT '内容',
  `sc_id` INT(11) NOT NULL COMMENT '售销id',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `visit_time` INT(11) NOT NULL DEFAULT 0 COMMENT '回访时间,0未回访',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm预约回访表';
/* ||-_-||qs_crm_reserve_visit||-_-|| */

DROP TABLE IF EXISTS `qs_crm_resume`;
CREATE TABLE `qs_crm_resume` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sc_id` INT(11) NOT NULL COMMENT '销售id',
  `uid` INT(11) NOT NULL COMMENT '主系统uid',
  `last_visit_time` INT(11) NOT NULL DEFAULT 0 COMMENT '上次回访时间',
  `remark` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注',
  `last_visit_id` INT(11) NOT NULL DEFAULT 0 COMMENT '上次回访id',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `updatetime` INT(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm简历表';
/* ||-_-||qs_crm_resume||-_-|| */

DROP TABLE IF EXISTS `qs_crm_resume_get_log`;
CREATE TABLE `qs_crm_resume_get_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `get_time` INT(11) NOT NULL COMMENT '领取时间',
  `uid` INT(11) NOT NULL DEFAULT 0 COMMENT '主系统uid',
  `sc_id` INT(11) NOT NULL COMMENT '销售 id',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `from_sc_id` INT(11) NOT NULL DEFAULT 0 COMMENT '0表示从公海领取',
  `remark` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '备注',
  `op_id` INT(11) NOT NULL DEFAULT 0 COMMENT '操作者id',
  `action_type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0新增 1领取 2转交',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `sc_id` (`sc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm简历领取记录表';
/* ||-_-||qs_crm_resume_get_log||-_-|| */

DROP TABLE IF EXISTS `qs_crm_resume_visit`;
CREATE TABLE `qs_crm_resume_visit` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL COMMENT '主系统uid',
  `method` INT(11) NOT NULL DEFAULT 0 COMMENT '跟进方式',
  `call_result` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '呼叫结果',
  `addtime` INT(11) NOT NULL,
  `updatetime` INT(11) NOT NULL,
  `sc_id` INT(11) NOT NULL DEFAULT 0 COMMENT '售销id',
  `result` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '跟进结果',
  `big_id` INT(11) NOT NULL DEFAULT 0 COMMENT '长内容id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='简历跟进记录表';
/* ||-_-||qs_crm_resume_visit||-_-|| */

DROP TABLE IF EXISTS `qs_crm_resume_visit_big`;
CREATE TABLE `qs_crm_resume_visit_big` (
  `visit_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `result` TEXT NOT NULL COMMENT '内容',
  PRIMARY KEY (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm简历跟进大内容表';
/* ||-_-||qs_crm_resume_visit_big||-_-|| */

DROP TABLE IF EXISTS `qs_crm_status_log`;
CREATE TABLE `qs_crm_status_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` INT(11) NOT NULL COMMENT '客户id',
  `status` INT(11) NOT NULL COMMENT '变化后的状态',
  `sc_id` INT(11) NOT NULL COMMENT '销售id',
  `addtime` INT(11) NOT NULL,
  `from_status` INT(11) NOT NULL COMMENT '原状态',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`,`addtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm客户状态变化记录表';
/* ||-_-||qs_crm_status_log||-_-|| */

DROP TABLE IF EXISTS `qs_crm_task`;
CREATE TABLE `qs_crm_task` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '任务标题',
  `type` INT(11) NOT NULL DEFAULT 0 COMMENT '任务类型',
  `create_sc_id` INT(11) NOT NULL DEFAULT 0 COMMENT '创建者id',
  `resolve_sc_id` INT(11) NOT NULL DEFAULT 0 COMMENT '处理者id',
  `complete_time` INT(11) NOT NULL DEFAULT 0 COMMENT '完成时间，0表示未完成',
  `result` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '反馈',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `updatetime` INT(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm任务表';
/* ||-_-||qs_crm_task||-_-|| */

DROP TABLE IF EXISTS `qs_crm_trip`;
CREATE TABLE `qs_crm_trip` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sc_id` INT(11) NOT NULL COMMENT '销售id',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `updatetime` INT(11) NOT NULL COMMENT '更新时间',
  `customer_name` VARCHAR(50) NOT NULL COMMENT '客户名称',
  `telephone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '座机',
  `mobile` VARCHAR(11) NOT NULL DEFAULT '' COMMENT '手机',
  `linkman_name` VARCHAR(11) NOT NULL COMMENT '联系人',
  `job_type` INT(11) NOT NULL COMMENT '任务类型',
  `back_time` INT(11) NOT NULL DEFAULT 0 COMMENT '返回时间',
  `out_time` INT(11) NOT NULL DEFAULT 0 COMMENT '外出时间',
  `remark` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `sc_id` (`sc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm外出记录表';
/* ||-_-||qs_crm_trip||-_-|| */

DROP TABLE IF EXISTS `qs_crm_visit_big_log`;
CREATE TABLE `qs_crm_visit_big_log` (
  `visit_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `result` TEXT NOT NULL,
  PRIMARY KEY (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm跟进企业记录大内容表';
/* ||-_-||qs_crm_visit_big_log||-_-|| */

DROP TABLE IF EXISTS `qs_crm_visit_log`;
CREATE TABLE `qs_crm_visit_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cid` INT(11) NOT NULL COMMENT '客户id',
  `method` INT(11) NOT NULL DEFAULT 0 COMMENT '跟进方式',
  `call_result` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '呼叫结果',
  `linkman` INT(11) NOT NULL DEFAULT 0 COMMENT '联系人id',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `updatetime` INT(11) NOT NULL COMMENT '更新时间',
  `sc_id` INT(11) NOT NULL DEFAULT 0 COMMENT '售销id',
  `result` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '跟进结果',
  `big_id` INT(11) NOT NULL DEFAULT 0 COMMENT '大内容id',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `sc_id` (`sc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='crm企业跟进记录表';
/* ||-_-||qs_crm_visit_log||-_-|| */

DROP TABLE IF EXISTS `qs_short_url`;
CREATE TABLE `qs_short_url` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(125) NOT NULL DEFAULT '' COMMENT '原始链接',
  `code` varbinary(5) NOT NULL DEFAULT '' COMMENT '短码',
  `addtime` INT(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `endtime` INT(11) NOT NULL DEFAULT 0 COMMENT '截止时间',
  `pv` INT(11) NOT NULL DEFAULT 0 COMMENT '点击量',
  `admin_id` INT(11) NOT NULL DEFAULT 0 COMMENT '建创者id',
  `remark` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `code_endtime` (`code`,`endtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4  COMMENT='短链接表';
/* ||-_-||qs_short_url||-_-|| */

DROP TABLE IF EXISTS `qs_subsite`;
CREATE TABLE `qs_subsite` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sitename` VARCHAR(30) NOT NULL,
  `district1` INT UNSIGNED NOT NULL,
  `district2` INT UNSIGNED NOT NULL,
  `district3` INT UNSIGNED NOT NULL,
  `district` INT UNSIGNED NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `keywords` VARCHAR(100) NOT NULL,
  `description` VARCHAR(200) NOT NULL,
  `tpl` VARCHAR(30) NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_subsite||-_-|| */

DROP TABLE IF EXISTS `qs_poster`;
CREATE TABLE `qs_poster` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `indexid` INT UNSIGNED NOT NULL,
  `type` TINYINT(1) UNSIGNED NOT NULL COMMENT '1职位 2简历 3企业',
  `name` VARCHAR(30) NOT NULL,
  `sort_id` INT UNSIGNED NOT NULL,
  `is_display` TINYINT(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_poster||-_-|| */

DROP TABLE IF EXISTS `qs_collection_seting`;
CREATE TABLE `qs_collection_seting` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '采集状态:0|关闭,1|开启',
    `matching_accuracy` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '匹配精准度',
    `job_seting` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '职位设置',
    `company_seting` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '企业设置',
    `account_seting` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '账号设置',
    `article_seting` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '资讯设置',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='采集设置';
/* ||-_-||qs_collection_seting||-_-|| */

DROP TABLE IF EXISTS `qs_marketing_template`;
CREATE TABLE `qs_marketing_template` (
 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID（模板ID）',
 `type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '模板类型[1:职位模板;2:企业模板;]',
 `built_in` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '内置模版[0:否;1:是]',
 `name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '模板名称',
 `head` LONGTEXT COMMENT '头部模板',
 `body` LONGTEXT COMMENT '内容（职位/企业）模板',
 `tail` LONGTEXT COMMENT '尾部模板',
 `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
 `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_marketing_template||-_-|| */

DROP TABLE IF EXISTS `qs_admin_notice`;
CREATE TABLE `qs_admin_notice` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) DEFAULT NULL,
  `is_open` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员通知项表';
/* ||-_-||qs_admin_notice||-_-|| */

DROP TABLE IF EXISTS `qs_admin_notice_config`;
CREATE TABLE `qs_admin_notice_config` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT(11) DEFAULT NULL COMMENT '管理员ID',
  `notice_id` INT(11) DEFAULT NULL COMMENT '通知项ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员与通知项关联表';
/* ||-_-||qs_admin_notice_config||-_-|| */

DROP TABLE IF EXISTS `qs_jobfair_online_view_log`;
CREATE TABLE `qs_jobfair_online_view_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL COMMENT 'uid',
  `addtime` INT(11) NOT NULL COMMENT '添加时间',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '查看类型 1-企业 2-职位 3-简历',
  `content_id` INT NOT NULL COMMENT '查看内容id（企业id/职位id/会员简历id）',
  `photo_img` INT NOT NULL DEFAULT 0 COMMENT '头像id',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='网络招聘会查看记录表';
/* ||-_-||qs_jobfair_online_view_log||-_-|| */

DROP TABLE IF EXISTS `qs_member_balance`;
CREATE TABLE `qs_member_balance`  (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NULL DEFAULT NULL COMMENT '用户ID',
  `money` decimal(10, 2) NULL DEFAULT NULL COMMENT '用户余额',
  `is_blacklist` INT(255) NULL DEFAULT 0 COMMENT '是否是黑名单',
  `token` VARCHAR(255) NULL DEFAULT NULL COMMENT 'token',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COMMENT = '会员余额表';
/* ||-_-||qs_member_balance||-_-|| */

DROP TABLE IF EXISTS `qs_member_balance_log`;
CREATE TABLE `qs_member_balance_log`  (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` INT UNSIGNED NOT NULL COMMENT 'uid',
  `type` VARCHAR(255) NULL DEFAULT NULL COMMENT '红包类型',
  `op` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '操作类型1增加2减少',
  `money` decimal(10, 2) UNSIGNED NOT NULL COMMENT '金额',
  `content` VARCHAR(255) NOT NULL COMMENT '操作内容',
  `addtime` INT UNSIGNED NOT NULL COMMENT '添加时间',
  `openid` VARCHAR(255) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `index_uid`(`uid`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COMMENT = '会员余额日志表';
/* ||-_-||qs_member_balance_log||-_-|| */

DROP TABLE IF EXISTS `qs_member_invite_register`;
CREATE TABLE `qs_member_invite_register`  (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` INT(11) NULL DEFAULT NULL COMMENT '邀请人',
  `uid` INT(11) NULL DEFAULT NULL COMMENT '被邀请人',
  `state` INT(11) NULL DEFAULT 0 COMMENT '邀请状态:0=邀请中,1=邀请通过,2=邀请失败',
  `examine_state` INT(11) NULL DEFAULT 0 COMMENT '审核状态:0=审核中,1=审核通过,2=拒绝',
  `examinetime` INT(11) NULL DEFAULT 0 COMMENT '审核时间',
  `addtime` INT(11) NULL DEFAULT 0 COMMENT '添加时间',
  `reason` VARCHAR(255) NULL DEFAULT NULL COMMENT '拒绝理由',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COMMENT = '会员邀请注册表';
/* ||-_-||qs_member_invite_register||-_-|| */

DROP TABLE IF EXISTS `qs_member_withdrawal_record`;
CREATE TABLE `qs_member_withdrawal_record`  (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NULL DEFAULT NULL COMMENT '用户ID',
  `price` decimal(10, 2) NULL DEFAULT NULL COMMENT '提现金额',
  `addtime` INT(11) NULL DEFAULT NULL COMMENT '提现时间',
  `state` INT(255) NULL DEFAULT NULL COMMENT '提现状态:0=拒绝,1=同意,2=审核中',
  `reason` VARCHAR(255) NULL DEFAULT NULL COMMENT '拒绝理由',
  `examinetime` INT(11) NULL DEFAULT 0 COMMENT '审核时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COMMENT = '提现记录';
/* ||-_-||qs_member_withdrawal_record||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_channel`;
CREATE TABLE `qs_corpwechat_channel` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `channel_name` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '二维码名称',
  `group_id` INT NOT NULL DEFAULT 1 COMMENT '所属分组',
  `user_list` TEXT COMMENT '选择员工【json】',
  `is_tag` TINYINT(1) DEFAULT 1 COMMENT '是否打渠道标签[1:是;0:否]',
  `channel_tag` TEXT COMMENT '渠道标签【json】',
  `welcome_type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '欢迎语类型[1:渠道欢迎语;2:默认欢迎语;3:不使用欢迎语;]',
  `text_content` VARCHAR(600) NOT NULL DEFAULT '' COMMENT '消息文本内容',
  `type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '附件类型[1:text;2:image;3:link;]',
  `pic_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '2:image.图片的链接',
  `link_form` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '3:link.链接欢迎语的链接形式[1:内链;2:外链]',
  `link_type` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '3:link.link_form=1:内链类型[index:首页;reg_personal:求职者注册页;reg_company:企业注册页;company:公司详情页;job:职位详情页;resume:简历详情页;notice:公告详情页;jobfair:招聘会详情页;jobfairol:网络招聘会详情页;news:资讯详情页]【link_form=1时生效】',
  `inner_id` INT NOT NULL DEFAULT 0 COMMENT '3.link.link_form=1:内链ID【link_form=1时生效】',
  `inner_name` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3.link.link_form=1:内链名称【link_form=1时生效】',
  `link_title` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.图文消息标题',
  `link_picurl` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.图文消息封面的url',
  `link_desc` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.图文消息的描述',
  `link_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.图文消息的链接',
  `skip_verify` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '自动通过好友[1:true(默认);0:false]',
  `state` CHAR(36) NOT NULL DEFAULT '' COMMENT '企业自定义的state参数，用于区分不同的添加渠道',
  `config_id` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '新增联系方式的配置id',
  `qr_code` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '联系我二维码链接，仅在scene为2时返回',
  `scan_num` INT NOT NULL DEFAULT 0 COMMENT '扫码次数',
  `is_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除[1:是;0:否]',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_channel||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_channel_day_log`;
CREATE TABLE `qs_corpwechat_channel_day_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `state` CHAR(36) NOT NULL DEFAULT '' COMMENT '企业自定义的state参数，用于区分不同的添加渠道',
  `add_total` INT NOT NULL DEFAULT 0 COMMENT '新增人数',
  `del_total` INT NOT NULL DEFAULT 0 COMMENT '流失人数',
  `follow_del` INT NOT NULL DEFAULT 0 COMMENT 'del_follow_user',
  `external_del` INT NOT NULL DEFAULT 0 COMMENT 'del_external_contact',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_channel_day_log||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_channel_group`;
CREATE TABLE `qs_corpwechat_channel_group` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '渠道活码分组ID',
  `name` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '渠道活码分组名称',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_channel_group||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_channel_log`;
CREATE TABLE `qs_corpwechat_channel_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `external_user_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '外部联系人的userid',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信成员UserID',
  `content` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '日志内容',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '[0:未定义;1:渠道活码添加好友;2:del_external_contact;3:del_follow_user;]',
  `state` CHAR(36) NOT NULL DEFAULT '' COMMENT '企业自定义的state参数，用于区分不同的添加渠道',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `external_user_id` (`external_user_id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_channel_log||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_corptag`;
CREATE TABLE `qs_corpwechat_corptag` (
  `id` VARCHAR(45) NOT NULL COMMENT '标签ID',
  `group_id` VARCHAR(45) NOT NULL COMMENT '标签组ID',
  `name` VARCHAR(45) NOT NULL COMMENT '标签名称',
  `group_name` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '标签组名称',
  `spell` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '拼音全品',
  `type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '标签类型[1:标签组;2:标签;]',
  `create_time` INT NOT NULL COMMENT '创建时间',
  `order` INT NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `index_parentid` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_corptag||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_external_log`;
CREATE TABLE `qs_corpwechat_external_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `external_user_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '外部联系人的userid',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信成员UserID',
  `content` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '日志内容',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '[0:未定义;1:添加企业客户事件;2:删除企业客户事件;3:删除跟进成员事件;]',
  PRIMARY KEY (`id`),
  KEY `external_user_id` (`external_user_id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_external_log||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_external_tag`;
CREATE TABLE `qs_corpwechat_external_tag` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '外部联系人标签ID',
  `external_user_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '外部联系人的userid',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信成员UserID',
  `group_name` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '标签的分组名称',
  `tag_name` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '标签名称',
  `type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '标签类型, 1-企业设置，2-用户自定义，3-规则组标签（仅系统应用返回）',
  `tag_id` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '标签ID',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` INT(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `is_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除[0:否;1:是;]',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_external_tag||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_external_user`;
CREATE TABLE `qs_corpwechat_external_user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `external_user_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '外部联系人的userid',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信成员UserID',
  `remark` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '备注',
  `type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '外部联系人类型[1:微信用户;2:企业微信用户]',
  `state` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '添加此用户的「联系我」方式配置的state参数，可用于识别添加此用户的渠道',
  `add_way` VARCHAR(3) NOT NULL DEFAULT 0 COMMENT '添加客户的来源[0:未知来源;1:扫描二维码;2:搜索手机号;3:名片分享;4:群聊;5:手机通讯录;6:微信联系人;8:安装第三方应用时自动添加的客服人员;9:搜索邮箱;10:视频号主页添加;201:内部成员共享;202:管理员/负责人分配]',
  `is_del` INT NOT NULL DEFAULT 0 COMMENT '是否删除[0:否;1:是;]',
  `source` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '删除客户的操作来源',
  `follow_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '删除跟进成员事件[0:否;1:是;]',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建/绑定时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  `add_time` INT NOT NULL DEFAULT 0 COMMENT '添加此外部联系人的时间',
  `tags` TEXT COMMENT '标签',
  `tag_group` TEXT COMMENT '标签组',
  `register` TINYINT(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '注册状态[3:非平台用户;1:企业用户;2:个人用户;]',
  `oper_userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '发起添加的userid，如果成员主动添加，为成员的userid；如果是客户主动添加，则为客户的外部联系人userid；如果是内部成员共享/管理员分配，则为对应的成员/管理员userid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_user_id_2` (`external_user_id`,`userid`),
  KEY `external_user_id` (`external_user_id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_external_user||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_group_chat`;
CREATE TABLE `qs_corpwechat_group_chat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '客户群列表主键',
  `chat_id` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '客户群ID',
  `name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '群名',
  `owner` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '群主ID',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '群的创建时间',
  `notice` TEXT COMMENT '群公告',
  `member_total` INT NOT NULL DEFAULT 0 COMMENT '群总人数',
  `input_time` INT NOT NULL DEFAULT 0 COMMENT '导入时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_group_chat||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_group_log`;
CREATE TABLE `qs_corpwechat_group_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '客户群ID',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '[0:未定义;1:成员入群;2:成员退群]',
  `mem_change_cnt` INT NOT NULL DEFAULT 0 COMMENT '入群或退群成员变更数量',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_group_log||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_group_user`;
CREATE TABLE `qs_corpwechat_group_user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '客户群ID',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信成员UserID',
  `is_owner` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是群主[0:否;1:是;]',
  `type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '成员类型[1:企业成员;2:外部联系人;]',
  `unionid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '微信unionid',
  `join_time` INT NOT NULL DEFAULT 0 COMMENT '入群时间',
  `join_scene` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '入群方式[0:未定义;1:由群成员邀请入群（直接邀请入群）;2:由群成员邀请入群（通过邀请链接入群）;3:通过扫描群二维码入群;]',
  `invitor` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '邀请者的userid',
  `nickname` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '在群里的昵称',
  `name` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '外部联系人名称',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_group_user||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_msg_template`;
CREATE TABLE `qs_corpwechat_msg_template` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `title` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '群发任务名称',
  `text_content` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '消息文本内容',
  `type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '附件类型[1:text;2:image;3:link;]',
  `pic_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '2:image.图片的链接',
  `link_form` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '3:link.链接欢迎语的链接形式[1:内链;2:外链]',
  `inner_id` INT NOT NULL DEFAULT 0 COMMENT '3.link.link_form=1:内链ID【link_form=1时生效】',
  `inner_name` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3.link.link_form=1:内链名称【link_form=1时生效】',
  `link_type` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '3:link.link_form=1:内链类型[index:首页;reg_personal:求职者注册页;reg_company:企业注册页;company:公司详情页;job:职位详情页;resume:简历详情页;notice:公告详情页;jobfair:招聘会详情页;jobfairol:网络招聘会详情页;news:资讯详情页]【link_form=1时生效】',
  `link_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的链接',
  `link_title` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的标题',
  `link_desc` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的描述',
  `link_picurl` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的封面的url',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `total_num` INT NOT NULL DEFAULT 0 COMMENT '推送客户数量',
  `fail_num` INT NOT NULL DEFAULT 0 COMMENT '推送失败数',
  `success_num` INT NOT NULL DEFAULT 0 COMMENT '推送成功数',
  `sender` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '发送企业群发消息的成员userid',
  `external_userids` TEXT COMMENT '客户的外部联系人id列表',
  `fail_list` TEXT COMMENT '无效或无法发送的external_userid列表',
  `msgId` VARCHAR(200) NOT NULL COMMENT '企业群发消息的id',
  `chat_type` VARCHAR(45) NOT NULL COMMENT '群发任务的类型[single(默认):客户，group:客户群]',
  `send_total` INT NOT NULL DEFAULT 0 COMMENT '发送总数',
  `send_fail` INT NOT NULL DEFAULT 0 COMMENT '发送失败数',
  `send_success` INT NOT NULL DEFAULT 0 COMMENT '发送成功数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_msg_template||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_push_log`;
CREATE TABLE `qs_corpwechat_push_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `external_user_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '外部联系人的UserID',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信UserID',
  `content` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '日志内容',
  `msgId` VARCHAR(200) NOT NULL COMMENT '企业群发消息的id',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '[0:未定义;1:推送消息;2:推送失败;3:推送成功;4:发送结果]',
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '发送状态[0:未发送;1:已发送;2:因客户不是好友导致发送失败;3:因客户已经收到其他群发消息导致发送失败]',
  `send_time` INT NOT NULL DEFAULT 0 COMMENT '发送时间',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `external_user_id` (`external_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_push_log||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_send_log`;
CREATE TABLE `qs_corpwechat_send_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `external_user_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '外部联系人的UserID',
  `userid` VARCHAR(64) DEFAULT '' COMMENT '企业微信UserID',
  `content` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '日志内容',
  `msgId` VARCHAR(200) NOT NULL COMMENT '企业群发消息的id',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '[0:未定义;1:推送消息;2:推送失败;3:推送成功;4:发送结果]',
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送状态[0:未发送;1:已发送;2:因客户不是好友导致发送失败;3:因客户已经收到其他群发消息导致发送失败]',
  `send_time` INT NOT NULL DEFAULT 0 COMMENT '发送时间',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `external_user_id` (`external_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_send_log||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_staff`;
CREATE TABLE `qs_corpwechat_staff` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `adminid` INT NOT NULL DEFAULT 0 COMMENT '后台管理员ID',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信成员UserID',
  `department` TEXT COMMENT '成员所属部门id列表',
  `mobile` VARCHAR(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `email` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '邮箱',
  `qr_code` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '	员工个人二维码',
  `alias` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '别名',
  `biz_mail` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '企业邮箱',
  `is_del` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除[0:否;1:是;]',
  `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '激活状态[1:已激活;2:已禁用;4:未激活;5:退出企业]',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建/绑定时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  `admin_id` INT NOT NULL DEFAULT 0 COMMENT '后台管理员ID',
  `is_bind` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否绑定企业微信[0:否;1:是;]',
  `bind_time` INT NOT NULL DEFAULT 0 COMMENT '绑定企业微信时间',
  `welcome_id` INT NOT NULL DEFAULT 0 COMMENT '欢迎语ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`) COMMENT '企业微信userId'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_staff||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_user_all`;
CREATE TABLE `qs_corpwechat_user_all` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `userid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '企业微信成员UserID',
  `name` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '外部联系人名称',
  `user_type` TINYINT(1) NOT NULL DEFAULT 2 COMMENT '成员类型[1:企业成员;2:外部联系人;]',
  `register` TINYINT(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '注册状态[1:企业用户;2:个人用户;3:非平台用户;4:企业成员;5:管理员;]',
  `avatar` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '头像url',
  `thumb_avatar` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '头像缩略图url',
  `external_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '外部联系人类型[0:未定义;1:微信用户;2:企业微信用户]',
  `mobile` VARCHAR(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `gender` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别[0:未定义;1:男性;2:女性]',
  `unionid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '微信unionid',
  `corp_name` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '外部联系人所在企业的简称',
  `corp_full_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '外部联系人所在企业的主体名称',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  `push_total` INT NOT NULL DEFAULT 0 COMMENT '推送总次数',
  `push_fail` INT NOT NULL DEFAULT 0 COMMENT '推送失败次数',
  `push_success` INT NOT NULL DEFAULT 0 COMMENT '推送成功次数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_user_all||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_userid_change_log`;
CREATE TABLE `qs_corpwechat_userid_change_log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '企微用户UserID变更记录ID',
  `userid` VARCHAR(64) NOT NULL COMMENT '旧UserID',
  `new_userid` VARCHAR(64) NOT NULL COMMENT '新UserID',
  `create_time` INT NOT NULL COMMENT '变更时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_userid_change_log||-_-|| */

DROP TABLE IF EXISTS `qs_corpwechat_welcome_words`;
CREATE TABLE `qs_corpwechat_welcome_words` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '欢迎语ID',
  `title` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '欢迎语名称',
  `text_content` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '消息文本内容',
  `type` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '欢迎语附件类型[1:text;2:image;3:link;]',
  `pic_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '2:image.图片欢迎语的图片链接',
  `link_form` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '3:link.链接欢迎语的链接形式[1:内链;2:外链]',
  `inner_id` INT NOT NULL DEFAULT 0 COMMENT '3.link.link_form=1:内链ID【link_form=1时生效】',
  `inner_name` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3.link.link_form=1:内链名称【link_form=1时生效】',
  `link_type` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '3:link.link_form=1:内链类型[index:首页;reg_personal:求职者注册页;reg_company:企业注册页;company:公司详情页;job:职位详情页;resume:简历详情页;notice:公告详情页;jobfair:招聘会详情页;jobfairol:网络招聘会详情页;news:资讯详情页]【link_form=1时生效】',
  `link_url` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的链接',
  `link_title` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的标题',
  `link_desc` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的描述',
  `link_picurl` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '3:link.链接欢迎语的封面的url',
  `create_time` INT NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` INT NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_corpwechat_welcome_words||-_-|| */

DROP TABLE IF EXISTS `qs_field_setting`;
CREATE TABLE `qs_field_setting` (
  `field_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_name` VARCHAR(15) NOT NULL DEFAULT '' COMMENT '字段名称',
  `field_alias` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '字段别名',
  `field_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '字段类型:1|文本框,2|单选框,3|复选框,4|下拉框,5|多行文本',
  `field_way` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '字段方式:1|普通,2|用于手机号需要验证码的情况',
  `field_remark` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '字段备注',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否显示:0|否,1|是',
  `is_system` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否系统内置:0|否,1|是',
  `add_admin_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '新增人ID',
  `add_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '新增时间',
  `update_admin_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改人ID',
  `update_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `is_del` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除:0|否',
  PRIMARY KEY (`field_id`),
  KEY `idx_is_del` (`is_del`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字段设置表';
/* ||-_-||qs_field_setting||-_-|| */

DROP TABLE IF EXISTS `qs_field_value`;
CREATE TABLE `qs_field_value` (
  `field_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '字段ID',
  `field_value` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '字段内容',
  `sort` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  KEY `idx_field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字段内容表';
/* ||-_-||qs_field_value||-_-|| */

DROP TABLE IF EXISTS `qs_form_template`;
CREATE TABLE `qs_form_template` (
  `template_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_name` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '模板名称',
  `template_desc` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '模板描述',
  `source` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源:1|求职登记',
  `add_admin_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '新增人ID',
  `add_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '新增时间',
  `update_admin_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改人ID',
  `update_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `is_use` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否使用:0|否,1|是',
  `is_del` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除:0|否',
  PRIMARY KEY (`template_id`),
  KEY `idx_source` (`source`),
  KEY `idx_del_use` (`is_del`,`is_use`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表单模板表';
/* ||-_-||qs_form_template||-_-|| */

DROP TABLE IF EXISTS `qs_form_template_field`;
CREATE TABLE `qs_form_template_field` (
  `template_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模板ID',
  `field_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '字段ID',
  `is_must` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否必填:0|否,1|是',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否显示:0|否,1|是',
  `sort` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  KEY `idx_t_f` (`template_id`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表单模板字段表';
/* ||-_-||qs_form_template_field||-_-|| */

DROP TABLE IF EXISTS `qs_template_default_field`;
CREATE TABLE `qs_template_default_field` (
  `template_source` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模板来源:1|求职登记',
  `field_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '字段ID',
  `def_close` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否允许关闭:0|否,1|是',
  `def_must` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否必填:0|自由,1|是',
  KEY `idx_t_f` (`template_source`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模板默认字段表';
/* ||-_-||qs_template_default_field||-_-|| */

DROP TABLE IF EXISTS `qs_job_register`;
CREATE TABLE `qs_job_register` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `handle_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '处理状态:0|否,1|是',
  `add_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '新增时间',
  `remark` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '备注',
  `is_del` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除:0|否',
  PRIMARY KEY (`id`),
  KEY `idx_d_h` (`is_del`,`handle_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='求职登记表';
/* ||-_-||qs_job_register||-_-|| */

DROP TABLE IF EXISTS `qs_job_register_content`;
CREATE TABLE `qs_job_register_content` (
  `register_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登记ID',
  `field_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '字段ID',
  `field_alias` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '字段别名',
  `content` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '字段内容',
  KEY `idx_r_f` (`register_id`,`field_id`),
  KEY `idx_field_id` (`field_id`),
  KEY `idx_field_alias` (`field_alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='求职登记内容表';
/* ||-_-||qs_job_register_content||-_-|| */

DROP TABLE IF EXISTS `qs_recruitment_today`;
CREATE TABLE `qs_recruitment_today` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_name` VARCHAR(30) NOT NULL COMMENT '主题名称',
  `is_display` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '显示状态 0-不显示 1-显示',
  `click` INT NOT NULL DEFAULT 0 COMMENT '点击数',
  `addtime` INT NOT NULL COMMENT '添加时间',
  `refreshtime` INT NOT NULL COMMENT '更新时间',
  `template_id` INT NOT NULL DEFAULT 0 COMMENT '模板id 0-默认',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='今日招聘';
/* ||-_-||qs_recruitment_today||-_-|| */

DROP TABLE IF EXISTS `qs_recruitment_today_company`;
CREATE TABLE `qs_recruitment_today_company` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recruitment_today_id` INT NOT NULL COMMENT '今日招聘id',
  `company_id` INT NOT NULL COMMENT '企业id',
  `addtime` INT NOT NULL COMMENT '添加时间',
  `refreshtime` INT NOT NULL COMMENT '更新时间',
  `data_sources` enum('1','2') NOT NULL DEFAULT 1 COMMENT '数据来源 1:系统添加 2:手动添加',
  `sort_id` INT NOT NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='今日招聘企业';
/* ||-_-||qs_recruitment_today_company||-_-|| */

DROP TABLE IF EXISTS `qs_recruitment_today_config`;
CREATE TABLE `qs_recruitment_today_config` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` VARCHAR(30) NOT NULL COMMENT '配置字段名称',
  `value` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `note` VARCHAR(100) DEFAULT NULL COMMENT '配置说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='今日招聘-基础配置';
/* ||-_-||qs_recruitment_today_config||-_-|| */

DROP TABLE IF EXISTS `qs_job_search_group`;
CREATE TABLE `qs_job_search_group` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` VARCHAR(30) NOT NULL COMMENT '配置字段名称',
  `value` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '配置名称',
  `note` VARCHAR(100) DEFAULT NULL COMMENT '配置说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='求职群';
/* ||-_-||qs_job_search_group||-_-|| */

DROP TABLE IF EXISTS `qs_im_unread_remind`;
CREATE TABLE `qs_im_unread_remind` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `to_uid` INT NOT NULL COMMENT '消息接收方UID',
  `from_uid` INT NOT NULL COMMENT '消息发送方UID',
  `chat_id` VARCHAR(32) NOT NULL COMMENT '聊天ID',
  `message_id` VARCHAR(32) NOT NULL COMMENT '消息ID',
  `message` TEXT COMMENT '消息内容',
  `keyword` VARCHAR(255) NOT NULL COMMENT '留言内容',
  `type` VARCHAR(20) NOT NULL COMMENT '消息类型',
  `remind_mode` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '提醒方式[0:失败;1:微信公众号模板消息;2:短信提醒;]',
  `add_time` INT NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='';
/* ||-_-||qs_im_unread_remind||-_-|| */

/* 商品类型 */
DROP TABLE IF EXISTS `qs_goods_type`; 
CREATE TABLE `qs_goods_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `pid` INT NOT NULL DEFAULT 0 COMMENT '上级id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `simple` CHAR(255) NULL COMMENT '简介',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品类型';
/* ||-_-||qs_goods_type||-_-|| */
/* 商品类型 */
DROP TABLE IF EXISTS `qs_goods_type2`; 
CREATE TABLE `qs_goods_type2` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `pid` INT NOT NULL DEFAULT 0 COMMENT '上级id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `simple` CHAR(255) NULL COMMENT '简介',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品类型';
/* ||-_-||qs_goods_type2||-_-|| */

/* 商品 */
DROP TABLE IF EXISTS `qs_goods`; 
CREATE TABLE `qs_goods` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sort` INT UNSIGNED NOT NULL COMMENT '排序',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `shop` INT UNSIGNED NOT NULL COMMENT '商铺id',
  `examine` CHAR(255) NOT NULL DEFAULT '待审核' COMMENT '审核文本,非空为异常',
  `type` INT NOT NULL COMMENT '类型',
  `type2` INT NOT NULL COMMENT '商户自定义类型',
  `sum` INT NOT NULL COMMENT '库存数量',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `simple` CHAR(255) NOT NULL COMMENT '简介',
  `content` TEXT NOT NULL COMMENT '正文',
  `cover` CHAR(255) NOT NULL COMMENT '封面',
  `banner` VARCHAR(510) NOT NULL COMMENT '横幅',
  `expense` decimal(10,2) UNSIGNED NOT NULL COMMENT '价格',
  `freight` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '运费',
  `enable_points_deduct` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '可积分抵扣0否1可2部',
  `deduct_max` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '可部分抵扣最大额',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示1是2否',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',

  `seo_title` CHAR(255) NOT NULL COMMENT 'seo标题',
  `seo_desc` CHAR(255) NOT NULL COMMENT 'seo简介',
  `seo_keywords` CHAR(255) NOT NULL COMMENT 'seo关键字',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品';
/* ||-_-||qs_goods||-_-|| */

/* 题库 */
DROP TABLE IF EXISTS `qs_exam`; 
CREATE TABLE `qs_exam` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `sort` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `is_display` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
  `type` INT NOT NULL COMMENT '类型',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `content` MEDIUMTEXT NOT NULL COMMENT '试题内容',
  `score` INT NOT NULL COMMENT '总分',
  `other` TEXT NOT NULL COMMENT '其他',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='题库';
/* ||-_-||qs_exam||-_-|| */
/* 试题类型 */
DROP TABLE IF EXISTS `qs_exam_type`; 
CREATE TABLE `qs_exam_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `pid` INT NOT NULL DEFAULT 0 COMMENT '上级id',
  `is_display` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `simple` TEXT NULL COMMENT '简介',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='试题类型';
/* ||-_-||qs_exam_type||-_-|| */
/* 答题 */
DROP TABLE IF EXISTS `qs_exam_answer`; 
CREATE TABLE `qs_exam_answer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `exam` INT NOT NULL COMMENT '题库id',
  `note` CHAR(255) NOT NULL COMMENT '备注',
  `name` CHAR(32) NOT NULL COMMENT '试卷名称',
  `content` MEDIUMTEXT NOT NULL COMMENT '答题内容',
  `score` INT NOT NULL COMMENT '总分',
  `state` INT NOT NULL COMMENT '状态',
  `correct` INT NOT NULL COMMENT '得分',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='答题';
/* ||-_-||qs_exam_answer||-_-|| */

/* 虾时光 */
DROP TABLE IF EXISTS `qs_xtime`; 
CREATE TABLE `qs_xtime` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `is_display` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否显示',
  `display_type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '可见值0:自己可见;1:部分可见;2:全部可见',
  `sum` INT NOT NULL DEFAULT 0 COMMENT '浏览量',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `content` MEDIUMTEXT NOT NULL COMMENT '正文富文本',
  `other` TEXT NOT NULL COMMENT '其他',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='虾时光';
/* ||-_-||qs_xtime||-_-|| */

/* 问答 */
DROP TABLE IF EXISTS `qs_qa`; 
CREATE TABLE `qs_qa` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `expense` INT UNSIGNED NOT NULL COMMENT '价格',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '问答',
  `answer` INT UNSIGNED  NOT NULL DEFAULT 0 COMMENT '答案id',
  `target` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '邀请对方uid',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `content` MEDIUMTEXT NOT NULL COMMENT '正文富文本',
  `other` VARCHAR(2048) NOT NULL COMMENT '其他',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='问答';
/* ||-_-||qs_qa||-_-|| */

/** 用户信息 */
DROP TABLE IF EXISTS `qs_member_info`; 
CREATE TABLE `qs_member_info` (
  `id` INT UNSIGNED NOT NULL COMMENT '用户id',
  `card` CHAR(18) NOT NULL DEFAULT '' COMMENT '身份证',
  `auth_face` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '人脸验证',
  `auth_card` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '身份证验证',
  `auth_weixin` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '微信转款验证',

  `online` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否在线',
  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  `photo` CHAR(128) NOT NULL DEFAULT '' COMMENT '头像',
  `addr` VARCHAR(2048) NOT NULL DEFAULT '[]' COMMENT '地址',
  `nickname` CHAR(64) NOT NULL DEFAULT '' COMMENT '昵称',
  `weixin` CHAR(64) NOT NULL DEFAULT '' COMMENT '微信号',
  `background` CHAR(255) NOT NULL DEFAULT '' COMMENT '背景',
  `autograph` CHAR(255) NOT NULL DEFAULT '' COMMENT '签名',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户信息';
/* ||-_-||qs_member_info||-_-|| */

/* 戏说财税 */
DROP TABLE IF EXISTS `qs_entertain`; 
CREATE TABLE `qs_entertain` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '戏说财税',
  `entertain` INT NOT NULL DEFAULT 0 COMMENT '隶属id;评论需要设置此值为目标id',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `content` MEDIUMTEXT NOT NULL COMMENT '正文富文本',
  `other` VARCHAR(2048) NOT NULL COMMENT '其他',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='戏说财税';
/* ||-_-||qs_entertain||-_-|| */

/* 财税游戏 */
DROP TABLE IF EXISTS `qs_games`; 
CREATE TABLE `qs_games` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `sort_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '财税游戏',
  `games` INT NOT NULL DEFAULT 0 COMMENT '隶属id;评论需要设置此值为目标id',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `cover` CHAR(255) NOT NULL COMMENT '封面',
  `link` CHAR(255) NOT NULL COMMENT '名称',
  `content` MEDIUMTEXT NOT NULL COMMENT '正文富文本',
  `other` VARCHAR(2048) NOT NULL COMMENT '其他',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='财税游戏';
/* ||-_-||qs_games||-_-|| */

/* 免费学习 */
DROP TABLE IF EXISTS `qs_study`; 
CREATE TABLE `qs_study` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `sort` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `type` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '免费学习',
  `is_caishui` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否财税',
  `is_video` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否视频',
  `expense` INT NOT NULL COMMENT '价格:正数增加,负数减少,扣除',
  `simple` VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '简介',
  `simple_teacher` VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '简介-讲师',
  `video` VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '视频url',
  `cover` VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '封面url',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `content` MEDIUMTEXT NOT NULL COMMENT '正文富文本',
  `other` VARCHAR(2048) NOT NULL COMMENT '其他',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='免费学习';
/* ||-_-||qs_study||-_-|| */
/* 免费学习_分类 */
DROP TABLE IF EXISTS `qs_study_type`; 
CREATE TABLE `qs_study_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '是否显示',
  `name` CHAR(32) NOT NULL COMMENT '名称',
  `simple` VARCHAR(1024) NOT NULL COMMENT '简介',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='免费学习_分类';
/* ||-_-||qs_study_type||-_-|| */

/* 购物车 */
DROP TABLE IF EXISTS `qs_cart`; 
CREATE TABLE `qs_cart` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `status` TINYINT NOT NULL DEFAULT 0 COMMENT '状态;0:创建; 1:已完成,无法删除',
  `goods` TEXT NOT NULL COMMENT '商品id数组',
  `expense` INT NOT NULL COMMENT '订单价格',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车';
/* ||-_-||qs_cart||-_-|| */

/* 提交需求-留言 */
DROP TABLE IF EXISTS `qs_apply`; 
CREATE TABLE `qs_apply` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `status` TINYINT NOT NULL DEFAULT 0 COMMENT '状态;0:未处理',
  `create` CHAR(32) NOT NULL DEFAULT '' COMMENT '创建人id',
  `type` CHAR(32) NOT NULL COMMENT '类型',
  `content` TEXT NOT NULL COMMENT '正文',
  `other` TEXT NOT NULL COMMENT 'json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='申请';
/* ||-_-||qs_apply||-_-|| */

/* 合同 */
DROP TABLE IF EXISTS `qs_contract`; 
CREATE TABLE `qs_contract` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `state` CHAR(16) NOT NULL DEFAULT 0 COMMENT '步骤',
  `order` INT NOT NULL DEFAULT 0 COMMENT '付款订单',
  `points` INT NOT NULL DEFAULT 0 COMMENT '金额(点券)',

  `type` CHAR(16) NOT NULL COMMENT '类型', /* 1:代招人才; 2:代找工作; 3:找代账; 4:实操实习; 5:财务外包; 6:保就业; 7:劳务派遣用工; 8:劳务派遣就业; 9:人才测评; 10:背景调查; */
  `endtime` INT UNSIGNED NOT NULL COMMENT '截止时间',
  `files` TEXT NOT NULL COMMENT '合同url',

  `contacts` CHAR(16) NOT NULL COMMENT '联系人',
  `tel` CHAR(16) NOT NULL COMMENT '联系电话',
  `other` TEXT NOT NULL COMMENT '其他json',

  `appraise` CHAR(255) NOT NULL COMMENT '评价',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='合同';
/* ||-_-||qs_contract||-_-|| */

/* 银行 */
DROP TABLE IF EXISTS `qs_bank`; 
CREATE TABLE `qs_bank` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `cover` CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
  `content` TEXT NOT NULL COMMENT '正文json',
  `server` CHAR(64) NOT NULL DEFAULT '' COMMENT '客服id',
  `tel` CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
  `category_district` INT UNSIGNED NOT NULL COMMENT '行政区域id',
  `pid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='银行';
/* ||-_-||qs_bank||-_-|| */

/* 专家库 */
DROP TABLE IF EXISTS `qs_expert`; 
CREATE TABLE `qs_expert` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '是否显示',
  `create` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '归属用户',

  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `cover` CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
  `content` TEXT NOT NULL COMMENT '正文',
  `tel` CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
  `category_district` INT UNSIGNED NOT NULL COMMENT '行政区域id',
  `trade` INT UNSIGNED NOT NULL COMMENT '行业',
  `other` TEXT NOT NULL COMMENT '其他json',
 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专家库';
/* ||-_-||qs_expert||-_-|| */

/* 公司转让 */
DROP TABLE IF EXISTS `qs_company_transfer`; 
CREATE TABLE `qs_company_transfer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `change_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `click` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览量',
  `create` INT UNSIGNED NOT NULL COMMENT '创建者id',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `type` CHAR(32) NOT NULL DEFAULT '' COMMENT '企业类型',
  `points` BIGINT NOT NULL DEFAULT 0 COMMENT '转让价格',
  `cover` CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
  `reg_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '成立时间',
  `end_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期至',
  `category` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属行业',
  `regcap` CHAR(16) NOT NULL DEFAULT '' COMMENT '注册资本',
  `business` VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '经营范围',
  `taxpayer` CHAR(16) NOT NULL DEFAULT '' COMMENT '纳税人类型',
  `addr` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册地',
  `contacts` CHAR(64) NOT NULL DEFAULT '' COMMENT '联系人',
  `tel` CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
  `content` TEXT NOT NULL COMMENT '其他说明',
  `other` TEXT NOT NULL COMMENT '其他说明',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公司转让';
/* ||-_-||qs_company_transfer||-_-|| */
/* 公司转让类型 */
DROP TABLE IF EXISTS `qs_company_transfer_type`; 
CREATE TABLE `qs_company_transfer_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公司转让类型';
/* ||-_-||qs_company_transfer_type||-_-|| */
/* 公司求购 */
DROP TABLE IF EXISTS `qs_company_buy`; 
CREATE TABLE `qs_company_buy` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `change_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `click` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览量',
  `create` INT UNSIGNED NOT NULL COMMENT '创建者id',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `type` CHAR(32) NOT NULL DEFAULT '' COMMENT '企业类型',
  `min` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '价格',
  `max` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '价格',
  `reg_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '成立时间',
  `end_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期至',
  `category` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属行业',
  `regcap` CHAR(16) NOT NULL DEFAULT '' COMMENT '注册资本',
  `business` VARCHAR(1024) NOT NULL DEFAULT '' COMMENT '经营范围',
  `taxpayer` CHAR(16) NOT NULL DEFAULT '' COMMENT '纳税人类型',
  `addr` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册地',
  `contacts` CHAR(64) NOT NULL DEFAULT '' COMMENT '联系人',
  `tel` CHAR(16) NOT NULL DEFAULT '' COMMENT '电话',
  `content` TEXT NOT NULL COMMENT '其他说明',
  `other` TEXT NOT NULL COMMENT '其他',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公司转让';
/* ||-_-||qs_company_buy||-_-|| */


/* 商标转让 */
DROP TABLE IF EXISTS `qs_trademark_transfer`; 
CREATE TABLE `qs_trademark_transfer` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `change_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `click` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览量',
  `create` INT UNSIGNED NOT NULL COMMENT '创建者id',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `type` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册类别',
  `points` BIGINT NOT NULL DEFAULT 0 COMMENT '转让价格',
  `cover` CHAR(255) NOT NULL DEFAULT '' COMMENT '封面url',
  `reg_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '成立时间',
  `end_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期至',
  `tra_type` CHAR(16) NOT NULL DEFAULT '' COMMENT '商标类型',
  `contacts` CHAR(64) NOT NULL DEFAULT '' COMMENT '联系人',
  `tel` CHAR(16) NOT NULL DEFAULT '' COMMENT '联系人电话',
  `content` TEXT NOT NULL COMMENT '其他说明',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公司转让';
/* ||-_-||qs_trademark_transfer||-_-|| */
/* 商标转让类型 */
DROP TABLE IF EXISTS `qs_trademark_transfer_type`; 
CREATE TABLE `qs_trademark_transfer_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上级id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商标转让类型';
/* ||-_-||qs_trademark_transfer_type||-_-|| */
/* 商标求购 */
DROP TABLE IF EXISTS `qs_trademark_buy`; 
CREATE TABLE `qs_trademark_buy` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `change_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `click` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览量',
  `create` INT UNSIGNED NOT NULL COMMENT '创建者id',
  `is_display` TINYINT NOT NULL DEFAULT 1 COMMENT '软删除',
  `name` CHAR(32) NOT NULL DEFAULT '' COMMENT '名称',
  `type` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册类别',
  `min` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '价格',
  `max` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '价格',
  `reg_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '成立时间',
  `end_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期至',
  `tra_type` CHAR(16) NOT NULL DEFAULT '' COMMENT '商标类型',
  `contacts` CHAR(64) NOT NULL DEFAULT '' COMMENT '联系人',
  `tel` CHAR(16) NOT NULL DEFAULT '' COMMENT '联系人电话',
  `content` TEXT NOT NULL COMMENT '其他说明',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公司转让';
/* ||-_-||qs_trademark_buy||-_-|| */


/* 转发 */
DROP TABLE IF EXISTS `qs_reprint`; 
CREATE TABLE `qs_reprint` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`model`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='转发';
/* ||-_-||qs_reprint||-_-|| */
/* 收藏 */
DROP TABLE IF EXISTS `qs_favorites`; 
CREATE TABLE `qs_favorites` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`model`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收藏';
/* ||-_-||qs_hifavorites||-_-|| */
/* 评论 */
DROP TABLE IF EXISTS `qs_comment`; 
CREATE TABLE `qs_comment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',
  `content` TEXT COMMENT '正文',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评论';
/* ||-_-||qs_comment||-_-|| */
/* 举报 */
DROP TABLE IF EXISTS `qs_report`; 
CREATE TABLE `qs_report` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',
  `content` TEXT COMMENT '正文',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`model`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='举报';
/* ||-_-||qs_report||-_-|| */
/* 点赞 */
DROP TABLE IF EXISTS `qs_likes`; 
CREATE TABLE `qs_likes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`model`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='点赞';
/* ||-_-||qs_likes||-_-|| */
/* 黑名单 */
DROP TABLE IF EXISTS `qs_blacklist`; 
CREATE TABLE `qs_blacklist` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `mid` CHAR(32) NOT NULL COMMENT '拉黑人的id',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='黑名单';
/* ||-_-||qs_blacklist||-_-|| */
/** 购买本站虚拟 */
DROP TABLE IF EXISTS `qs_buy`;
CREATE TABLE `qs_buy` (
  `id` INT UNSIGNED NOT NULL NOT NULL AUTO_INCREMENT,
  `addtime` INT UNSIGNED NOT NULL NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL NOT NULL COMMENT '创建人',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',
  `content` TEXT NOT NULL COMMENT '其他json',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购买本站虚拟';
/* ||-_-||qs_buy||-_-|| */
/* 访客 */
DROP TABLE IF EXISTS `qs_visitor`; 
CREATE TABLE `qs_visitor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`model`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='访客';
/* ||-_-||qs_visitor||-_-|| */
/* 记录 */
DROP TABLE IF EXISTS `qs_history`; 
CREATE TABLE `qs_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`model`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='记录';
/* ||-_-||qs_history||-_-|| */
/* 报名 */
DROP TABLE IF EXISTS `qs_sign`; 
CREATE TABLE `qs_sign` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `model` CHAR(16) NOT NULL COMMENT '模块名,帕斯卡,首字母大写驼峰',
  `mid` CHAR(32) NOT NULL COMMENT '模块对应Id',

  PRIMARY KEY (`id`),
  UNIQUE KEY `_single` (`create`,`model`,`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='报名';
/* ||-_-||qs_sign||-_-|| */

/* 记事本 */
DROP TABLE IF EXISTS `qs_notepad`; 
CREATE TABLE `qs_notepad` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
  `time` CHAR(16) DEFAULT '' COMMENT '时间',
  `create` INT UNSIGNED COMMENT '创建人id',
  `content` TEXT COMMENT '正文json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='记事本';
/* ||-_-||qs_notepad||-_-|| */
/* 提醒 */
DROP TABLE IF EXISTS `qs_remid`; 
CREATE TABLE `qs_remid` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
  `date` CHAR(16) DEFAULT '' COMMENT '日期',
  `time` CHAR(16) DEFAULT '' COMMENT '时间',
  `type` INT UNSIGNED DEFAULT 0 COMMENT '类型',
  `create` INT UNSIGNED COMMENT '创建人id',
  `content` TEXT COMMENT '正文json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='记事本';
/* ||-_-||qs_remid||-_-|| */

/* 企服 */
DROP TABLE IF EXISTS `qs_service`; 
CREATE TABLE `qs_service` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
  `change_time` INT UNSIGNED DEFAULT 0 COMMENT '修改时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
  `type` INT UNSIGNED NOT NULL COMMENT '类型',
  `typec` CHAR(16) NOT NULL COMMENT '子类型',
  `points` INT UNSIGNED NOT NULL COMMENT '价格',
  `files` TEXT NOT NULL COMMENT '文件json',
  `content` TEXT NOT NULL COMMENT '正文json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企服';
/* ||-_-||qs_service||-_-|| */
/* 企服类型 */
DROP TABLE IF EXISTS `qs_service_type`; 
CREATE TABLE `qs_service_type` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
  `name` CHAR(16) DEFAULT '' COMMENT '名称',
  `img` CHAR(64) DEFAULT '' COMMENT '图片url',
  `status` VARCHAR(1024) NOT NULL COMMENT '步骤名',
  `content` TEXT NOT NULL COMMENT '子菜单',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企服类型';
/* ||-_-||qs_service_type||-_-|| */

/* 证书 */
DROP TABLE IF EXISTS `qs_certificate`; 
CREATE TABLE `qs_certificate` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `cover` CHAR (255) NOT NULL DEFAULT '' COMMENT '证书url',
  `content` TEXT NOT NULL COMMENT '正文内容',
  `other` TEXT NOT NULL COMMENT '其他json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='证书';
/* ||-_-||qs_certificate||-_-|| */

/* 发起投票 */
DROP TABLE IF EXISTS `qs_vote`; 
CREATE TABLE `qs_vote` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `multiple` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否多选',
  `name` CHAR(64) NOT NULL COMMENT '名称',
  `content` TEXT NOT NULL COMMENT '正文内容',
  `other` TEXT NOT NULL COMMENT '选项array',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='发起投票';
/* ||-_-||qs_vote||-_-|| */
/* 投票 */
DROP TABLE IF EXISTS `qs_vote_choice`; 
CREATE TABLE `qs_vote_choice` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `mid` INT UNSIGNED NOT NULL COMMENT 'vote id',
  `choice` INT UNSIGNED NOT NULL COMMENT '选项',

  UNIQUE KEY `_single` (`create`,`mid`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票';
/* ||-_-||qs_vote_choice||-_-|| */

/** 商店信息 */
DROP TABLE IF EXISTS `qs_shop`; 
CREATE TABLE `qs_shop` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '用户id',
  `sort` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否通过',
  `evaluate` TINYINT NOT NULL DEFAULT 100 COMMENT '评价(满分100)',
  `service` TINYINT NOT NULL DEFAULT 100 COMMENT '服务(满分100)',
  `logistics` TINYINT NOT NULL DEFAULT 100 COMMENT '物流(满分100)',

  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  `photo` CHAR(128) NOT NULL DEFAULT '' COMMENT '头像',
  `addr` VARCHAR(2048) NOT NULL DEFAULT '[]' COMMENT '地址',
  `credit` CHAR(64) NOT NULL DEFAULT '' COMMENT '信用代码',
  `legal` CHAR(64) NOT NULL DEFAULT '' COMMENT '法人',
  `email` CHAR(64) NOT NULL DEFAULT '' COMMENT '邮箱',
  `weixin` CHAR(64) NOT NULL DEFAULT '' COMMENT '微信号',
  `background` CHAR(255) NOT NULL DEFAULT '' COMMENT '背景',
  `autograph` CHAR(255) NOT NULL DEFAULT '' COMMENT '签名',
  `other` TEXT NOT NULL COMMENT '其他json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商店信息';
/* ||-_-||qs_shop||-_-|| */

/** 广告 */
DROP TABLE IF EXISTS `qs_ads`; 
CREATE TABLE `qs_ads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `sort` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `is_display` TINYINT(1) UNSIGNED NOT NULL COMMENT '是否显示',

  `type` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型',
  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  `content` TEXT NOT NULL COMMENT '广告内容正文',
  `img` CHAR(255) NOT NULL DEFAULT '' COMMENT '图片url',
  `url` CHAR(255) NOT NULL DEFAULT '' COMMENT '跳转url',
  `other` TEXT NOT NULL COMMENT '其他json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告';
/* ||-_-||qs_ads||-_-|| */
/** 广告类型 */
DROP TABLE IF EXISTS `qs_ads_type`; 
CREATE TABLE `qs_ads_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  `model_name` CHAR(64) NOT NULL DEFAULT '' COMMENT '模块名',
  `model` CHAR(64) NOT NULL DEFAULT '' COMMENT '模块',
  `type` CHAR(64) NOT NULL DEFAULT '' COMMENT '模块类型',
  `content` TEXT NOT NULL COMMENT '模块id组',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_name` (`name`),
  UNIQUE KEY `index_model` (`model`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告类型';
/* ||-_-||qs_ads_type||-_-|| */

/** 聊天 */
DROP TABLE IF EXISTS `qs_chat`; 
CREATE TABLE `qs_chat` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者',
  `target` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '目标用户',
  `type` CHAR(16) NOT NULL COMMENT '类型,接口type获得',
  `new` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '新消息',
  `content` CHAR(255) NOT NULL COMMENT '正文',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天';
/* ||-_-||qs_chat||-_-|| */

/** 维权 */
DROP TABLE IF EXISTS `qs_weiquan`; 
CREATE TABLE `qs_weiquan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者',
  `state` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
  `evidence` TEXT NOT NULL COMMENT '证据',
  `mobile` CHAR(16) NOT NULL DEFAULT 0 COMMENT '电话',
  `reason` TEXT NOT NULL COMMENT '原因',
  `content` CHAR(255) NOT NULL COMMENT '正文',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='维权';
/* ||-_-||qs_weiquan||-_-|| */

/** 文档 */
DROP TABLE IF EXISTS `qs_files`; 
CREATE TABLE `qs_files` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `create` INT UNSIGNED NOT NULL COMMENT '创建者',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `sort` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `is_display` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否显示',

  `type` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型',
  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  `content` TEXT NOT NULL COMMENT '文档内容正文',
  `url` CHAR(255) NOT NULL DEFAULT '' COMMENT '跳转url',
  `other` TEXT NOT NULL COMMENT '其他json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文档';
/* ||-_-||qs_files||-_-|| */
/** 文档类型 */
DROP TABLE IF EXISTS `qs_files_type`; 
CREATE TABLE `qs_files_type` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '父id',
  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文档类型';
/* ||-_-||qs_files_type||-_-|| */

/** 图片库 */
DROP TABLE IF EXISTS `qs_imgs`; 
CREATE TABLE `qs_imgs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sort` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  `url` CHAR(255) NOT NULL DEFAULT '' COMMENT '完整URL',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='图片库';
/* ||-_-||qs_imgs||-_-|| */

/** 认证 */
DROP TABLE IF EXISTS `qs_auth`; 
CREATE TABLE `qs_auth` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `uid` INT UNSIGNED NOT NULL COMMENT '用户id',
  `type` CHAR(255) NOT NULL DEFAULT '' COMMENT '认证类型',
  `files` TEXT NOT NULL COMMENT '相关认证文件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='认证';
/* ||-_-||qs_auth||-_-|| */


/** 模块对照表 */
DROP TABLE IF EXISTS `qs_model`; 
CREATE TABLE `qs_model` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` CHAR(64) NOT NULL DEFAULT '' COMMENT '名称',
  `model` CHAR(64) NOT NULL DEFAULT '' COMMENT '模块名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模块对照表';
/* ||-_-||qs_model||-_-|| */


/* 记账往来 */
DROP TABLE IF EXISTS `qs_bill`; 
CREATE TABLE `qs_bill` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED DEFAULT 0 COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `account` INT UNSIGNED NOT NULL COMMENT '归属账户',
  `date` INT UNSIGNED NOT NULL COMMENT '时间',
  `desc` CHAR (255) NOT NULL DEFAULT '' COMMENT '描述',
  `pay_type` TINYINT NOT NULL DEFAULT 0 COMMENT '支付方式;0现金1支付宝2微信',
  `receivable` INT NOT NULL COMMENT '应收(单位分)',
  `net_receipts` INT NOT NULL COMMENT '实收(单位分)',
  `owe` INT NOT NULL COMMENT '欠收(单位分)',
  `imgs` VARCHAR(2048) NOT NULL COMMENT '',
  `customer` INT UNSIGNED NOT NULL COMMENT '往来单位',
  `mold` TINYINT NOT NULL COMMENT '模式;0收入1支出',
  `other` TEXT NOT NULL COMMENT '其他json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='记账往来';
/* ||-_-||qs_bill||-_-|| */
/* 往来单位 */
DROP TABLE IF EXISTS `qs_customer`; 
CREATE TABLE `qs_customer` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `type` INT UNSIGNED NOT NULL COMMENT '类型;1客户2供应商3收支大类',
  `name` CHAR(16) NOT NULL COMMENT '名称',
  `headerpic` CHAR(64) NOT NULL DEFAULT '' COMMENT '头像url',
  `account` INT UNSIGNED NOT NULL COMMENT '归属账户',
  `other` TEXT NOT NULL COMMENT '其他json',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='往来单位';
/* ||-_-||qs_customer||-_-|| */
/* 新增账户 */
DROP TABLE IF EXISTS `qs_account`; 
CREATE TABLE `qs_account` (
  `id` INT UNSIGNED AUTO_INCREMENT COMMENT 'id',
  `addtime` INT UNSIGNED NOT NULL COMMENT '创建时间',
  `create` INT UNSIGNED NOT NULL COMMENT '创建人id',
  `name` CHAR(16) NOT NULL COMMENT '账户名称',
  `merchant` CHAR(16) NOT NULL COMMENT '商户名称',
  `contacts` CHAR(16) NOT NULL COMMENT '联系人',
  `mobile` CHAR(16) NOT NULL COMMENT '手机号',
  `address` INT UNSIGNED NOT NULL COMMENT '地址id',
  `address_info` CHAR(255) NOT NULL COMMENT '地址详情',
  `business_license` CHAR(64) NOT NULL COMMENT '营业执照url',
  `idcard` CHAR(20) NOT NULL COMMENT '身份证号码',
  `idcard_a` CHAR(64) NOT NULL COMMENT '身份证a URL',
  `idcard_b` CHAR(64) NOT NULL COMMENT '身份证b URL',
  `tel` CHAR(64) NOT NULL COMMENT '电话',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='往来单位';
/* ||-_-||qs_account||-_-|| */
