<?php
declare(strict_types=1);
/**
 * The create view file of gitea module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Ke Zhao<zhaoke@easycorp.ltd>
 * @package     gitea
 * @link        https://www.zentao.net
 */

namespace zin;

formPanel(set::id('giteaCreateForm'), set::title($lang->gitea->lblCreate), formRow
(
    formGroup(set::name('name'), set::label($lang->gitea->name), set::value($gitea->name))
), formRow
(
    formGroup(set::name('url'), set::label($lang->gitea->url), set::value($gitea->url))
), formRow
(
    formGroup(set::name('token'), set::label($lang->gitea->token), set::value($gitea->token))
));
