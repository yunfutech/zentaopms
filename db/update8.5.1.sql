CREATE OR REPLACE VIEW `ztv_projectsummary` AS select `zt_task`.`project` AS `project`,sum(if((`zt_task`.`parent` >= '0'),`zt_task`.`estimate`,0)) AS `estimate`,sum(if((`zt_task`.`parent` >= '0'),`zt_task`.`consumed`,0)) AS `consumed`,sum(if(((`zt_task`.`status` <> 'cancel') and (`zt_task`.`status` <> 'closed') and (`zt_task`.`parent` >= '0')),`zt_task`.`left`,0)) AS `left`,count(0) AS `number`,sum(if(((`zt_task`.`status` != 'done') and (`zt_task`.`status` != 'closed')),1,0)) AS `undone`,sum((if((`zt_task`.`parent` >= '0'),`zt_task`.`consumed`,0) + if(((`zt_task`.`status` <> 'cancel') and (`zt_task`.`status` <> 'closed') and (`zt_task`.`parent` >= '0')),`zt_task`.`left`,0))) AS `totalReal` from `zt_task` where (`zt_task`.`deleted` = '0') group by `zt_task`.`project`;