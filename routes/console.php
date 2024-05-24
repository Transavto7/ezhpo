<?php

use App\Anketa;
use App\Company;
use App\Driver;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

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

Artisan::command('companies:procedure_pv-fix', function () {
    Company::whereNotIn('procedure_pv', [
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

Artisan::command('anketas:clear', function () {
    $date = Carbon::parse('2023-09-15');
    $anketas = Anketa::where('driver_id', 217543)->whereBetween('date', [
        $date->startOfDay(),
        $date->endOfDay()
    ]);

    $saved = $anketas->first();

    if ($saved) {
        $anketas = $anketas->where('id', '!=', $saved->id);
    }

    $count = $anketas->count();

    $this->comment('delete ' . $count . ' anketas');

})->describe('Display an inspiring quote');

Artisan::command('inspections:fix-realy-by-date', function () {
    $forms = Anketa::query()
        ->where('created_at', '>=', Carbon::parse('01-07-2022')->startOfDay())
        ->where('created_at', '<=', Carbon::now())
        ->whereNull('realy')
        ->get();

    $this->comment("Анкет к проверке: " . $forms->count());

    foreach ($forms as $form) {
        if (!$form->created_at || !$form->date) {
            $form->realy = 'нет';

            $form->save();

            continue;
        }

        $date = Carbon::parse($form->created_at)->timestamp - Carbon::parse($form->date)->timestamp;
        $diffInHours = abs($date) / 3600;
        if ($diffInHours >= 12) {
            $form->realy = 'нет';
        } else {
            $form->realy = 'да';
        }

        $form->save();
    }

    $this->comment('Поле real анкет после 01.07.2022 исправлено!');

})->describe('Fix inspection realy by diff in date and created at');

Artisan::command('inspections:fix', function () {
    $count = Anketa::query()
        ->whereIn('type_anketa', ['medic', 'tech'])
        ->where('realy', 'like', '%да%')
        ->update(['realy' => 'да']);

    $this->comment('Реальных анкет -  ' . $count);

    $count = Anketa::query()
        ->whereIn('type_anketa', ['medic', 'tech'])
        ->where('realy', 'like', '%нет%')
        ->update(['realy' => 'нет']);

    $this->comment('Не реальных анкет - ' . $count);

    $count = Anketa::query()
        ->whereIn('type_anketa', ['medic', 'tech'])
        ->whereNull('realy')
        ->orWhere(function ($query) {
            $query
                ->where('realy', 'not like', '%да%')
                ->where('realy', 'not like', '%нет%');
        })
        ->whereNotNull('flag_pak')
        ->update(['realy' => 'да']);

    $this->comment('Анкет с некорректным типом реальности (ПАК) - ' . $count);

    $count = Anketa::query()
        ->whereIn('type_anketa', ['medic', 'tech'])
        ->whereNull('realy')
        ->orWhere(function ($query) {
            $query
                ->where('realy', 'not like', '%да%')
                ->where('realy', 'not like', '%нет%');
        })
        ->update(['realy' => 'нет']);

    $this->comment('Анкет с некорректным типом реальности - ' . $count);

    Anketa::query()
        ->whereIn('type_anketa', ['medic', 'tech'])
        ->where('type_view', '!=', 'Послерейсовый/Послесменный')
        ->where('type_view', '!=', 'Предрейсовый/Предсменный')
        ->update(['type_view' => 'Предрейсовый/Предсменный']);

    Anketa::query()
        ->where('type_anketa', 'medic')
        ->where('proba_alko', '!=', 'Отрицательно')
        ->where('proba_alko', '!=', 'Положительно')
        ->update(['proba_alko' => 'Отрицательно']);

})->describe('Fix data in inspections');

Artisan::command('inspections:import', function () {
    $json = file_get_contents(storage_path() . "/inspections.json");
    $inspections = json_decode($json);
    $created = [];

    foreach ($inspections as $inspection) {
        $user = User::find($inspection->user_id);
        $driver = Driver::where('hash_id', $inspection->driver_id)->first();

        $created[] = Anketa::create([
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
