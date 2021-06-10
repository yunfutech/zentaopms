ALTER TABLE `zt_storyspec` ADD `solution` TEXT NULL COMMENT '技术方案' AFTER `verify`;
ALTER TABLE `zt_story` ADD `skill` INT NULL COMMENT '技能' AFTER `status`;