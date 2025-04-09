<?php

namespace App\Exceptions;

class CarWithSameVinAlreadyExistInTrash extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат по VIN-коду Автомобиля в корзине';
}
