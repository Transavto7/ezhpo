<?php

namespace App\Providers;

use App\Settings;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
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
