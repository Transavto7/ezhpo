<?php

namespace App;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    public $fillable
        = [
            'hash_id',
            'photo',
            'fio',
            'year_birthday',
            'phone',
            'gender',
            'group_risk',
            'company_id',
            'products_id',
            'count_pl',
            'note',
            'procedure_pv',
            'date_bdd',
            'date_prmo',
            'date_driver_license',
            'date_narcotic_test',
            'date_report_driver',
            'time_card_driver',
            'town_id',
            'dismissed',
            'autosync_fields',
            'date_of_employment',
            'contract_id',
            'deleted_id',
            'pressure_systolic',
            'pressure_diastolic'
        ];

    protected $casts
        = [
            'date_of_employment' => 'datetime',
        ];


    public function inspections_medic()
    {
        return $this->hasMany(
            Anketa::class,
            'driver_id',
            'hash_id'
        )->where('type_anketa', 'medic');
    }

    // Печать путевых листов
    public function inspections_pechat_pl()
    {
        return $this->hasMany(
            Anketa::class,
            'driver_id',
            'hash_id'
        )->where('type_anketa', 'pechat_pl');
    }

    // инструктажи БДД
    public function inspections_bdd()
    {
        return $this->hasMany(
            Anketa::class,
            'driver_id',
            'hash_id'
        )->where('type_anketa', 'bdd');
    }

    // снятия отчетов с карт
    public function inspections_report_cart()
    {
        return $this->hasMany(
            Anketa::class,
            'driver_id',
            'hash_id'
        )->where('type_anketa', 'report_cart');
    }


    public function contracts()
    {
        return $this->belongsToMany(
            Contract::class,
            'driver_contact_pivot',
            'driver_id',
            'contract_id'
        );
    }

//    public function contract()
//    {
//        return $this->belongsTo(Contract::class, 'contract_id', 'id')
//                    ->withDefault();
//    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public function getAutoSyncFieldsFromHashId($hash_id)
    {
        $element = self::where('hash_id', $hash_id)->first();

        if ($element) {
            if ($element->autosync_fields) {
                return explode(',', $element->autosync_fields);
            }
        }

        return [];
    }

    public static function getAutoSyncFields($hash_id)
    {
        $element = self::where('hash_id', $hash_id)->first();

        if ($element) {
            if ($element->autosync_fields) {
                return explode(',', $element->autosync_fields);
            }
        }

        return [];
    }

    public static function calcServices($hash_id, $type_anketa = '', $type_view = '', $count = 0)
    {
        $element = self::where('hash_id', $hash_id)->first();
        $data    = '';

        if ($element) {
            if (isset($element->products_id)) {
                $data     = [];
                $services = explode(',', $element->products_id);

                $services = Product::whereIn('id', $services)
                                   ->where('type_anketa', $type_anketa)
                                   ->where('type_view', 'LIKE', "%$type_view%")->get();

                foreach ($services as $serviceKey => $service) {
                    $discounts = Discount::where('products_id', $service->id)->get();

                    if ($discounts->count()) {
                        foreach ($discounts as $discount) {
                            eval('$is_discount_valid = '.$count.$discount->trigger.$discount->porog.';');

                            if ($is_discount_valid) {
                                $disc   = ($service->price_unit * $discount->discount) / 100;
                                $p_unit = $service->price_unit;

                                $services[$serviceKey]->price_unit = $p_unit - $disc;
                            }
                        }
                    }
                }

                if (count($services)) {
                    $data['summ'] = $services->sum('price_unit').'₽';
                } else {
                    if ( !isset($element->products_id)) {
                        $data['summ'] = "<span class='text-red'>Услуги не указаны</span>";
                    } else {
                        $data['summ'] = "<span class='text-red'>Услуги не найдены</span>";
                    }
                }

                return "<div><b>$data[summ]</b></div>";
            } else {
                $data = 'Услуги не указаны';
            }
        }

        return $data;
    }

    // Получение пункта выпуска
    public static function getName($id = 0)
    {
        $driver = Driver::where('hash_id', $id)->first();

        if ($driver) {
            $driver = $driver->fio;
        } else {
            $driver = '';
        }

        return $driver;
    }

    public static function getAll()
    {
        return self::all();
    }

    /**
     * @param $id
     * @param $tonometer
     * - Если поле Возраст водителя более 50 лет, то ему автоматически назначается “Группа риска - Возраст” (проверять
     * при каждом заполнении Анкеты)
     * - Если поле “Показания тонометра” выше допустимого (более 140) - Водителю назначается “Группа риска - А/Д”
     * - Если поля Алкоголь или Наркотики значение “Положительно” - автоматически назначается Алкоголь или Наркотики к
     * Группе риска Водителя.
     */
    public static function DriverChecker($id, $tonometer, $test_narko, $proba_alko)
    {
        $Driver = Driver::where('hash_id', $id)->first();

        if ($Driver) {
            $Driver->checkGroupRisk($tonometer, $test_narko, $proba_alko);

            return true;
        }

        return false;
    }

    public function checkGroupRisk($tonometer, $test_narko, $proba_alko)
    {
        $y = date('Y');

        $year_birthday = date('Y', strtotime($this->year_birthday));
        $age           = $y - $year_birthday;
        $group_risk    = '';

        if ($age > 50) {
            $group_risk = 'Возраст';
        }

        if ($tonometer > 140) {
            $group_risk = 'А\Д';
        }

        if ($test_narko === 'Положительно') {
            $group_risk = 'Наркотики';
        } else {
            if ($proba_alko === 'Положительно') {
                $group_risk = 'Алкоголь';
            }
        }

        $this->group_risk = $group_risk;
        $this->save();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')
                    ->withDefault();
    }

    public function getPressureSystolic() {
        if ($this->pressure_systolic) {
            return $this->pressure_systolic;
        }

        if ($this->company->pressure_systolic) {
            return $this->company->pressure_systolic;
        }

        return 150;
    }

    public function getPressureDiastolic() {
        if ($this->pressure_diastolic) {
            return $this->pressure_diastolic;
        }

        if ($this->company->pressure_diastolic) {
            return $this->company->pressure_diastolic;
        }

        return 100;
    }
}
