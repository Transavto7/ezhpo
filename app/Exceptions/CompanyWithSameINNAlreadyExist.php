<?php

namespace App\Exceptions;

class CompanyWithSameINNAlreadyExist extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат компании по ИНН (+КПП) или ОГРН';
}
