<?php
declare(strict_types=1);
namespace zin;

class modalNextStep extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'tip: string',
        'items: array'
    );

    /**
     * @return string|false
     */
    public static function getPageCSS()
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    protected function build(): wg
    {
        $tip = $this->prop('tip');
        $items = $this->prop('items');

        return modalDialog
        (
            setClass('modal-next-step'),
            row
            (
                set::align('center'),
                setClass('gap-2'),
                center
                (
                    setClass('w-8 h-8 rounded-full success'),
                    icon(setClass('text-xl font-bold'), 'check')
                ),
                span
                (
                    setClass('font-medium text-md'),
                    $tip
                )
            ),
            set::footerActions($items)
        );
    }
}
