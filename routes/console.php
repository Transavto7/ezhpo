<?php

use Illuminate\Foundation\Inspiring;

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
