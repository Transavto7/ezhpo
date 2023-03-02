<?php

namespace App\Services\Helpers;

use Illuminate\Database\Eloquent\Model;

class ModelHelper
{
    public static function getModelFields(Model $model): array
    {
        return $model->getAttributes();
    }
}