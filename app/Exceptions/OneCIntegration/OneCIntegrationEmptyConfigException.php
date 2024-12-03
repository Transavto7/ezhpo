<?php

namespace App\Exceptions\OneCIntegration;

use Exception;

class OneCIntegrationEmptyConfigException extends Exception
{
    protected $message = 'Ошибка интеграции с 1С. Пустой конфиг.';
}
