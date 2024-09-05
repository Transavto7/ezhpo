<?php

namespace App\Services\FormHash;

class FormHashGenerator
{
    public static function generate(HashData $data): string
    {
        return md5($data->toHashString());
    }
}
