<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $fillable = [
        'id', 'logo',

        'sms_api_key', 'sms_text_driver', 'sms_text_car',
        'sms_text_phone', 'sms_text_default'
    ];

    public static function setting ($setting)
    {
        $data = self::first();

        if(isset($data->$setting)) {
            return $data->$setting;
        }

        return '';
    }

    public static function getAll () {
        return self::all();
    }
}
