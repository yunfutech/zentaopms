<?php
declare(strict_types=1);
namespace zin;

class textarea extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'name?: string',
        'id?: string',
        'class?: string',
        'required?: bool',
        'placeholder?: string',
        'rows?: int',
        'cols?: int',
        'value?: string'
    );

    /**
     * @var mixed[]
     */
    protected static $defaultProps = array(
        'class' => 'form-control',
        'rows' => 10
    );

    /**
     * @param mixed[]|string|\zin\wg|\zin\directive $child
     */
    protected function onAddChild($child)
    {
        if(is_string($child) && !$this->props->has('value'))
        {
            $this->setProp('value', $child);
            return false;
        }

        return $child;
    }

    protected function build(): wg
    {
        return h::textarea
        (
            set($this->props->pick(array('name', 'id', 'class', 'required', 'placeholder', 'rows', 'cols', 'disabled'))),
            $this->prop('value'),
            $this->children()
        );
    }
}
