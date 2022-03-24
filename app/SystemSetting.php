<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    public $fillable = [
        'param', 'label', 'val', 'values', 'input_type', 'category'
    ];

    public $settings = [
        ['param' => 'id_auto', 'label' => 'Поле "ID авто" в МО', 'category' => 'medic', 'input_type' => 'checkbox'],
        ['param' => 'id_auto_required', 'label' => 'Обязательное поле ID авто в МО', 'category' => 'medic', 'input_type' => 'checkbox']
    ];

    public static function check ($setting, $category = 'medic')
    {
        $data = self::where('param', $setting)->where('category', $category)->first();

        if($data) {
            return $data->val;
        }

        return '';
    }
}
