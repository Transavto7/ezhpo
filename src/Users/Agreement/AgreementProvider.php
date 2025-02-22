<?php

namespace Src\Users\Agreement;

use Illuminate\Support\ServiceProvider;

final class AgreementProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'UsersAgreement');
    }
}
