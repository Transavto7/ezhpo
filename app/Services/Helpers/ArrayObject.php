<?php

namespace App\Services\Helpers;
use ArrayIterator;
use ArrayObject as BaseArrayObject;

class ArrayObject extends BaseArrayObject
{
    /**
     * @param array|object $array
     * @param integer $flags
     * @param string $iteratorClass
     */
    public function __construct(
        $array = [],
        $flags = BaseArrayObject::ARRAY_AS_PROPS,
        $iteratorClass = ArrayIterator::class
    ) {
        $data = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = new self($value);
            }
            $data[$key] = $value;
        }

        parent::__construct($data, $flags, $iteratorClass);
    }

    /**
     * @param array|object $array
     * @param integer $flags
     * @param string $iteratorClass
     * @return \App\Services\Helpers\ArrayObject
     */
    public static function make(
        $array = [],
        int $flags = BaseArrayObject::ARRAY_AS_PROPS,
        string $iteratorClass = ArrayIterator::class): ArrayObject
    {
        return new self($array, $flags, $iteratorClass);
    }
}