<?php

namespace App\Services\Helpers;
use ArrayObject as BaseArrayObject;

class ServiceCollection extends BaseArrayObject
{
    public function __set($name, $val)
    {
        $this[$name] = $val;
    }

    public function __get($name)
    {
        return $this[$name];
    }
}