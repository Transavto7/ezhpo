<?php

namespace App\Exceptions;

use Exception;

class InvalidCarRegionException extends Exception
{
    protected $message = 'Невалидный формат гос.номера. Автомобильный регион не найден';
}
