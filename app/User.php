<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    public $fillable
        = [
            'hash_id',
            'req_id',
            'photo',
            'name',
            'email',
            'password',
            'eds',
            'pv_id',
            'timezone',
            'role',
            'role_manager',
            'blocked',
            'pv_id_default',
            'api_token',
            'login',
            'user_post',
            'company_id',
        ];

    protected $hidden
        = [
            'password',
        ];

    protected $casts
        = [
            'email_verified_at' => 'datetime',
        ];

    public function anketas()
    {
        return $this->hasMany(Anketa::class, 'user_id', 'id')
                    ->withDefault();
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')
                    ->withDefault();
    }

    public function pv()
    {
        return $this->belongsTo(Point::class, 'pv_id')
                    ->withDefault();
    }

    public static $newUserRolesText
        = [
            1 => 'Контролёр ТС',
            2 => 'Медицинский сотрудник',
            3 => 'Водитель',
            4 => 'Оператор СДПО',
            5 => 'Менеджер',
            6 => 'Клиент',
            7 => 'Инженер БДД',
            8 => 'Администратор',
            9 => 'Терминал',
        ];
    public static $newUserRolesTextEN
        = [
            1 => 'tech',
            2 => 'medic',
            3 => 'driver',
            4 => 'operator_sdpo',
            5 => 'manager',
            6 => 'client',
            7 => 'engineer_bdd',
            8 => 'admin',
            9 => 'terminal',
        ];

    // check access
    public function access(...$permissionName)
    {

        return $this->getAllPermissions()
                    ->whereIn('name', $permissionName)
                    ->isNotEmpty();

//        foreach (){
//            $this->getAllPermissions()->where()
//        }
//        if(count($permissionName) === 1){
//            return $this->hasPermissionTo($permissionName[0]);
//        }else{
//            return $this->hasAnyPermission($permissionName);
//        }
    }

    public static function fetchRoles()
    {
//        DB::statement("SET foreign_key_checks=0");
//        Role::truncate();
//        DB::statement("SET foreign_key_checks=1");
//
//        foreach (self::$newUserRolesTextEN as $keyRole => $nameRole) {
//            Role::updateOrCreate([
//                'name'       => $nameRole,
//                'guard_name' => self::$newUserRolesText[$keyRole],
//            ]);
//        }

        $permissions = collect(config('access'));

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name'       => $permission['name'],
                'guard_name' => $permission['description'],
            ]);
        }

        foreach (User::get() as $user) {
            if($user->roles()){
                continue;
            }
            switch ($user->role) {
                case 1:
                    $user->roles()->attach(1);
                    break;
                case 2:
                    $user->roles()->attach(2);
                    break;
                case 3:
                    $user->roles()->attach(3);
                    break;
                case 4:
                    $user->roles()->attach(4);
                    break;
                case 11:
                    $user->roles()->attach(5);
                    break;
                case 12:
                    $user->roles()->attach(6);
                    break;
                case 13:
                    $user->roles()->attach(7);
                    break;
                case 777:
                    $user->roles()->attach(8);
                    break;
                case 778:
                    $user->roles()->attach(9);
                    break;
            }
            if ($user->role_manager == 1) {
                $user->roles()->attach(5);
            }
        }

        self::fetchOldDataPermission($permissions);
    }

    public static function fetchOldDataPermission($permissions)
    {
        // permission for admin
        $adminRole = Role::where('name', 'admin')->first();
        $terminalRole = Role::where('name', 'terminal')->first();

        $allPermissions = Permission::get();

        $adminRole->permissions()->sync($allPermissions->pluck('id'));
        $terminalRole->permissions()->sync($allPermissions->pluck('id'));

        // permission for tech
        $techRole = Role::where('name', 'tech')->first();



        // admin permissions
        $permissionIgnore = [
            'client_create',
            'report_service_company_read',
            'report_schedule_pv_read',
            'report_schedule_pv_read',
            'discount_create',
            'discount_read',
            'discount_update',
            'discount_delete',
            'briefings_create',
            'briefings_read',
            'briefings_update',
            'briefings_delete',
            'settings_read',
            'system_create',
            'system_read',
            'system_update',
            'system_delete',
            'settings_system_create',
            'settings_system_read',
            'settings_system_update',
            'settings_system_delete',
            'city_create',
            'city_read',
            'city_update',
            'city_delete',
            'pv_create',
            'pv_read',
            'pv_update',
            'pv_delete',
            'employee_create',
            'employee_read',
            'employee_update',
            'employee_delete',
            'pak_sdpo_create',
            'pak_sdpo_read',
            'pak_sdpo_update',
            'pak_sdpo_delete',
            'date_control_create',
            'date_control_read',
            'date_control_update',
            'date_control_delete',
            'story_field_create',
            'story_field_read',
            'story_field_update',
            'story_field_delete',
            'requisites_create',
            'requisites_read',
            'requisites_update',
            'requisites_delete',
            'releases_read',

            'tech_create',
            //            'tech_read',
            'tech_update',
            'tech_delete',
            'tech_trash',

            'medic_create',
            //            'medic_read',
            'medic_update',
            'medic_delete',
            'medic_trash',
        ];
        foreach ($allPermissions as $permission) {
            if (in_array($permission->name, [
                'tech_create',
                //                'tech_read',
                'tech_update',
                'tech_trash',
            ])) {
                $techRole->permissions()->sync($permission->pluck('id'));
                continue;
            }
            if (in_array($permission->name, $permissionIgnore)) {
                continue;
            }
            $techRole->permissions()->sync($permission->pluck('id'));
        }
        $medicRole = Role::where('name', 'medic')->first();

        foreach ($allPermissions as $permission) {
            // Это нам надо
            if (in_array($permission->name, [
                'report_service_company_read',
                'report_schedule_pv_read',
                'medic_trash',
            ])) {
                $medicRole->permissions()->sync($permission->pluck('id'));
                continue;
            }
            // Это нам не надо
            if (in_array($permission->name, $permissionIgnore)) {
                continue;
            }
            // всё отсальное надо
            $medicRole->permissions()->sync($permission->pluck('id'));
        }
        $operator_sdpoRole = Role::where('name', 'operator_sdpo')->first();

        foreach ($allPermissions as $permission) {
            // Это нам не надо
            if (in_array($permission->name, [
                'service_create',
                'service_read',
                'service_update',
                'service_delete',
            ])) {
                continue;
            }
            if (in_array($permission->name, [
                'medic_create',
                'medic_update',
                'medic_trash',
            ])) {
                $operator_sdpoRole->permissions()->sync($permission->pluck('id'));
                continue;
            }
            if (in_array($permission, $permissionIgnore)) {
                continue;
            }
            // всё отсальное надо
            $operator_sdpoRole->permissions()->sync($permission->pluck('id'));
        }
    }


    public static $userRolesValues
        = [
            'client'       => 12,
            'tech'         => 1,
            'medic'        => 2,
            'driver'       => 3,
            'terminal'     => 778,
            'engineer_bdd' => 12,
            'manager'      => 11,
            'admin'        => 777,
            'operator_pak' => 4,
        ];

    public static $userRolesKeys
        = [
            '0'   => '',
            '1'   => 'tech',
            '2'   => 'medic',
            '4'   => 'pak_queue',
            '12'  => 'medic',
            '11'  => 'medic',
            '777' => 'medic',
            '778' => 'medic',
        ];

    public static $userRolesText
        = [
            1   => 'Контролёр ТС',
            2   => 'Медицинский сотрудник',
            3   => 'Водитель',
            4   => 'Оператор СДПО',
            11  => 'Менеджер',
            12  => 'Клиент',
            13  => 'Инженер БДД',
            777 => 'Администратор',
            778 => 'Терминал',
        ];

    public static function getUserCompanyId($field = 'id')
    {
        $point = auth()->user()->pv_id;
        $point = Point::find($point);

        if ($point) {
            $company = $point->company_id ? Company::fiqnd($point->company_id) : 0;

            if ($company) {
                return $company->$field;
            } else {
                return -1;
            }
        }

        return -1;
    }

//    public function hasRole($role, $prefix = '>=')
//    {
//        if (isset(self::$userRolesValues[$role])) {
//            $c_role = self::$userRolesValues[$role];
//            $user   = auth()->user();
//
//            eval('$expr = '.$user->role.$prefix.$c_role.';');
//
//            if ($expr) {
//                return true;
//            }
//
//        }
//
//        return false;
//    }

    /**
     * Проверка админа пользователя
     */
//    public static function isAdmin()
//    {
//        return Auth::user()->role >= 777;
//    }

//    public static function getAll()
//    {
//        return self::where('role', '!=', 3)->get();
//    }

    /**
     * Получение имени юзера
     *
     * @param $id
     *
     * @return string
     */

    public function getName($id = -1, $authId = true)
    {
        $id = $id ? $id : ($authId ? auth()->user()->id : -1);

        $userName = User::find($id);

        if ($userName) {
            $userName = $userName->name;
        } else {
            $userName = '';
        }

        return $userName;
    }
}
