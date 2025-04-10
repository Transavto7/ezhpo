<?php

namespace App\Exceptions;

class CarWithSameVinAlreadyExist extends EntityAlreadyExistException
{
    protected $message = 'Найден дубликат по VIN-коду Автомобиля';
}
