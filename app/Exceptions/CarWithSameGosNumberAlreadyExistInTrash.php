<?php

namespace App\Exceptions;

class CarWithSameGosNumberAlreadyExistInTrash extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат по гос.номеру Автомобиля в корзине';
}
