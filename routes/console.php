<?php

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
