<?php
declare(strict_types=1);
namespace zin;

class detailBody extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'isForm?: bool=false'
    );

    /**
     * @var mixed[]
     */
    protected static $defineBlocks = array(
        'main' => array('map' => 'sectionList'),
        'side' => array('map' => 'detailSide'),
        'bottom' => array('map' => 'history,fileList'),
        'floating' => array('map' => 'floatToolbar')
    );

    /**
     * @return string|false
     */
    public static function getPageCSS()
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    /**
     * @return string|false
     */
    public static function getPageJS()
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    protected function build(): wg
    {
        $main     = $this->block('main');
        $side     = $this->block('side');
        $bottom   = $this->block('bottom');
        $floating = $this->block('floating');
        $isForm   = $this->prop('isForm');

        if(!$isForm)
        {
            return div
            (
                setClass('detail-body rounded flex gap-1'),
                set($this->getRestProps()),
                div
                (
                    setClass('col gap-1 grow'),
                    $main,
                    $bottom,
                    empty($floating) ? null : center(setClass('pt-6'), $floating)
                ),
                $side
            );
        }

        return formBase
        (
            set::actionsClass('h-14 flex flex-none items-center justify-center shadow'),
            setClass('detail-body rounded col overflow-y-hidden bg-white'),
            set($this->getRestProps()),
            setStyle('height', 'calc(100vh - 120px)'),
            div
            (
                setClass('flex-auto overflow-y-auto'),
                div
                (
                    setClass('flex'),
                    setStyle('min-height', '100%'),
                    div
                    (
                        setClass('col grow'),
                        $main,
                        $bottom
                    ),
                    div
                    (
                        setClass('w-1'),
                        setStyle('background', 'var(--zt-page-bg)')
                    ),
                    $side
                )
            )
        );
    }
}
