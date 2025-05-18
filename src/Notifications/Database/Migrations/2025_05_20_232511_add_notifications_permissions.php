<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class AddNotificationsPermissions extends Migration
{
    const PERMISSIONS = [
        'notifications_logs' => 'Уведомления - Просмотр журнала действий с уведомлениями',
        'notifications_view_other' => 'Уведомления - Просмотр всех уведомлений',
        'notifications_change_other' => 'Уведомления - Прочтение и выполнение всех уведомлений',
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
