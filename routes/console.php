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
