<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    public $fillable = [
        'hash_id',
        //'old_id',
        'photo', 'fio', 'year_birthday', 'phone', 'gender', 'group_risk', 'company_id',
        'products_id', 'count_pl', 'note', 'procedure_pv',
        'date_bdd', 'date_prmo',
        'date_report_driver',
        'time_card_driver',
        'town_id', 'dismissed',
        'autosync_fields'
    ];

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

    // Получение пункта выпуска
    public static function getName ($id = 0) {
        $driver = Driver::where('hash_id', $id)->first();

        if($driver) {
            $driver = $driver->fio;
        } else {
            $driver = '';
        }

        return $driver;
    }

    public static function getAll () {
        return self::all();
    }

    /**
     * @param $id
     * @param $tonometer
     * - Если поле Возраст водителя более 50 лет, то ему автоматически назначается “Группа риска - Возраст” (проверять при каждом заполнении Анкеты)
       - Если поле “Показания тонометра” выше допустимого (более 140) - Водителю назначается “Группа риска - А/Д”
       - Если поля Алкоголь или Наркотики значение “Положительно” - автоматически назначается Алкоголь или Наркотики к Группе риска Водителя.
     */
    public static function DriverChecker ($id, $tonometer, $test_narko, $proba_alko)
    {
        $Driver = Driver::where('hash_id', $id)->first();

        if($Driver) {
            $y = date('Y');

            $year_birthday = date('Y', strtotime($Driver->year_birthday));
            $age = $y - $year_birthday;
            $group_risk = '';

            if($age > 50) {
                $group_risk = 'Возраст';
            }

            if($tonometer > 140) {
                $group_risk = 'А\Д';
            }

            if($test_narko === 'Положительно') {
                $group_risk = 'Наркотики';
            } else if ($proba_alko === 'Положительно') {
                $group_risk = 'Алкоголь';
            }

            $Driver->group_risk = $group_risk;
            $Driver->save();

            return true;
        }

        return false;
    }
}
