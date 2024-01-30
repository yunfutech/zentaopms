<?php
declare(strict_types=1);
namespace zin;

class riskLabel extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'text?: string', // 标签文本
        'level?: string' // 风险等级：高('high' 或 'h')、中('middle' 或 'm')、低('low' 或 'l')
    );

    /**
     * @return string|false
     */
    public static function getPageCSS()
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    /**
     * @param mixed $child
     * @return mixed
     */
    protected function onAddChild($child)
    {
        if(is_string($child) && !$this->props->has('text'))
        {
            $this->props->set('text', $child);
            return false;
        }
    }

    private function getThemeClass(): string
    {
        $level = $this->prop('level');
        if($level === 'h' || $level === 'high')  return 'risk-high';
        if($level === 'm' || $level === 'middle')  return 'risk-middle';
        return 'risk-low';
    }

    protected function build(): wg
    {
        $text = $this->prop('text');
        return span
        (
            set($this->getRestProps()),
            setClass($this->getThemeClass()),
            $text
        );
    }
}
