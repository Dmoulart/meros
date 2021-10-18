<?php

namespace App\Utils;

class ArrayUtils
{
    static function object_to_array(Object $data): object|array
    {
        if (is_array($data) || is_object($data))
        {
            $result = [];
            foreach ($data as $key => $value)
            {
                $result[$key] = (is_array($data) || is_object($data)) ? ArrayUtils::object_to_array($value) : $value;
            }
            return $result;
        }
        return $data;
    }

    static  function isAssociativeArray(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}