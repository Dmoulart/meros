<?php

namespace App\Utils;

class StringUtils
{
    static function snakeToCamel($input, $capitalizeFirstCharacter = false): array|string
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    static function camelToSnake($input): string
    {
        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        preg_match_all($pattern, $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ?
                strtolower($match) :
                lcfirst($match);
        }
        return implode('_', $ret);
    }
}