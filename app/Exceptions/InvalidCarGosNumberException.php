<?php

namespace App\Exceptions;

use Exception;

class InvalidCarGosNumberException extends Exception
{
    protected $message = 'Невалидный формат гос.номера. Гос номер не соответствует выбранной категории авто';
}
