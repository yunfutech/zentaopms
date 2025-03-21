<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'modaldialog' . DS . 'v1.php';

class modal extends modalDialog
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'id?:string="$GID"',
        'modalProps?:array'
    );

    /**
     * @var mixed[]
     */
    protected static $defaultProps = array(
        'modalProps' => array()
    );

    protected function build(): wg
    {
        list($id, $modalProps) = $this->prop(array('id', 'modalProps'));

        $this->setProp($modalProps);

        return div
        (
            setClass('modal'),
            setID($id),
            set($this->props->skip(array_merge(array_keys($modalProps), array_keys(static::definedPropsList())))),
            parent::build()
        );
    }
}
