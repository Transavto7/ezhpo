<?php

namespace Src\ExternalSystem\Exceptions;

final class HumanNameException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Фамилия и имя обязательны для заполнения';
        parent::__construct();
    }
}
