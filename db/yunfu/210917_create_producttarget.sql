CREATE TABLE `zentao`.`zt_producttarget` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(50) NOT NULL COMMENT '名称' , `target` INT NOT NULL COMMENT '目标进度' , `performance` INT NULL COMMENT '实际进度' , `cause` INT NULL COMMENT '进度偏差原因' , `createdDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `zt_producttarget` ADD `createdBy` VARCHAR(50) NOT NULL COMMENT '创建用户' AFTER `createdDate`, ADD `deleted` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT '是否被删除' AFTER `createdBy`;

CREATE TABLE `zentao`.`zt_producttargetitem` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(500) NOT NULL COMMENT '目标名称' , `intro` TEXT NULL COMMENT '目标说明' , `acceptance` TEXT NOT NULL COMMENT '验收标准' , `completion` TEXT NULL COMMENT '完成情况' , `createdDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `createdBy` VARCHAR(50) NOT NULL , `deleted` ENUM('0','1') NOT NULL DEFAULT '0' , `target` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `zt_producttarget` ADD `middle` INT NULL COMMENT '月中实际进度' AFTER `target`;

ALTER TABLE `zt_producttarget` CHANGE `cause` `cause` TEXT NULL DEFAULT NULL COMMENT '进度偏差原因';

ALTER TABLE `zt_producttarget` ADD `confidence` ENUM('0','1','2') NOT NULL COMMENT '信心指数' AFTER `performance`;

ALTER TABLE `zt_producttarget` ADD `product` INT NOT NULL COMMENT '所属项目' AFTER `name`;

ALTER TABLE `zt_producttarget` ADD `lastTarget` INT NULL COMMENT '上月月末进度' AFTER `product`;