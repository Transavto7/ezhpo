<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class AddUsersManagementPermissions extends Migration
{
    const PERMISSIONS = [
        'users_read' => 'Пользователи - Просмотр',
        'users_password_change' => 'Пользователи - Смена пароля',
        'users_block' => 'Пользователи - Блокировка/разблокировка',
        'users_access_change' => 'Пользователи - Настройка доступа',
        'users_logs_read' => 'Пользователи - Просмотр журнала действий',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $adminRole = Role::query()
            ->where('name', 'admin')
            ->first();

        if (empty($adminRole)) {
            throw new Exception('Default admin role does not exists!');
        }

        /** @var User $user */
        $user = User::query()
            ->withoutGlobalScopes()
            ->where('login', User::DEFAULT_USER_LOGIN)
            ->first();

        if (empty($user)) {
            throw new Exception('Default admin user does not exists!');
        }

        $permissionClass = app(PermissionContract::class);

        foreach (self::PERMISSIONS as $slug => $title) {
            $permission = $permissionClass::findOrCreate($slug, $title);

            $permissionId = $permission->id;

            $adminRole->permissions()->syncWithoutDetaching([$permissionId]);
            $user->permissions()->syncWithoutDetaching([$permissionId]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissionClass = app(PermissionContract::class);

        foreach (self::PERMISSIONS as $slug => $title) {
            $permission = $permissionClass::findOrCreate($slug, $title);

            $permission->forceDelete();
        }
    }
}
