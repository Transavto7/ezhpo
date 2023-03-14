<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\User
 *
 * @property string $name
 * @property int $id
 * @property Collection $companies
 * @property string $hash_id
 * @property int|null $req_id
 * @property int $role
 * @property int $role_manager
 * @property int $blocked
 * @property int $pv_id
 * @property int $pv_id_default
 * @property string|null $eds
 * @property string|null $photo
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $timezone
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $login
 * @property string|null $user_post
 * @property int|null $company_id
 * @property string|null $deleted_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $fields_visible
 * @property \Illuminate\Support\Carbon|null $last_connection_at
 * @property-read Collection|\App\Anketa[] $anketas
 * @property-read int|null $anketas_count
 * @property-read int|null $companies_count
 * @property-read \App\Company|null $company
 * @property-read User|null $deleted_user
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Point $pv
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFieldsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHashId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastConnectionAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePvIdDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReqId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserPost($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
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
            'last_connection_at'
        ];

    protected $hidden
        = [
            'password',
        ];

    protected $casts
        = [
            'email_verified_at' => 'datetime',
            'last_connection_at' => 'datetime',
        ];

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
//                    ->withDefault()
            ;
    }
    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }

    public function roles($deleted = false) : BelongsToMany
    {
        return $this->belongsToMany(Role::class,
            'model_has_roles',
            'model_id',
            'role_id',
            'id',
            'id'
        )->withPivot('deleted')
        ->wherePivot('deleted', $deleted ? 1 : 0);
    }

    public function anketas()
    {
        return $this->hasMany(Anketa::class, 'user_id', 'id')
            ->withDefault()
            ;
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')
            ->withDefault()
            ;
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function pv()
    {
        return $this->belongsTo(Point::class, 'pv_id')
            ->withDefault()
            ;
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
