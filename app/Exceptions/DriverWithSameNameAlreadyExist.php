<?php

namespace App\Exceptions;

class DriverWithSameNameAlreadyExist extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат по ФИО водителя';
}
