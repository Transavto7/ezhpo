<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $fillable = [
        'hash_id', 'name', 'type_product', 'unit', 'price_unit',
        'type_anketa', 'type_view', 'essence'
    ];

    // Сущности
    public static $essence = [
        0 => 'Только компанию',
        1 => 'Только водителей',
        2 => 'Только автомобили',
        3 => 'Автомобили или водителей',
    ];

    // многие ко многим ебать
    public function getName ($id)
    {
        $id = explode(',', $id);

        $data = self::whereIn('id', $id)->get();

        if(!$data) {
            $data = '';
        } else {
            $newData = '';

            foreach($data as $dataItemKey => $dataItem) {
                $newData .= ($dataItemKey !== 0 ? ', ' : '') . $dataItem->name;
            }

            $data = $newData;
        }

        return $data;
    }
    // это чё блять за хуйня блят ь
    public static function getAll () {
        return self::all();
    }
}
