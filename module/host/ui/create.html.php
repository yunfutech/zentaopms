<?php
declare(strict_types=1);
/**
 * The create view file of host module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Ke Zhao<zhaoke@easycorp.ltd>
 * @package     host
 * @link        https://www.zentao.net
 */

namespace zin;

formPanel(set::id('hostCreateForm'), set::title($lang->host->create), formRow
(
    formGroup(set::width('1/3'), set::name('name'), set::label($lang->host->name)),
    formGroup(set::width('1/3'), set::label($lang->host->admin), set::control('picker'), set::name('admin'), set::items($accounts))
), formRow
(
    formGroup(set::width('1/3'), set::label($lang->host->cpuBrand), set::control('picker'), set::name('cpuBrand'), set::items($lang->host->cpuBrandList)),
    formGroup(set::width('1/3'), set::name('cpuModel'), set::label($lang->host->cpuModel))
), formRow
(
    formGroup(set::width('1/3'), set::name('cpuNumber'), set::label($lang->host->cpuNumber)),
    formGroup(set::width('1/3'), set::name('cpuCores'), set::label($lang->host->cpuCores))
), formRow
(
    formGroup(set::width('1/3'), set::name('memory'), set::label($lang->host->memory), set::control(array
    (
        'type'        => 'inputControl',
        'suffix'      => 'GB',
        'suffixWidth' => 40,
    ))),
    formGroup(set::width('1/3'), set::name('diskSize'), set::label($lang->host->diskSize), set::control(array
    (
        'type'        => 'inputControl',
        'suffix'      => 'GB',
        'suffixWidth' => 40,
    )))
), formRow
(
    formGroup(set::width('1/3'), set::label($lang->host->group), set::control('picker'), set::name('group'), set::items($optionMenu)),
    formGroup(set::width('1/3'), set::label($lang->host->serverRoom), set::control('picker'), set::name('serverRoom'), set::items($rooms))
), formRow
(
    formGroup(set::width('1/3'), set::name('serverModel'), set::label($lang->host->serverModel)),
    formGroup
    (
        set::width('1/3'),
        set::label($lang->host->hostType),
        set::control('picker'),
        set::name('hostType'),
        set::items($lang->host->hostTypeList),
        set::value('virtual')
    )
), formRow
(
    formGroup(set::width('1/3'), set::label($lang->host->osName), set::control('picker'), set::name('osName'), set::items($lang->host->osNameList), set::value($osName ? $osName : $host->osName), on::change('osChange')),
    formGroup(set::width('1/3'), set::label($lang->host->osVersion), set::control('picker'), set::name('osVersion'), set::items($lang->host->{"{$osName}List"}))
), formRow
(
    formGroup(set::width('1/3'), set::name('zap'), set::label($lang->host->zap), set::value(8086)),
    formGroup
    (
        set::width('1/3'),
        set::label($lang->host->tags),
        set::control('picker'),
        set::name('vsoft'),
        set::items($lang->host->tagsList),
        set::value('vm')
    )
), formRow
(
    formGroup(set::width('1/3'), set::name('intranet'), set::label($lang->host->intranet)),
    formGroup(set::width('1/3'), set::name('extranet'), set::label($lang->host->extranet))
), formRow
(
    formGroup(set::width('1/3'), set::label($lang->host->provider), set::control('picker'), set::name('provider'), set::items($lang->host->providerList)),
    formGroup(set::width('1/3'), set::name('status'), set::control('radioList'), set::label($lang->host->status), set::value('online'), set::inline(true), set::items($lang->host->statusList))
));
