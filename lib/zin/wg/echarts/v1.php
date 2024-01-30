<?php
declare(strict_types=1);
namespace zin;

class echarts extends wg
{
    /**
     * @param string|int $width
     * @param string|int $height
     */
    public function size($width, $height): echarts
    {
        if(is_numeric($width))  $width  = "{$width}px";
        if(is_numeric($height)) $height = "{$height}px";
        $this->setProp('_size', array($width, $height));
        return $this;
    }

    /**
     * @param string|mixed[] $value
     */
    public function theme($value): echarts
    {
        $this->setProp('theme', $value);
        return $this;
    }

    public function responsive(bool $value): echarts
    {
        $this->setProp('responsive', $value);
        return $this;
    }

    protected function build(): zui
    {
        global $app;
        $jsFile = $app->getWebRoot() . 'js/echarts/echarts.common.min.js';

        return zui::echarts(inherit($this), set::_call("~((name,selector,options) => $.getLib('$jsFile', {root: false}, () => zui.create(name,selector,options)))"));
    }
}
