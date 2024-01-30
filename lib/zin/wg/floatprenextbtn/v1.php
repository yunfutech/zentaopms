<?php
declare(strict_types=1);
namespace zin;

class floatPreNextBtn extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'preLink?:string',
        'nextLink?:string'
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
    /**
     * @return \zin\wg|mixed[]
     */
    protected function build()
    {
        global $app;
        $preLink  = $this->prop('preLink');
        $nextLink = $this->prop('nextLink');

        return array
        (
            !empty($preLink) ? btn
            (
                setID('preButton'),
                set::url($preLink),
                setClass('float-btn fixed z-10'),
                set::icon('angle-left'),
                set('data-app', $app->tab)
            ) : null,
            !empty($nextLink) ? btn
            (
                setID('nextButton'),
                set::url($nextLink),
                setClass('float-btn fixed z-10'),
                set::icon('angle-right'),
                set('data-app', $app->tab)
            ) : null
        );
    }
}
