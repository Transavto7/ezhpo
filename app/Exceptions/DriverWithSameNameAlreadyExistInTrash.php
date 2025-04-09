<?php

namespace App\Exceptions;

class DriverWithSameNameAlreadyExistInTrash extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат ФИО водителя в корзине';
}
