<?php
declare(strict_types=1);
namespace zin\utils;

/**
 * @param object|mixed[] $data
 * @param mixed $defaultValue
 * @return mixed
 */
function deepGet(&$data, string $namePath, $defaultValue = null)
{
    $names = explode('.', $namePath);
    foreach($names as $name)
    {
        if(is_object($data))
        {
            if(!isset($data->$name)) return $defaultValue;
            $data = &$data->$name;
            continue;
        }
        if(!is_array($data) || !isset($data[$name])) return $defaultValue;
        $data = &$data[$name];
    }
    return $data === null ? $defaultValue : $data;
}

/**
 * @param mixed $value
 */
function deepSet(array &$data, string $namePath, $value)
{
    $names = explode('.', $namePath);
    $lastName = array_pop($names);
    if(!empty($names))
    {
        foreach($names as $name)
        {
            if(!is_array($data)) return;

            if(!isset($data[$name])) $data[$name] = array();
            $data = &$data[$name];
        }
    }

    $data[$lastName] = $value;
}
