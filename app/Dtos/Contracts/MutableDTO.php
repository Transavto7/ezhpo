<?php

namespace App\Dtos\Contracts;

use App\Values\Contracts\ValueInterface;
use ReflectionClass;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\FieldValidator;

class MutableDTO extends DataTransferObject
{
    public function __construct(array $parameters = [])
    {
        $this->prepareValues($parameters);
        parent::__construct($parameters);
    }

    private function prepareValues(array $parameters)
    {
        $reflection = new ReflectionClass(self::class);

        foreach ($reflection->getProperties() as $reflectionProperty) {
            $propTypeName = $reflectionProperty->getType()->getName();
            if(is_a($propTypeName, ValueInterface::class, true)) {
                $this->{$reflectionProperty->getName()} = new $propTypeName();
            }
        }
    }
}