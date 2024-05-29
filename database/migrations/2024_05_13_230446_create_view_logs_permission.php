<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class CreateViewLogsPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $permissionClass = app(PermissionContract::class);

        $permission = $permissionClass::findOrCreate('logs_read', 'Журнал действий - Просмотр');

        /** @var Role $role */
        $role = Role::query()
            ->where('name', 'admin')
            ->first();

        if (empty($role)) {
            throw new Exception('Default admin role does not exists!');
        }

        $role->permissions()->syncWithoutDetaching([$permission->id]);

        /** @var User $user */
        $user = User::query()
            ->withoutGlobalScopes()
            ->where('login', User::DEFAULT_USER_LOGIN)
            ->first();

        if (empty($user)) {
            throw new Exception('Default admin user does not exists!');
        }

        $user->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissionClass = app(PermissionContract::class);

        $permission = $permissionClass::findOrCreate('logs_read', 'Журнал действий - Просмотр');

        $permission->forceDelete();
    }
}
