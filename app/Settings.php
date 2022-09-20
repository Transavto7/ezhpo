<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $fillable
        = [
            'id',
            'logo',
            'key',
            'name',
            'value',
            'sms_api_key',
            'sms_text_driver',
            'sms_text_car',
            'sms_text_phone',
            'sms_text_default',
            'deleted_id'
        ];

    public static function setting($key)
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            return $setting->value;
        }

        return '';
    }

    public static function getAll()
    {
        return self::all();
    }
}
