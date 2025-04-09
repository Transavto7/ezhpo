<?php

namespace App\Exceptions;

class CompanyWithSameINNAlreadyExistInTrash extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат компании по ИНН (+КПП) или ОГРН в корзине';
}
