<?php
declare(strict_types=1);
namespace zin;

class label extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'text?:string'
    );

    /**
     * @return string|false
     */
    public static function getPageCSS()
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    public function onAddChild($child)
    {
        if(is_string($child) && !$this->props->has('text'))
        {
            $this->props->set('text', $child);
            return false;
        }
    }

    public function build(): wg
    {
        return span
        (
            setClass('label'),
            set($this->getRestProps()),
            $this->prop('text'),
            $this->children()
        );
    }
}
