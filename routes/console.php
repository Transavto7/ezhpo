<?php

use App\Driver;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('fetch:permissions', function () {
    $permissions = \App\User::fetchPermissions();
    $this->comment($permissions['total'].' - доступов всего, '
                   .$permissions['added'].' - доступов добавлено, '
                   .$permissions['deleted'].' - доступов удалено');
})->describe('Display an inspiring quote');

Artisan::command('init:contracts', function () {
    if(\App\Models\Contract::startContract()){
        $this->comment('Договора вроде инициализированы');
    }
})->describe('Display an inspiring quote');

Artisan::command('contract:first', function () {
    if(\App\Models\Contract::deleteOld()){
        $this->comment('Всё удалено');
    }
})->describe('Display an inspiring quote');

Artisan::command('contract:second {limit}', function ($limit) {
    if(\App\Models\Contract::init_companies($limit)){
        $this->comment('Связано');
    }
})->describe('Display an inspiring quote');
Artisan::command('contract:third', function () {
    if(\App\Models\Contract::test_one()){
        $this->comment('Связано nakonec to');
    }
})->describe('Display an inspiring quote');

Artisan::command('companies:procedure_pv-fix', function () {
    \App\Company::whereNotIn('procedure_pv', [
        'Наперед без дат',
        'Наперёд с датами',
        'Задним числом',
        'Фактовый',
    ])
                ->orWhereNull('procedure_pv')
                ->update([
                    'procedure_pv' => 'Фактовый',
                ]);
    $this->comment('Компани пофикшенс');

})->describe('Display an inspiring quote');

Artisan::command('anketas:fix', function () {
    $anketas = \App\Anketa::where('created_at', '>=', Carbon::parse('01-07-2022')->startOfDay())
        ->where('created_at', '<=', Carbon::now())->whereNull('realy')->get();

    $this->comment($anketas->count());

    foreach ($anketas as $anketa){
        if ($anketa->created_at && $anketa->date) {
            $date = Carbon::parse($anketa->created_at)->timestamp - Carbon::parse($anketa->date)->timestamp;
            $date = abs($date) / 60 / 60;
            if ($date >= 12) {
                $anketa->realy = 'нет';
            } else {
                $anketa->realy = 'да';
            }

        } else {
            $anketa->realy = 'нет';
        }

        $anketa->save();
    }

    $this->comment('Анкеты пофикшенs');

})->describe('Display an inspiring quote');

Artisan::command('services:create', function () {
    \App\FieldPrompt::where('type', 'service')->forceDelete();

    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'hash_id',
        'name' => 'ID'
    ]);
    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'name',
        'name' => 'Название'
    ]);
    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'type_product',
        'name' => 'Тип'
    ]);
    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'unit',
        'name' => 'Ед.изм.'
    ]);
    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'price_unit',
        'name' => 'Стоимость за единицу'
    ]);
    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'type_anketa',
        'name' => 'Реестр'
    ]);
    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'type_view',
        'name' => 'Тип осмотра'
    ]);
    \App\FieldPrompt::create([
        'type' => 'service',
        'field' => 'essence',
        'name' => 'Сущности'
    ]);

    \App\Service::where('id', '>', -1)->forceDelete();
    $products = \App\Product::get()->toArray();
    \App\Service::insert($products);
});

