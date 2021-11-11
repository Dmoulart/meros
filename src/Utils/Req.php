<?php

namespace App\Utils;

use Exception;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class Req
{

    /**
     * @param Request $request
     * @param string $class
     * @param array $params
     * @param Object|null $entityToUpdate
     * @return Object|null
     */
    static function toEntity(Request $request,
                             string  $class,
                             array   $params = [],
                             Object  $entityToUpdate = null): ?Object
    {
        try {
            $reflectionClass = new ReflectionClass(new $class);
        }
        catch(\ReflectionException $e){
            return null;

           /* throw new EntityConverterException(
                $e->getMessage(),
                0,
                $e
            );*/
        }


        $entity = $entityToUpdate ? $entityToUpdate : new $class;

        $properties = $reflectionClass->getProperties();

        $filledProperties = [];

        foreach($properties as $property)
        {
            if(isset($params['ignore']) && in_array($property, $params['ignore']))
                continue;

            $attributeSetter = 'set'.ucfirst($property->getName());

            try{

                if($reflectionClass->getMethod($attributeSetter)){

                    // If the prop in request is formatted as snake_case
                    if($request->get(StringUtils::camelToSnake($property->getName())))
                    {
                        $entity->$attributeSetter($request->get(StringUtils::camelToSnake($property->getName())));
                        $filledProperties[] = $property->getName();
                    }

                    // If the prop in request is formatted as camelCase
                    if($request->get($property->getName()))
                    {
                        $entity->$attributeSetter($request->get($property->getName()));
                        $filledProperties[] = $property->getName();
                    }

                }


            }
            catch(Exception $e) {
                continue;
            }
        }

/*        $missingProps = [];

        if(isset($params['required']))
            foreach($params['required'] as $requiredParam) {
                if(!in_array($requiredParam,$filledProperties))
                    $missingProps[] = $requiredParam;
            }*/

/*        if(count($missingProps)){
            throw new EntityConverterException(
                "The $class is missing required properties",
                0,
                null,
                $missingProps,
                $class
            );
        }*/

        return $entity;
    }
}