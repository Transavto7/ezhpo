<?php

namespace App\Exceptions;

class CompanyWithSameNameAlreadyExist extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат по названию компании';
}
