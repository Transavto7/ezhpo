<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use Notifiable;

    public $fillable = [
        'hash_id', 'req_id', 'photo', 'name', 'email', 'password', 'eds', 'pv_id', 'timezone', 'role', 'role_manager', 'blocked', 'pv_id_default', 'api_token',
        'login', 'user_post', 'company_id'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // sorry for name
    public function anketas()
    {
        return $this->hasMany(Anketa::class, 'user_id', 'id');
    }

    // sorry for name
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')
                    ->withDefault();
    }

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
        '777' => 'medic',
        '778' => 'medic'
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
