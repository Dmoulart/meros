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
}