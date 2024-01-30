<?php
declare(strict_types=1);
namespace zin;

require_once dirname(__DIR__) . DS . 'input' . DS . 'v1.php';

class fileInput extends input
{
    /**
     * @var mixed[]
     */
    protected static $defaultProps = array(
        'name' => 'file',
        'type' => 'file'
    );
}
