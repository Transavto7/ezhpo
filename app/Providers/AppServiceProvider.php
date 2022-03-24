<?php

namespace App\Providers;

use App\SystemSetting;
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
        //
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

            if($user->role == 11 || $user->role_manager >= 1 || $user->role >= 777) {
                return 1;
            }

            return 0;
        });

        Blade::if('accessSetting', function ($setting, $category) {
            $accessed = SystemSetting::check($setting, $category);

            return !!$accessed;
        });

        Blade::if('role', function ($dataRoles) {
            $roles = User::$userRolesValues;
            $user_role = Auth::user()->role;
            $is_role_manager = Auth::user()->role_manager;

            $validRoles = [];

            foreach($dataRoles as $role) {
                if(isset($roles[$role])) {
                    if(($user_role === $roles[$role] || ($is_role_manager && $role === 'manager')) || $user_role === 777) {
                        array_push($validRoles, 1);
                    }
                }
            }

            return count($validRoles);
        });
    }
}
