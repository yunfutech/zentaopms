<?php
declare(strict_types=1);
/**
 * The zin class file of zin of ZenTaoPMS.
 *
 * @copyright   Copyright 2023 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @version     $Id
 * @link        https://www.zentao.net
 */

namespace zin;

require_once dirname(__DIR__) . DS . 'utils' . DS . 'deep.func.php';

class zin
{
    /**
     * @var mixed[]
     */
    public static $globalRenderList = array();

    /**
     * @var bool
     */
    public static $enabledGlobalRender = true;

    /**
     * @var mixed[]
     */
    public static $data = array();

    /**
     * @var bool
     */
    public static $rendered = false;

    /**
     * @var bool
     */
    public static $rawContentCalled = false;

    /**
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function getData(string $namePath, $defaultValue = null)
    {
        return \zin\utils\deepGet(static::$data, $namePath, $defaultValue);
    }

    /**
     * @param mixed $value
     */
    public static function setData(string $namePath, $value)
    {
        \zin\utils\deepSet(static::$data, $namePath, $value);
    }

    public static function enableGlobalRender()
    {
        static::$enabledGlobalRender = true;
    }

    public static function disableGlobalRender()
    {
        static::$enabledGlobalRender = false;
    }

    public static function renderInGlobal(): bool
    {
        if(!static::$enabledGlobalRender) return false;

        static::$globalRenderList = array_merge(static::$globalRenderList, func_get_args());
        return true;
    }

    public static function getGlobalRenderList(bool $clear = true): array
    {
        $globalItems = array();

        foreach(static::$globalRenderList as $item)
        {
            if(is_object($item))
            {
                if((isset($item->parent) && $item->parent) || ($item instanceof wg && ($item->shortType() === 'wg' || $item->shortType() === 'item')))
                continue;
            }
            $globalItems[] = $item;
        }

        /* Clear globalRenderList. */
        if($clear) static::$globalRenderList = array();

        return $globalItems;
    }
}
