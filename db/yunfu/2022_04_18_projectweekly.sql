CREATE TABLE `zt_projectweekly` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '周报名称',
  `overview` longtext NOT NULL COMMENT '项目概况',
  `question` longtext NOT NULL COMMENT '项目问题',
  `weekjob` longtext NOT NULL COMMENT '本周完成工作',
  `weekplan` longtext NOT NULL COMMENT '下周工作计划',
  `account` varchar(30) NOT NULL COMMENT '创建用户',
  `projectID` mediumint(8) unsigned NOT NULL COMMENT '项目id',
  `createdDatetime` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `project_fk` (`projectID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;