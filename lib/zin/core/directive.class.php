<?php
declare(strict_types=1);
/**
 * The directive class file of zin lib.
 *
 * @copyright   Copyright 2023 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @version     $Id
 * @link        https://www.zentao.net
 */

namespace zin;

require_once __DIR__ . DS . 'zin.class.php';

class directive
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var mixed[]|null
     */
    public $options;

    /**
     * @var \zin\wg|null
     */
    public $parent;

    /**
     * Construct a directive object
     * @param  string $type
     * @param  mixed  $data
     * @param  array  $options
     * @access public
     */
    public function __construct(string $type, $data, ?array $options = null)
    {
        $this->type    = $type;
        $this->data    = $data;
        $this->options = $options;

        zin::renderInGlobal($this);
    }

    public function __debugInfo(): array
    {
        return array(
            'type'    => $this->type,
            'data'    => $this->data,
            'options' => $this->options
        );
    }

    /**
     * @param mixed $item
     */
    public static function is($item, ?string $type = null): bool
    {
        return $item instanceof directive && ($type === null || $item->type === $type);
    }
}

function directive($type, $data, $options = null): directive
{
    return new directive($type, $data, $options);
}

/**
 * @param mixed $item
 */
function isDirective($item, ?string $type = null): bool
{
    return directive::is($item, $type);
}