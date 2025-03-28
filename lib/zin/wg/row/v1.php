<?php
declare(strict_types=1);
namespace zin;

class row extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'justify?: string',
        'align?: string'
    );

    protected function build(): wg
    {
        $classList = 'row';
        list($justify, $align) = $this->prop(array('justify', 'align'));
        if(!empty($justify)) $classList .= ' justify-' . $justify;
        if(!empty($align))   $classList .= ' items-' . $align;

        return div
        (
            setClass($classList),
            set($this->getRestProps()),
            $this->children()
        );
    }
}
