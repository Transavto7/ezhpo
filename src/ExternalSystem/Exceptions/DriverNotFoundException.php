<?php

namespace Src\ExternalSystem\Exceptions;

final class DriverNotFoundException extends \Exception
{

    public function __construct()
    {
        $this->message = 'Не найден водитель, указанный в анкете';
        parent::__construct();
    }
}
