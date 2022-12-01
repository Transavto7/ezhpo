<?php

namespace App\Models;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\FieldPrompt;
use App\Product;
use App\Req;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'contracts';

    protected $appends = ['name_with_dates'];

    protected $casts
        = [
            'date_of_end'   => 'datetime', // :d.m.Y
            'date_of_start' => 'datetime', // :d.m.Y
        ];

    protected $guarded = [];

    public function getNameWithDatesAttribute(){
        return $this->name
               . " \nс: " .
               ($this->date_of_start ? $this->date_of_start->format('d-m-Y') : '' )
               . " \nпо: " .
               ($this->date_of_end ? $this->date_of_end->format('d-m-Y') : '');
    }


    public static $types
        = [
            1 => 'Абонентская плата',
            2 => 'Разовая',
        ];

    public function scopeMain($query)
    {
        return $query->where("main_for_company", 1);
    }

    /**
     * Для определения текущего договора
     *
     * @param Builder $query
     * @param Carbon  $date
     *
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeForDate(Builder $query, Carbon $date)
    {
        return $query->whereDate("date_of_end", '>', $date)
                     ->whereDate("date_of_start", '<', $date);
    }

    public function drivers()
    {
        return $this->belongsToMany(
            Driver::class,
            'driver_contact_pivot',
            'contract_id',
            'driver_id'
        );
    }

    public function cars()
    {
        return $this->belongsToMany(
            Car::class,
            'car_contact_pivot',
            'contract_id',
            'car_id'
        );
    }

    // main contract for company
//    public static function mainForCompany($company_id)
//    {
//        if ($normalCompany = Company::where('hash_id', $company_id)->first()) {
//            return self::where('company_id', $normalCompany->id)
//                       ->orderBy("main_for_company", 'DESC')
//                       ->first();
//        } else {
//            return null;
//        }
//    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }

    public function company()
    {
        return $this->belongsTo(
            Company::class,
            'company_id',
            'id'
        )->withDefault();
    }

    public function our_company()
    {
        return $this->belongsTo(
            Req::class,
            'our_company_id',
            'id'
        )->withDefault();
    }

    public function services()
    {
        return $this->belongsToMany(
            Product::class,
            'contract_service',
            'contract_id',
            'service_id',
            'id',
            'id'
        )->withPivot('service_cost');
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }


    public static function startContract()
    {
//        FieldPrompt::where('field', 'products_id')->where('type', 'company')->delete();
//        FieldPrompt::where('field', 'products_id')->where('type', 'car')->delete();
//        FieldPrompt::where('field', 'products_id')->where('type', 'driver')->delete();

//        $products = Product::get()->toArray();
//
//        Service::insert($products);

        // НОВЫЕ УСЛУГИ ИЗ ДОГОВОРОВ ДЛЯ ВОДИЛ И МАШИН,  MO & TO
//        FieldPrompt::create([
//            'field' => 'services',
//            'type' => 'car',
//            'name' => 'Услуги новые',
//        ]);
//        FieldPrompt::create([
//            'field' => 'services',
//            'type' => 'driver',
//            'name' => 'Услуги новые',
//        ]);

//        FieldPrompt::create([
//            'field' => 'contract_id',
//            'type'  => 'tech',
//            'name'  => 'Договор',
//        ]);
//        FieldPrompt::create([
//            'field' => 'contract_id',
//            'type'  => 'medic',
//            'name'  => 'Договор',
//        ]);
        FieldPrompt::create([
            'field' => 'contract_id',
            'type'  => 'bdd',
            'name'  => 'Договор',
        ]);
        FieldPrompt::create([
            'field' => 'contract_id',
            'type'  => 'pechat_pl',
            'name'  => 'Договор',
        ]);
        FieldPrompt::create([
            'field' => 'contract_id',
            'type'  => 'report_cart',
            'name'  => 'Договор',
        ]);

//        FieldPrompt::create([
//            'field' => 'contract',
//            'type' => 'car',
//            'name' => 'Договор',
//        ]);
//        FieldPrompt::create([
//            'field' => 'contract',
//            'type' => 'driver',
//            'name' => 'Договор',
//        ]);
//        FieldPrompt::create([
//            'field' => 'contracts',
//            'type'  => 'company',
//            'name'  => 'Договор',
//        ]);
//        FieldPrompt::create([
//            'field' => 'products_id',
//            'type'  => 'car',
//            'name'  => 'Договор',
//        ]);
//        FieldPrompt::create([
//            'field' => 'products_id',
//            'type'  => 'driver',
//            'name'  => 'Услуги',
//        ]);
//        FieldPrompt::create([
//            'field' => 'products_id',
//            'type'  => 'company',
//            'name'  => 'Услуги',
//        ]);
//        FieldPrompt::whereName('ankets.service')
//                   ->delete();
//        FieldPrompt::insert([
//            [
//                'type' => 'service',
//                'field' => 'hash_id',
//                'name' => 'ID',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//            [
//                'type' => 'service',
//                'field' => 'name',
//                'name' => 'Название',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//            [
//                'type' => 'service',
//                'field' => 'type_product',
//                'name' => 'Тип',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//            [
//                'type' => 'service',
//                'field' => 'unit',
//                'name' => 'Ед.изм.',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//            [
//                'type' => 'service',
//                'field' => 'price_unit',
//                'name' => 'Стоимость за единицу',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//            [
//                'type' => 'service',
//                'field' => 'type_anketa',
//                'name' => 'Реестр',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//            [
//                'type' => 'service',
//                'field' => 'type_view',
//                'name' => 'Тип осмотра',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ],
//            [
//                'type' => 'service',
//                'field' => 'essence',
//                'name' => 'Сущности',
//                'created_at' => Carbon::now(),
//                'updated_at' => Carbon::now(),
//            ]
//        ]);

//        ini_set('memory_limit', '-1');
//        ini_set('max_execution_time', 400);
//        set_time_limit(400);
//

        $drivers = Company::with(['drivers', 'cars'])
                          ->get();
//        dd();


        $companies = Company::whereNotNull('products_id')
                            ->get(['id', 'products_id']);

        $services = Service::get();

        $comp_products_arr = [];

        foreach ($companies as $company) {
            $services_item = explode(',', $company->products_id);
            $res           = [];
            foreach ($services_item as $item) {
                if ($tar = $services->where('id', $item)->first()) {
                    $res[$tar->id] = ['service_cost' => $tar->price_unit];
//                        [$tar->id => ['service_cost' => $tar->price_unit]];
                }
            }

            $comp_products_arr[$company->id] = $res;
        }
//        dd($comp_products_arr);
        foreach ($comp_products_arr as $company_id => $services_item) {
            if ( !$services_item) {
                continue;
            }
//            if(!(($services_item['pr_id'] ?? false) && ($services_item['sync'] ?? false))){
//                continue;
//            }
            if ($contract = Contract::where('company_id', $company_id)->first()) {
                $contract->services()->sync($services_item);
                continue;
            }
            $contract = Contract::create([
                'name'       => "Договор $company_id",
                'company_id' => $company_id,
            ]);

            $contract->services()->sync($services_item);

            Driver::where('company_id', $company_id)->update([
                'contract_id' => $contract->id,
            ]);
            Car::where('company_id', $company_id)->update([
                'contract_id' => $contract->id,
            ]);
        }


        return true;
    }


    public static function deleteOld()
    {

        //=================DELETE
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Contract::truncate();
        Anketa::whereNotNull('contract_id')->update([
            'contract_id' => null,
        ]);

        Driver::query()->update([
            'contract_id' => null,
        ]);
        \Illuminate\Support\Facades\DB::table('contract_service')->truncate();
        Car::query()->update([
            'contract_id' => null,
        ]);
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        return 1;

        //==================DELETE
    }

    public static function init_companies($limit)
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '-1');
        set_time_limit(7200);
//
        $companies = Company::with(['drivers', 'cars'])
                            ->whereDoesntHave('contracts')
                            ->limit($limit)
//                            ->whereHashId(346632844)
                            ->get()
                            ->map(function ($q) {
                                $arr          = [];
                                $cars_drivers = $q->drivers->merge($q->cars);
//                                dd(
//                                    $cars_drivers->toArray()
//                                );

                                foreach ($cars_drivers as &$driver_or_car) {
                                    $driver_or_car->products_id = explode(',', $driver_or_car->products_id);
                                    $driver_or_car->products_id = array_map(function ($q) {
                                        return intval($q);
                                    }, $driver_or_car->products_id);

                                    $arr = $driver_or_car->products_id;
                                    asort($arr);
                                    $driver_or_car->products_id = $arr;

                                    $driver_or_car->key_for_group = implode('_', $arr);
                                }
//                                return $driver_or_car;
//                                foreach ($q->cars as &$car) {
//                                    $car->products_id = explode(',', $car->products_id);
//                                    $car->products_id = array_map(function ($q) {
//                                        return intval($q);
//                                    }, $car->products_id);
//                                    $arr              = $car->products_id;
//                                    asort($arr);
//                                    $car->products_id   = $arr;
//                                    $car->key_for_group = implode('_', $arr);
//                                }
                                $q->car_or_driver = $cars_drivers;

                                return $q;
                            });
        $services  = Product::get();

        foreach ($companies as $company) {
            $services_id = [];
            foreach ($company->car_or_driver->groupBy('key_for_group') as $key_group => $drivers_for_company_group) {

                if ( !$drivers_for_company_group[0]) {
                    continue;
                }
                $services_id = $drivers_for_company_group[0]->products_id;
                $res         = [];
                foreach ($services_id as $service_id) {
                    if ($tar = $services->where('id', $service_id)->first()) {
                        $res[$tar->id] = ['service_cost' => $tar->price_unit];
                    }
                }
                $comp_products_arr = $res;

                $contract = Contract::create([
                    'name'          => "Договор $company->id $key_group",
                    'company_id'    => $company->id,
                    'date_of_end'   => Carbon::now()->addYear(), // :d.m.Y
                    'date_of_start' => Carbon::now()->subYears(5), // :d.m.Y
                ]);

                $contract->services()->sync($comp_products_arr);

                $drivers = Driver::whereIn('id', $drivers_for_company_group->filter(function ($q) {
                    return isset($q->fio);
                })->pluck('id'))->get();

                foreach ($drivers as $driver) {
                    $driver->contracts()
                           ->sync([$contract->id]);
                }

                $cars = Car::whereIn('id', $drivers_for_company_group->filter(function ($q) {
                    return !isset($q->fio);
                })->pluck('id'))->get();

                foreach ($cars as $car) {
                    $car->contracts()
                        ->sync([$contract->id]);
                }
//                Anketa::whereIn('type_anketa', [
//                    'medic',
//                    'bdd',
//                    'report_cart',
//                ])
//                      ->where('created_at', '>', Carbon::now()->subMonths(10))
//                      ->whereIn('driver_id', $drivers_for_company_group->pluck('hash_id'))
//                      ->update([
//
//                          'contract_id' => $contract->id,
//                      ]);
            }

//            $services_id = [];
//            foreach ($company->cars->groupBy('key_for_group') as $key_group => $cars_for_company_group) {
//
//                if ( !$cars_for_company_group[0]) {
//                    continue;
//                }
//                $services_id = $cars_for_company_group[0]->products_id;
//                $res         = [];
//                foreach ($services_id as $service_id) {
//                    if ($tar = $services->where('id', $service_id)->first()) {
//                        $res[$tar->id] = ['service_cost' => $tar->price_unit];
//                    }
//                }
//                $comp_products_arr = $res;
//
//                $contract = Contract::create([
//                    'name'       => "Договор $company->id $key_group Автомобили",
//                    'company_id' => $company->id,
//                ]);
//                $contract->services()->sync($comp_products_arr);
//
//                Car::whereIn('id', $cars_for_company_group->pluck('id'))
//                   ->update([
//                       'contract_id' => $contract->id,
//                   ]);
//
//                Anketa::where('type_anketa', 'tech')
//                      ->where('created_at', '>', Carbon::now()->subMonths(10))
//                      ->whereIn('car_id', $cars_for_company_group->pluck('hash_id'))
//                      ->update([
//                          'contract_id' => $contract->id,
//                      ]);
//            }


        }

        return 1;

    }

    public static function test_one()
    {
        $companies = Company::with('contracts.services')//->limit(10)->where('id', 11765)
                            ->get()
                            ->map(function ($q) {
                                $q->contracts = $q->contracts->map(function ($contract) {
                                    $counter                   = $contract->services->count();
                                    $contract->service_counter = $counter;

                                    return $contract;
                                });
                                if($q->contracts->isNotEmpty()){
                                    $q->main_contract = $q->contracts->sortByDesc('service_counter')->first()->id;
                                }
                                return $q;
                            });

        foreach ($companies as $company) {
            if($company->main_contract){
                Contract::where('company_id', $company->id)
                        ->where('id', $company->main_contract)->update([
                        'main_for_company' => 1,
                    ]);
            }
        }
    }

}
