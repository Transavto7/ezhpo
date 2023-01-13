<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use Notifiable, HasRoles, SoftDeletes;

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
            'deleted_id',
        ];

    protected $hidden
        = [
            'password',
        ];

    protected $casts
        = [
            'email_verified_at' => 'datetime',
        ];

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
                    ->withDefault();
    }
    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }

    public function roles($deleted = false) : BelongsToMany
    {
        // pizdec huita///.....// prosto nahui relationship
        return $this->belongsToMany(\App\Role::class,
            'model_has_roles',
            'model_id',
            'role_id',
            'id',
            'id'
        )->withPivot('deleted')
        ->wherePivot('deleted', $deleted ? 1 : 0);
    }

//    public function scopePermissions() : BelongsToMany
//    {
//        return parent::permissions()->wherePivot('deleted',0);
//    }
//    public function roles() : BelongsToMany
//    {
//        // pizdec huita///.....// prosto nahui relationship
//        return $this->belongsToMany(\App\Role::class,
//            'model_has_roles',
//            'model_id',
//            'role_id',
//            'id',
//            'id'
//        )->withPivot('deleted')
//                    ->wherePivot('deleted', 0);
//    }

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
    }

    public static function fetchPermissions()
    {
        $permissions = collect(config('access'));
        DB::statement("SET foreign_key_checks=0");

        $counter['added'] = 0;
        foreach ($permissions as $permission) {
            if($updatePermission = Permission::where('name', $permission['name'])->first()){
                $updatePermission->guard_name = $permission['description'];

                $updatePermission->save();
            }else{
                $counter['added']++;
                Permission::create([
                    'name'       => $permission['name'],
                    'guard_name' => $permission['description'],
                ]);
            }
        }
        $deleted = Permission::whereNotIn('name', $permissions->pluck('name'));
        $counter['deleted'] = $deleted->count();
        $deleted->delete();

        $counter['total'] = Permission::count();

        DB::statement("SET foreign_key_checks=1");

        return $counter;
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
            '0'   => 'medic',
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
            $company = $point->company_id ? Company::find($point->company_id) : 0;

            if ($company) {
                return $company->$field;
            } else {
                return -1;
            }
        }

        return -1;
    }

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
