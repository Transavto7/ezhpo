<?php

namespace Src\Helper\Pattern;

class SmartEnum
{
    protected $value = null;

    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    public static function create($value = null) {
        return new static($value);
    }

    public function isDefined()
    {
        return !is_null($this->value);
    }

    /**
     * @param $value
     * @return static
     */
    public function setValue($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
