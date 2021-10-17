<?php

namespace App\Exceptions;

use App\Utils\Res;
use Exception;

class EntityConverterException extends Exception
{
    private ?array $missingProps;
    private ?string $class;


    public function __construct(string $message,
                                int $code = 0,
                                Exception $previous = null,
                                array $missingProps = null,
                                string $class = null)
    {
        parent::__construct($message, $code, $previous);

        $this->missingProps = $missingProps;
        $this->class = $class;
    }

    public function getResponse(): \Symfony\Component\HttpFoundation\Response
    {
        if(count($this->getMissingProps())){
            return Res::json([
                "error" => "The $this->class entity is missing required properties.",
                "properties" => $this->getMissingProps()
            ], 422);
        }
        return Res::json($this->getMessage(), 500);
    }

    public function getMissingProps(): ?array
    {
        return $this->missingProps;
    }
}