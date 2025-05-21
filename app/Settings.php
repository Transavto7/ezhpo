<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public const DEFAULT_PRESSURE_SYSTOLIC = 150;

    public const DEFAULT_PRESSURE_DIASTOLIC = 100;

    public const DEFAULT_PULSE_LOWER = 0;

    public const DEFAULT_PULSE_UPPER = 1000;

    public const DEFAULT_TIME_OF_ALCOHOL_BAN = 0;

    public const DEFAULT_TIME_OF_PRESSURE_BAN = 0;

    public $fillable = [
        'deleted_at',
        'key',
        'value',
    ];

    public static function setting($key)
    {
        $setting = self::query()->where('key', $key)->first();

        if ($setting) {
            return $setting->value;
        }

        return '';
    }

    public static function set($key, $value)
    {
        self::query()->updateOrCreate(['key' => $key], [
            'value' => $value,
        ]);
    }
}
