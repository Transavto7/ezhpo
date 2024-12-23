<?php

namespace App\Exceptions;

class InvalidCarTypeAutoForIsDopTechForm extends \Exception
{
    protected $message = 'Категория ТС не совпадает с выбранным авто!';
}
