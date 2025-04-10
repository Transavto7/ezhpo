<?php

namespace App\Exceptions;

use Exception;

class WrongCarVinException extends Exception
{
    protected $message = 'Невалидный формат VIN. Могут быть использованы только цифры и латинские символы, длина - 16 или 17 символов.';
}
