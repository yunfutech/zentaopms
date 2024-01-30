<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'checkbox' . DS . 'v1.php';

class switcher extends checkbox
{
    /**
     * @var mixed[]
     */
    protected static $defaultProps = array
    (
        'typeClass' => 'switch switch'
    );
}
