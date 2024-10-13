<?php

namespace App\Providers;

use App\Car;
use App\Company;
use App\Driver;
use App\Models\Contract;
use App\Observers\CarObserver;
use App\Observers\CompanyObserver;
use App\Observers\ContractObserver;
use App\Observers\DriverObserver;
use App\Observers\UserObserver;
use App\Services\ElementsSearch\ElementSearchService;
use App\Services\ElementsSearch\ElementsSearchServiceInterface;
use App\Services\HashIdGenerator\HashIdGeneratorService;
use App\Services\QRCode\QRCodeGenerator;
use App\Services\QRCode\QRCodeGeneratorInterface;
use App\Settings;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('hash-id-generator', function () {
            return new HashIdGeneratorService();
        });

        $this->app->bind(ElementsSearchServiceInterface::class, ElementSearchService::class);
        $this->app->bind(QRCodeGeneratorInterface::class, QRCodeGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->registerBladeDirectives();
        $this->registerModelObservers();
    }

    private function registerBladeDirectives()
    {
        $this->registerAdminBlade();
        $this->registerManagerBlade();
        $this->registerAccessSettingBlade();
        $this->registerExcludeRoleBlade();
        $this->registerRoleBlade();
    }

    private function registerAdminBlade()
    {
        Blade::if('admin', function () {
            /** @var User $user */
            $user = Auth::user();

            if ($user->role >= 777) {
                return 1;
            }

            return 0;
        });
    }

    private function registerManagerBlade()
    {
        Blade::if('manager', function () {
            /** @var User $user */
            $user = Auth::user();

            if ($user->role_manager) {
                return 1;
            }

            $userRole = $user->role;
            if ($userRole == 12 || $userRole == 11 || $userRole >= 777) {
                return 1;
            }

            return 0;
        });
    }

    private function registerAccessSettingBlade()
    {
        Blade::if('accessSetting', function ($setting) {
            /** @var Settings $setting */
            $setting = Settings::where('key', $setting)->first();

            if (!$setting) {
                return false;
            }

            if ($setting->value == '1') {
                return true;
            }

            return false;
        });
    }

    private function registerExcludeRoleBlade()
    {
        Blade::if('excludeRole', function ($dataRoles) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->role_manager) {
                return true;
            }

            $roles = User::$userRolesValues;
            foreach ($dataRoles as $role) {
                if (!isset($roles[$role])) {
                    continue;
                }

                $userRole = $user->role;
                if ($userRole == $roles[$role] && $userRole !== 777) {
                    return false;
                }
            }

            return true;
        });
    }

    private function registerRoleBlade()
    {
        Blade::if('role', function ($dataRoles) {
            /** @var User $user */
            $user = Auth::user();
            $userRole = $user->role;
            $isRoleManager = $user->role_manager;

            $validRoles = 0;

            $roles = User::$userRolesValues;
            foreach($dataRoles as $role) {
                if (!isset($roles[$role])) {
                    continue;
                }

                if (($userRole == $roles[$role] || ($isRoleManager && $role === 'manager')) || $userRole === 777) {
                    $validRoles++;
                }
            }

            return $validRoles;
        });
    }

    private function registerModelObservers()
    {
        Car::observe(CarObserver::class);
        Company::observe(CompanyObserver::class);
        Contract::observe(ContractObserver::class);
        Driver::observe(DriverObserver::class);
        User::observe(UserObserver::class);
    }
}
