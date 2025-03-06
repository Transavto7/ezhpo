<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public const DEFAULT_PRESSURE_SYSTOLIC = 150;
    public const DEFAULT_PRESSURE_DIASTOLIC = 100;
    public const DEFAULT_PULSE_LOWER = PHP_INT_MIN;
    public const DEFAULT_PULSE_UPPER = PHP_INT_MAX;
    public const DEFAULT_TIME_OF_ALCOHOL_BAN = 0;
    public const DEFAULT_TIME_OF_PRESSURE_BAN = 0;

    public $fillable
        = [
            'deleted_at',
            'key',
            'value',
        ];

    public static function setting($key)
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            return $setting->value;
        }

        return '';
    }

    public static function set($key, $value) {
        self::updateOrCreate(['key' => $key], [
            'value' => $value
        ]);
    }
}
