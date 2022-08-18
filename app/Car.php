<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $fillable = [
        'hash_id',
        //'old_id',
        'gos_number', 'mark_model', 'type_auto', 'products_id', 'trailer', 'company_id',
        'count_pl', 'note', 'procedure_pv', 'date_prto', 'date_techview',
        'time_skzi', 'date_osago',
        'town_id', 'dismissed',
        'autosync_fields'
    ];

    // sorry for name
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }


    public function getAutoSyncFieldsFromHashId ($hash_id) {
        $element = self::where('hash_id', $hash_id)->first();

        if($element) {
            if($element->autosync_fields) {
                return explode(',', $element->autosync_fields);
            }
        }

        return [];
    }

    public static function getAutoSyncFields ($hash_id) {
        $element = self::where('hash_id', $hash_id)->first();

        if($element) {
            if($element->autosync_fields) {
                return explode(',', $element->autosync_fields);
            }
        }

        return [];
    }

    public static function calcServices ($hash_id, $type_anketa = '', $type_view = '', $count = 0) {
        $element = self::where('hash_id', $hash_id)->first();
        $data = '';

        if($element) {
            if(isset($element->products_id)) {
                $data = [];
                $services = explode(',', $element->products_id);

                $services = Product::whereIn('id', $services)
                    ->where('type_anketa', $type_anketa)
                    ->where('type_view', 'LIKE', "%$type_view%")->get();

                foreach($services as $serviceKey => $service) {
                    $discounts = Discount::where('products_id', $service->id)->get();

                    if($discounts->count()) {
                        foreach($discounts as $discount) {
                            eval('$is_discount_valid = ' . $count . $discount->trigger . $discount->porog . ';');

                            if($is_discount_valid) {
                                $disc = ($service->price_unit * $discount->discount) / 100;
                                $p_unit = $service->price_unit;

                                $services[$serviceKey]->price_unit = $p_unit - $disc;
                            }
                        }
                    }
                }

                if(count($services)) {
                    $data['summ'] = $services->sum('price_unit') . '₽';
                } else if(!isset($element->products_id)) {
                    $data['summ'] = "<span class='text-red'>Услуги не указаны</span>";
                } else {
                    $data['summ'] = "<span class='text-red'>Услуги не найдены</span>";
                }

                return "<div><b>$data[summ]</b></div>";
            } else {
                $data = 'Услуги не указаны';
            }
        }

        return $data;
    }

    public static function getAll () {
        return self::all();
    }

    // Получение пункта выпуска
    public static function getName ($id = 0) {
        $car = Car::where('hash_id', $id)->first();

        if($car) {
            $car = $car->gos_number;
        } else {
            $car = '';
        }

        return $car;
    }
}
