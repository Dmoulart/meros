<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

class Res
{
    private static Converter $_converter;

    static function converter(): Converter
    {
        return Res::$_converter ?? (Res::$_converter = new Converter);
    }
/*
    static function json(Object | array | string $data,
                         int $statusCode = 200,
                         array $headers = ['Content-Type'=>'application/json']): Response
    {
        if(is_string($data))
            $data = [$statusCode === 200 ? 'response' : 'errors' => $data];

        $data = Res::converter()->toJSON($data);

        return new Response($data, $statusCode, $headers);
    }*/
}