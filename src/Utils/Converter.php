<?php

namespace App\Utils;

use Exception;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class Converter
{
    private Serializer $serializer;

    function __construct(){
        $this->serializer = SerializerBuilder::create()->build();
    }

    function toJson(array | Object $data): string
    {
        if(is_object($data))
        {
            $data = [$data];
        }
        if(is_string($data))
        {
            $data = ["response" => $data];
        }
        return $this->serializer->serialize($data, 'json');
    }
}