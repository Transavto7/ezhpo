<?php

namespace App\Values\Contracts;

interface ValueInterface
{
    public function __construct($value);

    public function getValue();
}