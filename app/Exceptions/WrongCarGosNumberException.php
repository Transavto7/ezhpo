<?php

namespace App\Exceptions;

use Exception;

class WrongCarGosNumberException extends Exception
{
    protected $message = 'Невалидный формат гос.номера. Могут быть использованы только цифры и буквы. Длина 8 или 9 символов.';
}
