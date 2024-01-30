<?php
declare(strict_types=1);
namespace zin;

class floatToolbar extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'prefix?:array',
        'main?:array',
        'suffix?:array',
        'object?:object'
    );

    /**
     * @var mixed[]
     */
    protected static $defineBlocks = array(
        'prefix' => array(),
        'main'   => array(),
        'suffix' => array()
    );

    /**
     * @param \zin\wg|mixed[]|null $wg1
     * @param \zin\wg|mixed[]|null $wg2
     */
    private function buildDivider($wg1, $wg2): ?\zin\wg
    {
        if(empty($wg1) || empty($wg2)) return null;

        return div(setClass('divider w-px self-center h-6 mx-2'));
    }

    private function buildBtns(?array $items): ?array
    {
        if(empty($items)) return null;

        $btns = array();
        foreach ($items as &$item)
        {
            if(!$item) continue;

            if(!empty($item['url']))      $item['url']      = preg_replace_callback('/\{(\w+)\}/', array($this, 'getObjectValue'), $item['url']);
            if(!empty($item['data-url'])) $item['data-url'] = preg_replace_callback('/\{(\w+)\}/', array($this, 'getObjectValue'), $item['data-url']);

            $className = 'ghost';
            if(!empty($item['className'])) $className .= ' ' . $item['className'];
            $btns[] = btn(set($item), setClass($className));
        }
        return $btns;
    }

    public function getObjectValue($matches)
    {
        if(!isset($this->object)) $this->object = $this->prop('object');

        return zget($this->object, $matches[1]);
    }

    /**
     * @param mixed[]|\zin\wg|null $block
     * @return mixed[]|\zin\wg|null
     */
    private function mergeBtns(?array $btns, $block)
    {
        if(empty($btns) && empty($block)) return null;
        if(empty($block)) return $btns;

        if($block[0] instanceof btn)
        {
            $block[0]->add(setClass('ghost'));
        }
        else
        {
            foreach($block[0]->children() as $blockBtn) $blockBtn->add(setClass('ghost'));
        }
        if(empty($btns)) return $block;

        if(!is_array($block)) $block = array($block);
        return array_merge($btns, $block);
    }

    protected function build(): wg
    {
        $prefixBtns = $this->buildBtns($this->prop('prefix'));
        $mainBtns   = $this->buildBtns($this->prop('main'));
        $suffixBtns = $this->buildBtns($this->prop('suffix'));

        $prefixBlock = $this->block('prefix');
        $mainBlock   = $this->block('main');
        $suffixBlock = $this->block('suffix');

        $prefixBtns = $this->mergeBtns($prefixBtns, $prefixBlock);
        $mainBtns   = $this->mergeBtns($mainBtns, $mainBlock);
        $suffixBtns = $this->mergeBtns($suffixBtns, $suffixBlock);

        return div
        (
            setClass('toolbar bg-darker backdrop-blur bg-opacity-60 text-canvas float-toolbar rounded p-1.5'),
            $prefixBtns,
            $this->buildDivider($prefixBtns, $mainBtns),
            $mainBtns,
            $this->buildDivider($mainBtns, $suffixBtns),
            $suffixBtns
        );
    }
}
