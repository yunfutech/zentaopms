-- 已关闭项目迁移至旧版项目集
UPDATE `zt_project` SET parent = 852, path = concat(',852,', id, ',') WHERE type = 'project' and status = 'closed' and parent = 573;
-- yfint项目迁移至yfint项目集
UPDATE `zt_project` SET parent = 845, path = concat(',845,', id, ',') WHERE id = 579;
-- 财务工作2022迁移至管理
UPDATE `zt_project` SET parent = 843, path = concat(',843,', id, ',') WHERE id = 767;
-- 基础模块项目迁移至yfkm项目集
UPDATE `zt_project` SET parent = 605, path = concat(',605,', id, ',') WHERE id = 580;
-- 禅道oa项目、运维管理、公司管理、财务工作2022迁移至管理项目集
UPDATE `zt_project` SET parent = 843, path = concat(',843,', id, ',') WHERE id in (582, 705, 716, 767);
-- 公司官网、商务管理迁移至营销项目集
UPDATE `zt_project` SET parent = 842, path = concat(',842,', id, ',') WHERE id in (583, 708);
-- 调研管理、日常工作迁移至旧版项目集
UPDATE `zt_project` SET parent = 852, path = concat(',852,', id, ',') WHERE id in (698, 713);