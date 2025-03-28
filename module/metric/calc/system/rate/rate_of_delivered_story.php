<?php
/**
 * 按系统统计的研发需求交付率。
 * Rate of delivered story.
 *
 * 范围：system
 * 对象：story
 * 目的：rate
 * 度量名称：按系统统计的研发需求交付率
 * 单位：%
 * 描述：按系统统计的研发需求交付率反映了组织在研发过程中按时交付需求的能力和表现。用于评估组织对于评估交付能力、客户满意度和信任建立、项目管理和资源优化、竞争力和市场表现，以及持续改进和效率提升具有重要意义。
 * 定义：复用：;按系统统计的已交付研发需求数;按系统统计的有效研发需求数;公式：;按系统统计的研发需求完成率=按系统统计的已交付研发需求数/按系统统计的有效研发需求数*100%;
 *
 * @copyright Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @author    zhouxin <zhouxin@easycorp.ltd>
 * @package
 * @uses      func
 * @license   ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @Link      https://www.zentao.net
 */
class rate_of_delivered_story extends baseCalc
{
    public $dataset = 'getDevStories';

    public $fieldList = array('t1.closedReason', 't1.stage');

    public $result = array('valid' => 0, 'delivered' => 0);

    public function calculate($row)
    {
        if(!isset($this->result['delivered'])) $this->result['delivered'] = 0;
        if(!isset($this->result['valid']))    $this->result['valid'] = 0;

        if($row->stage == 'released' || $row->closedReason == 'done')                            $this->result['delivered'] ++;
        if(!in_array($row->closedReason, array('duplicate', 'willnotdo', 'bydesign', 'cancel'))) $this->result['valid'] ++;
    }

    public function getResult($options = array())
    {
        $this->result = !empty($this->result['valid']) ? round($this->result['delivered'] / $this->result['valid'], 4) : 0;
        $records = array(array('value' => $this->result));
        return $this->filterByOptions($records, $options);

    }
}
