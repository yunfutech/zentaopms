-- 增加迭代类型
ALTER TABLE `zt_project` ADD `project_type` TINYINT(3) NOT NULL DEFAULT '0' AFTER `end`;
