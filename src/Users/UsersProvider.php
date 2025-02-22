<?php

namespace Src\Users;

use Illuminate\Support\ServiceProvider;
use Src\Users\Agreement\AgreementProvider;
use Src\Users\Management\ManagementProvider;
use Src\Users\Profile\ProfileProvider;

final class UsersProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(AgreementProvider::class);
        $this->app->register(ManagementProvider::class);
        $this->app->register(ProfileProvider::class);
    }
}