Artisan::command('drivers_birthday:fix', function () {

    $id = [
        352908 => '13.03.1971',
        444400 => '09.11.1972',
        239766 => '16.06.1995',
        122531 => '02.03.1993',
        110022 => '30.09.1959',
        214760 => '02.02.1982',
        461446 => '26.06.1957',
        282041 => '25.10.1986',
        155295 => '17.05.1983',
        391472 => '28.09.1965',
        451551 => '22.08.1974',
        473911 => '21.10.1969',
        213242 => '11.01.1971',
        385403 => '19.06.1961',
        276015 => '27.07.1972',
        212481 => '28.10.1969',
        157912 => '24.04.1975',
        295712 => '29.07.1980',
        447180 => '11.07.1966',
        387638 => '05.10.1947',
        333302 => '13.12.1962',
        495985 => '05.12.1967',
        185817 => '26.04.1970',
        393163 => '15.06.1983',
        325660 => '30.11.1973',
        389232 => '28.07.1967',
        402545 => '18.05.1985',
        249597 => '07.12.1961',
        196311 => '08.09.1986',
        147015 => '21.10.1957',
        370461 => '07.11.1973',
        139816 => '24.06.1988',
        255195 => '15.01.1979',
        310723 => '16.08.1980',
        398311 => '21.12.1982',
        240819 => '02.01.1992',
        431626 => '24.07.1979',
        489877 => '03.07.1985',
        433041 => '12.05.1959',
        318538 => '09.08.1973',
        128406 => '21.12.1960',
        392862 => '20.06.1959',
        497514 => '20.04.1956',
        294059 => '26.11.1979',
        488724 => '30.01.1966',
        247050 => '01.11.1959',
        288938 => '18.12.1966',
        267214 => '18.07.1972',
        476006 => '15.04.1971',
        423160 => '20.10.1954',
        172020 => '23.07.1965',
        417219 => '18.06.1963',
        455947 => '05.02.1959',
        466702 => '14.04.1958',
        348819 => '18.06.1974',
        318998 => '02.03.1966',
        209238 => '21.02.1957',
        479808 => '10.09.1960',
        135370 => '14.10.1958',
        118461 => '26.06.1957',
        479731 => '28.03.1981',
        373681 => '07.05.1984',
        191987 => '16.06.1965',
        273696 => '22.12.1971',
        127908 => '24.03.1964',
        399674 => '24.07.1966',
        118485 => '19.08.1999',
        295367 => '14.08.1955',
        116849 => '10.10.1968',
        128940 => '26.09.1983',
        445833 => '16.08.1961',
        464416 => '28.02.1964',
        345588 => '20.01.1995',
        346332 => '25.10.1984',
        180892 => '16.08.1989',
        276897 => '19.02.1968',
        317051 => '01.02.1984',
        290973 => '24.10.1978',
        446307 => '09.04.1989',
        374179 => '01.03.1977',
        221550 => '02.07.1963',
        194445 => '06.03.1983',
        391231 => '17.12.1966',
        485341 => '06.09.1979',
        381640 => '25.02.1977',
        307652 => '23.11.1964',
        293172 => '15.06.1985',
        127899 => '06.04.1972',
        377914 => '11.09.1976',
        496315 => '16.03.1971',
        232701 => '28.12.1978',
        362428 => '29.04.1958',
        362868 => '06.08.1953',
        346889 => '09.05.1982',
        488113 => '03.10.1980',
        282875 => '17.12.1987',
        402583 => '20.06.1967',
        282638 => '14.01.1977',
        398349 => '25.12.1952',
        221569 => '12.06.1981',
        363640 => '07.03.1958',
        274291 => '23.12.1998',
        441433 => '14.06.1980',
        484932 => '20.02.1983',
        247400 => '17.01.1959',
        383064 => '20.08.1982',
        388648 => '06.03.1973',
        342496 => '21.05.1965',
    ];



    foreach ($id as $driver_id => $date_bd){
        if($driver = Driver::whereHashId($driver_id)->first()){
            $driver->year_birthday = Carbon::parse($date_bd);
            $driver->save();
        }
    }

    $this->comment('Drivers dates of birthday fixed');

})->describe('Display an inspiring quote');

Artisan::command('crm:fix', function () {
    $count = \App\Anketa::whereIn('type_anketa', ['medic', 'tech'])
        ->where('realy', 'like', '%да%')->update(['realy' => 'да']);

    $this->comment('anketas da ' . $count);

    $count = \App\Anketa::whereIn('type_anketa', ['medic', 'tech'])
        ->where('realy', 'like', '%нет%')->update(['realy' => 'нет']);
    $this->comment('anketas net ' . $count);

    $count = \App\Anketa::whereIn('type_anketa', ['medic', 'tech'])
        ->whereNull('realy')->orWhere(function ($query) {
            $query->where('realy', 'not like', '%да%')->where('realy', 'not like', '%нет%');
        })->whereNotNull('flag_pak')->update(['realy' => 'да']);
    $this->comment('anketas other ' . $count);

    $count = \App\Anketa::whereIn('type_anketa', ['medic', 'tech'])
        ->whereNull('realy')->orWhere(function ($query) {
            $query->where('realy', 'not like', '%да%')->where('realy', 'not like', '%нет%');
        })->update(['realy' => 'нет']);

    \App\Anketa::whereIn('type_anketa', ['medic', 'tech'])
        ->where('type_view', '!=', 'Послерейсовый/Послесменный')
        ->where('type_view', '!=', 'Предрейсовый/Предсменный')
        ->update(['type_view' => 'Предрейсовый/Предсменный']);

    \App\Anketa::where('type_anketa', 'medic')
        ->where('proba_alko', '!=', 'Отрицательно')
        ->where('proba_alko', '!=', 'Положительно')
        ->update(['proba_alko' => 'Отрицательно']);
})->describe('Fiix data in anketas');

Artisan::command('inspections:import', function () {
    $json = file_get_contents(storage_path() . "/inspections.json");
    $inspections = json_decode($json);
    $created = [];

    foreach($inspections as $inspection) {
        $user = User::find($inspection->user_id);
        $driver = \App\Driver::where('hash_id', $inspection->driver_id)->first();

        $created[] = \App\Anketa::create([
            'type_anketa' => 'medic',
            'user_id' => $inspection->user_id,
            'user_name' => $user->name,
            'user_eds' => $inspection->user_eds,
            'pulse' => mt_rand(60, 80),
            'pv_id' => 'Кооперативная, 19',
            'point_id' => 2,
            'tonometer' => rand(118, 129) . '/' . rand(70, 90),
            'driver_id' => $inspection->driver_id,
            'driver_fio' => $driver->fio,
            'driver_gender' => $driver->gender,
            'driver_group_risk' => $driver->group_risk,
            'company_id' => $driver->company->hash_id,
            'company_name' => $driver->company->name,
            'med_view' => 'В норме',
            't_people' => rand(360, 370) / 10,
            'type_view' => 'Предрейсовый/Предсменный',
            'flag_pak' => 'СДПО А',
            'terminal_id' => 2889,
            'created_at' => Carbon::parse($inspection->created_at),
            'date' => Carbon::parse($inspection->created_at),
            'realy' => 'да',
            'proba_alko' => 'Отрицательно'
        ])->id;
    }

    $this->comment(implode(", ", $created));
})->describe('Import inspections');
