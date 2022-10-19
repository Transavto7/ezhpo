<?php

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

Artisan::command('company:password-fix', function () {
    $companyProfiles = \App\User::where('role', 12)->get();

    foreach ($companyProfiles as $companyProfile){
        $companyProfile->password = Hash::make($companyProfile->login);
        $companyProfile->save();
    }

    $this->comment('Компани пофикшенs');

})->describe('Display an inspiring quote');
