# 云孚禅道

## 修改记录

| 修改日期   | 描述                                                                                       | 修改文件                                                                                                                                                                                                                                                                                                                                                               | 修改人 |
| ---------- | ------------------------------------------------------------------------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------ |
| 2022-04-20 | “创建项目”增加“项目顾问、项目经理”两个字段                                             | db/yunfu/2022_04_19_alter_project.sql<br />module/project/lang/zh-cn.php<br />module/project/control.php<br />module/project/view/create.html.php                                                                                                                                                                                                                      | 王鑫鹏 |
| 2022-04-20 | “编辑项目”增加“项目顾问、项目经理”两个字段                                             | module/project/view/edit.html.php<br />module/project/control.php<br />module/user/model.php                                                                                                                                                                                                                                                                           | 王鑫鹏 |
| 2022-04-20 | “项目列表”页面增加“项目顾问、项目经理”两个字段                                         | module/project/view/browselist.html.php<br />module/project/model.php                                                                                                                                                                                                                                                                                                  | 王鑫鹏 |
| 2022-04-20 | “项目设置”页面增加项目顾问和项目经理成员<br />修复团队中未存储项目负责人和项目经理的问题 | module/project/view/view.html.php<br />module/project/control.php<br />module/project/model.php                                                                                                                                                                                                                                                                        | 王鑫鹏 |
| 2022-04-20 | “统计->任务”增加“项目负责人”筛选                                                       | module/report/control.php<br />module/report/js/taskboard.js<br />module/report/lang/zh-cn.php<br />module/report/view/taskboard.html.php                                                                                                                                                                                                                              | 王鑫鹏 |
| 2022-04-21 | 增加周报表和周报菜单                                                                       | config/zentaopms.php<br />db/yunfu/2022_04_18_projectweekly.sql<br />module/common/lang/menu.php<br />module/project/control.php<br />module/project/lang/zh-cn.php                                                                                                                                                                                                    | 王鑫鹏 |
| 2022-04-21 | 增加周报模块                                                                               | extension/projectweekly/                                                                                                                                                                                                                                                                                                                                               | 王鑫鹏 |
| 2022-04-21 | 将项目周报增加至权限配置                                                                   | extension/custom/projectweekly/lang/zh-cn.php<br />module/group/lang/resource.php                                                                                                                                                                                                                                                                                      | 王鑫鹏 |
| 2022-04-22 | 统计增加项目周报看板                                                                       | extension/custom/projectweekly/control.php<br />extension/custom/projectweekly/model.php<br />extension/custom/projectweekly/js/weeklyboard.js<br />extension/custom/projectweekly/lang/zh-cn.php<br />extension/custom/projectweekly/view/weeklyboard.html.php<br />module/common/lang/menu.php<br />module/common/lang/zh-cn.php<br />module/group/lang/resource.php | 王鑫鹏 |

### 详细描述

- “创建项目”增加“项目顾问、项目经理”两个字段
  - db/yunfu/2022_04_19_alter_project.sql
    - 项目表新增“项目负责人、项目顾问”两个字段
- “编辑项目”增加“项目顾问、项目经理”两个字段
  - module/project/view/edit.html.php
    - 前端页面新增“项目负责人、项目顾问”两个字段
- “项目列表”页面增加“项目顾问、项目经理”两个字段
  - module/project/view/browselist.html.phpmodule/project/model.ph
    - 前端页面增加“项目负责人、项目顾问”两个字段，
  - module/project/model.php
    - 返回结果增加“项目负责人、项目顾问”
- “项目设置”页面增加项目顾问和项目经理成员
  - module/project/control.php
    - 修复创建项目时，“项目负责人、项目顾问”未能存储到团队表中的问题
- “统计->任务”增加“项目负责人”筛选
  - module/report/control.php
    - 增加按“项目负责人”筛选
- 增加"周报"表和"周报"菜单
  - db/yunfu/2022_04_18_projectweekly.sql、config/zentaopms.php
    - 新增周报表格
  - module/common/lang/menu.php
    - 增加周报菜单
- 增加周报模块
  - extension/projectweekly/lang/
    - 周报模块中文配置
  - extension/projectweekly/view/
    - 周报模块html文件
  - extension/projectweekly/config.php
    - 周报模块配置
