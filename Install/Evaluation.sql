DROP TABLE IF EXISTS `cms_evaluation_category`;
CREATE TABLE `cms_evaluation_category` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` int(11) NOT NULL COMMENT '分类名',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) NOT NULL,
  `show_status` tinyint(11) NOT NULL DEFAULT 1 COMMENT '是否展示 0否 1是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cms_evaluation_config`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int(11) NULL DEFAULT NULL,
  `enable_review_content` tinyint(11) NULL DEFAULT NULL COMMENT '开启评价审核',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

DROP TABLE IF EXISTS `cms_evaluation_content`;
CREATE TABLE `cms_evaluation_content` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `target` varchar(128) NOT NULL DEFAULT '' COMMENT '评论所属对象',
  `target_type` varchar(128) NOT NULL DEFAULT '' COMMENT '评论所属对象类型',
  `from` varchar(128) NOT NULL DEFAULT '' COMMENT '评论来源',
  `from_type` varchar(128) NOT NULL DEFAULT '' COMMENT '评论来源类型',
  `content` text NOT NULL COMMENT '内容',
  `rate` tinyint(2) NOT NULL COMMENT '评分',
  `user_type` varchar(64) NOT NULL DEFAULT '' COMMENT '用户类型',
  `user_id` varchar(64) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_nickname` varchar(128) NOT NULL DEFAULT '' COMMENT '用户呢称',
  `user_avatar` text NOT NULL COMMENT '用户头像',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) NOT NULL COMMENT '删除时间',
  `show_status` tinyint(11) NOT NULL DEFAULT '1' COMMENT '是否展示 0否 1是',
  `images` text NOT NULL COMMENT '图片列表 序列化',
  `review_status` int(11) NOT NULL COMMENT '审核状态 0待审核 1审核通过 2审核不通过',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `cms_evaluation_summary`;
CREATE TABLE `cms_evaluation_summary` (
   `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `target` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '来源',
  `target_type` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '来源类型',
  `total_content` int(11) NOT NULL COMMENT '评价总数',
  `average_rate` decimal(10, 1) NOT NULL COMMENT '平均分',
  `update_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;