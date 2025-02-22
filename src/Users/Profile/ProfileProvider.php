<?php

namespace Src\Users\Profile;

use Illuminate\Support\ServiceProvider;

final class ProfileProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'UsersProfile');
    }
}