<?php

namespace App\Exceptions;

class CompanyWithSameNameAlreadyExistInTrash extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат по названию компании в корзине';
}
