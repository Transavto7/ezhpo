<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'hash_id', 'req_id', 'photo', 'name', 'email', 'password', 'eds', 'pv_id', 'timezone', 'role', 'role_manager', 'blocked', 'pv_id_default', 'api_token',
        'login', 'user_post'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $userRolesValues = [
        'client' => 12,
        'tech' => 1,
        'medic' => 2,
        'driver' => 3,
        'terminal' => 778,
        'engineer_bdd' => 12,
        'manager' => 11,
        'admin' => 777,
        'operator_pak' => 4
    ];

    public static $userRolesKeys = [
        '0' => '',
        '1' => 'tech',
        '2' => 'medic',
        '4' => 'pak_queue',
        '12' => 'medic',
        '11' => 'medic',
        '13' => 'bdd',
        '777' => 'medic',
        '778' => 'medic'
    ];

    public static function getUserCompanyId ($field = 'id')
    {
        $point = auth()->user()->pv_id;
        $point = Point::find($point);

        if($point) {
            $company = $point->company_id ? Company::find($point->company_id) : 0;

            if($company) {
                return $company->$field;
            } else {
                return -1;
            }
        }

        return -1;
    }

    public function hasRole ($role, $prefix = '>=')
    {
        if(isset(self::$userRolesValues[$role])) {
            $c_role = self::$userRolesValues[$role];
            $user = auth()->user();

            eval('$expr = ' . $user->role . $prefix . $c_role . ';');

            if($expr) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка админа пользователя
     */
    public static function isAdmin () {
        return Auth::user()->role >= 777;
    }

    public static function getAll () {
        return self::where('role', '!=', 3)->get();
    }

    /**
     * Получение имени юзера
     * @param $id
     * @return string
     */

    public function getName ($id = -1, $authId = true)
    {
        $id = $id ? $id : ($authId ? auth()->user()->id : -1);

        $userName = User::find($id);

        if($userName) {
            $userName = $userName->name;
        } else {
            $userName = '';
        }

        return $userName;
    }
}
