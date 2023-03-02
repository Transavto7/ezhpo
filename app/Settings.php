<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Settings
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $key
 * @property string|null $value
 * @method static Builder|Settings newModelQuery()
 * @method static Builder|Settings newQuery()
 * @method static Builder|Settings query()
 * @method static Builder|Settings whereCreatedAt($value)
 * @method static Builder|Settings whereId($value)
 * @method static Builder|Settings whereKey($value)
 * @method static Builder|Settings whereUpdatedAt($value)
 * @method static Builder|Settings whereValue($value)
 * @mixin \Eloquent
 */
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
