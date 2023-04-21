<?php

namespace App\Providers;

use App\Http\Controllers\SidebarMenuItemsController;
use App\Http\Controllers\WorkReportsController;
use App\Services\Contracts\BaseInspectionService;
use App\Services\Contracts\ServiceInterface;
use App\Services\Inspections\MedicalInspectionService;
use App\Services\SidebarService;
use App\Services\WorkReportService;
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
        $this->app->when(SidebarMenuItemsController::class)
            ->needs(ServiceInterface::class)
            ->give(SidebarService::class)
        ;

        $this->app->when(WorkReportsController::class)
            ->needs(ServiceInterface::class)
            ->give(WorkReportService::class)
        ;

        $this->app->singleton(BaseInspectionService::class, MedicalInspectionService::class);
        $this->app->singleton(ServiceInterface::class, WorkReportService::class);

//        if ($this->app->isLocal()) {
//            $this->app->register(TelescopeServiceProvider::class);
//        }

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Blade::if('admin', function () {
            $user = Auth::user();

            if($user->role >= 777) {
                return 1;
            }

            return 0;
        });

        Blade::if('manager', function () {
            $user = Auth::user();

            if($user->role == 12 || $user->role == 11 || $user->role_manager || $user->role >= 777) {
                return 1;
            }

            return 0;
        });

        Blade::if('accessSetting', function ($setting) {
            $setting = Settings::where('key', $setting)->first();
            if (!$setting) {
                return false;
            }
            if ($setting->value == '1') {
                return true;
            }

            return false;
        });

        Blade::if('excludeRole', function ($dataRoles) {
            $roles = User::$userRolesValues;
            $user = Auth::user();
            $user_role = $user->role;

            $excluded = true;

            foreach($dataRoles as $role) {
                if(isset($roles[$role])) {
                    if($user_role == $roles[$role] && $user_role !== 777) {
                        $excluded = false;
                    }
                }
            }

            if($user->role_manager) {
                $excluded = true;
            }

            return $excluded;
        });

        Blade::if('role', function ($dataRoles) {
            $roles = User::$userRolesValues;
            $user_role = Auth::user()->role;
            $is_role_manager = Auth::user()->role_manager;


            $validRoles = [];

            foreach($dataRoles as $role) {
                if(isset($roles[$role])) {
                    if(($user_role == $roles[$role] || ($is_role_manager && $role === 'manager')) || $user_role === 777) {
                        $validRoles[] = 1;
                    }
                }
            }

            return count($validRoles);
        });
    }
}
