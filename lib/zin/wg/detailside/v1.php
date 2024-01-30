<?php
declare(strict_types=1);
namespace zin;

class detailSide extends wg
{
    /**
     * @return string|false
     */
    public static function getPageCSS()
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    protected function build(): wg
    {
        return div
        (
            setClass('detail-side flex-none px-6'),
            set($this->getRestProps()),
            $this->children()
        );
    }
}
