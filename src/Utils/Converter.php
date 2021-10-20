<?php

namespace App\Utils;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class Converter
{
    private Serializer $serializer;

    function __construct(){
        $this->serializer = SerializerBuilder::create()->build();
    }

    function toJson(array | Object $data): string
    {
        return $this->serializer->serialize($data, 'json');
    }
}