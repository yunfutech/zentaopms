<?php
/* Fields. */
$lang->program->id           = '编号';
$lang->program->name         = '项目集名称';
$lang->program->template     = '项目集模板';
$lang->program->category     = '项目集类型';
$lang->program->desc         = '项目集描述';
$lang->program->status       = '状态';
$lang->program->PM           = '负责人';
$lang->program->budget       = '预算';
$lang->program->budgetUnit   = '预算单位';
$lang->program->begin        = '计划开始';
$lang->program->end          = '计划完成';
$lang->program->stage        = '阶段';
$lang->program->type         = '类型';
$lang->program->pri          = '优先级';
$lang->program->parent       = '父项目集';
$lang->program->exchangeRate = '汇率';
$lang->program->openedBy     = '由谁创建';
$lang->program->openedDate   = '创建日期';
$lang->program->closedBy     = '由谁关闭';
$lang->program->closedDate   = '关闭日期';
$lang->program->canceledBy   = '由谁取消';
$lang->program->canceledDate = '取消日期';
$lang->program->team         = '团队';
$lang->program->order        = '排序';
$lang->program->days         = '可用工作日';
$lang->program->acl          = '访问控制';
$lang->program->whitelist    = '白名单';
$lang->program->deleted      = '已删除';

/* Actions. */
$lang->program->common                  = '项目集';
$lang->program->index                   = '项目集主页';
$lang->program->create                  = '添加项目集';
$lang->program->createGuide             = '选择项目模板';
$lang->program->edit                    = '编辑项目集';
$lang->program->browse                  = '项目集列表';
$lang->program->kanbanAction            = '项目集看板';
$lang->program->view                    = '项目集详情';
$lang->program->copy                    = '复制项目集';
$lang->program->product                 = '产品列表';
$lang->program->project                 = '项目集项目列表';
$lang->program->all                     = '所有项目集';
$lang->program->start                   = '启动项目集';
$lang->program->finish                  = '完成项目集';
$lang->program->suspend                 = '挂起项目集';
$lang->program->delete                  = '删除项目集';
$lang->program->close                   = '关闭项目集';
$lang->program->activate                = '激活项目集';
$lang->program->export                  = '导出';
$lang->program->stakeholder             = '干系人列表';
$lang->program->createStakeholder       = '添加干系人';
$lang->program->unlinkStakeholder       = '移除干系人';
$lang->program->batchUnlinkStakeholders = '批量移除干系人';
$lang->program->unlink                  = '移除';
$lang->program->updateOrder             = '排序';
$lang->program->unbindWhitelist         = '移除白名单';
$lang->program->importStakeholder       = '从父项目集导入';
$lang->program->manageMembers           = '项目集团队';
$lang->program->confirmChangePRJUint    = '是否同步更新该项目集下子项目集和项目的预算的单位？若确认更新,请填写今日汇率。';
$lang->program->exRateNotNegative       = '『汇率』不能是负数。';
$lang->program->changePRJUnit           = '更新项目预算单位';

$lang->program->progress         = '项目进度';
$lang->program->children         = '子项目集';
$lang->program->allInvest        = '项目集总投入';
$lang->program->teamCount        = '总人数';
$lang->program->longTime         = '长期';
$lang->program->moreProgram      = '更多项目集';
$lang->program->stakeholderType  = '干系人类型';
$lang->program->parentBudget     = '所属项目集剩余预算：';
$lang->program->isStakeholderKey = '关键干系人';

$lang->program->stakeholderTypeList['inside']  = '内部';
$lang->program->stakeholderTypeList['outside'] = '外部';

$lang->program->noProgram          = '暂时没有项目集';
$lang->program->showClosed         = '显示已关闭';
$lang->program->tips               = '选择了父项目集，则可关联该父项目集下的产品。如果项目未选择任何项目集，则系统会默认创建一个和该项目同名的产品并关联该项目。';
$lang->program->confirmBatchUnlink = "您确定要批量移除这些干系人吗？";
$lang->program->beginLetterParent  = "父项目集的开始日期：%s，开始日期不能小于父项目集的开始日期";
$lang->program->endGreaterParent   = "父项目集的完成日期：%s，完成日期不能大于父项目集的完成日期";
$lang->program->beginGreateChild   = "子项目集的最小开始日期：%s，父项目集的开始日期不能大于子项目集的最小开始日期";
$lang->program->endLetterChild     = "子项目的最大完成日期：%s，父项目的完成日期不能小于子项目的最大完成日期";
$lang->program->closeErrorMessage  = '存在子项目集或项目为未关闭状态';
$lang->program->hasChildren        = '该项目集有子项目集或项目存在，不能删除。';
$lang->program->hasProduct         = '该项目集有产品存在，不能删除。';
$lang->program->confirmDelete      = "您确定要删除吗？";
$lang->program->readjustTime       = '重新调整项目集起止时间';
$lang->program->accessDenied       = '你无权访问该项目集';
$lang->program->beyondParentBudget = '已超出所属项目集的剩余预算';

$lang->program->endList[31]  = '一个月';
$lang->program->endList[93]  = '三个月';
$lang->program->endList[186] = '半年';
$lang->program->endList[365] = '一年';
$lang->program->endList[999] = '长期';

$lang->program->aclList['private'] = "私有（项目集负责人和干系人可访问，干系人可后续维护）";
$lang->program->aclList['open']    = "公开（有项目集视图权限，即可访问）";

$lang->program->subAclList['private'] = "私有（本项目集负责人和干系人可访问，干系人可后续维护）";
$lang->program->subAclList['open']    = "全部公开（有项目集视图权限，即可访问）";
$lang->program->subAclList['program'] = "项目集内公开（所有上级项目集负责人和干系人、本项目集负责人和干系人可访问）";

$lang->program->subAcls['private'] = '私有';
$lang->program->subAcls['open']    = '全部公开';
$lang->program->subAcls['program'] = '项目集内公开';

$lang->program->authList['extend'] = '继承 (取项目权限与组织权限的并集)';
$lang->program->authList['reset']  = '重新定义 (只取项目权限)';

$lang->program->statusList['wait']      = '未开始';
$lang->program->statusList['doing']     = '进行中';
$lang->program->statusList['suspended'] = '已挂起';
$lang->program->statusList['closed']    = '已关闭';

$lang->program->featureBar['all'] = '所有';

$lang->program->kanban = new stdclass();
$lang->program->kanban->common             = '项目集看板';
$lang->program->kanban->typeList['my']     = '我参与的项目集';
$lang->program->kanban->typeList['others'] = '其他项目集';

$lang->program->kanban->openProducts    = '未关闭的产品';
$lang->program->kanban->unexpiredPlans  = '未过期的计划';
$lang->program->kanban->waitingProjects = '未开始的项目';
$lang->program->kanban->doingProjects   = '进行中的项目';
$lang->program->kanban->doingExecutions = '进行中的执行';
$lang->program->kanban->normalReleases  = '正常的发布';

$lang->program->kanban->laneColorList = array('#32C5FF', '#006AF1', '#9D28B2', '#FF8F26', '#FFC20E', '#00A78E', '#7FBB00', '#424BAC', '#C0E9FF', '#EC2761');
