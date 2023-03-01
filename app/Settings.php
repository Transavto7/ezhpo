<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $fillable
        = [
            'deleted_at',
            'key',
            'value',
        ];

    public static function setting(string $key, ?string $default = '') : ?string
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            return $setting->value ?? $default;
        }

        return $default;
    }

    public static function set($key, $value) {
        self::updateOrCreate(['key' => $key], [
            'value' => $value
        ]);
    }

    public static function getAll()
    {
        return self::all();
    }
}
