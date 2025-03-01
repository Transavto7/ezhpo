<?php

namespace App\Exceptions;

class WrongCompanyReqsException extends \Exception
{
    protected $message = 'Невалидные реквизиты компании';
}
