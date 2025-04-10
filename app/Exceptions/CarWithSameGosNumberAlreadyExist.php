<?php

namespace App\Exceptions;

class CarWithSameGosNumberAlreadyExist extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат по гос.номеру Автомобиля';
}
