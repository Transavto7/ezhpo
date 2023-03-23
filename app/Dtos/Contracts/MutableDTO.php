<?php

namespace App\Dtos\Contracts;

use App\Dtos\WorkReportFilterData;
use App\Values\Contracts\ValueInterface;
use App\Values\Exceptions\ValueObjectException;
use App\Values\WorkReport\FilterDateValue;
use Exception;
use ReflectionClass;
use ReflectionException;
use Spatie\DataTransferObject\DataTransferObject;

class MutableDTO extends DataTransferObject
{
    /**
     * @throws ReflectionException
     * @throws ValueObjectException
     */
    public function __construct(array $parameters = [])
    {
        $this->prepareValues($parameters);
        parent::__construct($parameters);
    }

    /**
     * @throws ReflectionException
     * @throws \App\Values\Exceptions\ValueObjectException
     * @throws \Exception
     */
    private function prepareValues(array &$parameters)
    {
        $reflection = new ReflectionClass(WorkReportFilterData::class);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }
            if ($reflectionProperty->getType()) {
                $propTypeName = $reflectionProperty->getType()->getName();
                if (is_a($propTypeName, ValueInterface::class, true)) {
                    $value = $parameters[$reflectionProperty->getName()] ?? null;
                    $valueReflection = new ReflectionClass($propTypeName);
                    $setterName = null;
                    foreach ($valueReflection->getProperties() as $reflectionValueProperty) {
                        $propName = $reflectionValueProperty->getName();
                        if ($propName === $reflectionProperty->getName() or $propName === 'value') {
                            if (!$valueReflection->hasMethod('get'.ucfirst($propName))) {
                                throw new ValueObjectException("Missing getter method for ValueObject class property {$propName}");
                            }

                            if (!$valueReflection->hasMethod('set'.ucfirst($propName))) {
                                throw new ValueObjectException("Missing setter method for ValueObject class property {$propName}");
                            }

                            if (!$valueReflection->hasMethod('__toString')) {
                                throw new ValueObjectException("Missing __toString method for ValueObject");
                            }

                            $setterName = 'set'.ucfirst($reflectionValueProperty->getName());
                        }
                    }

                    /** @var FilterDateValue $valueObjectInstance */
                    $valueObjectInstance = $valueReflection->newInstance();

                    if ($value) {
                        $parameters[$reflectionProperty->getName()] = $valueObjectInstance->{$setterName}($value);
                    }
                }
            } else {
                throw new Exception("Untyped property {$reflectionProperty->getName()} in data transfer object");
            }
        }
    }
}