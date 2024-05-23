<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class CreateExportElementPermission extends Migration
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

        $permissionDriver = $permissionClass::findOrCreate('drivers_export', 'Водители - Экспорт');
        $permissionCar = $permissionClass::findOrCreate('cars_export', 'Автомобили - Экспорт');

        /** @var Role $role */
        $role = Role::query()
            ->where('name', 'admin')
            ->first();

        if (empty($role)) {
            throw new Exception('Default admin role does not exists!');
        }

        $role->permissions()->syncWithoutDetaching([$permissionDriver->id, $permissionCar->id]);

        /** @var User $user */
        $user = User::query()
            ->withoutGlobalScopes()
            ->where('login', User::DEFAULT_USER_LOGIN)
            ->first();

        if (empty($user)) {
            throw new Exception('Default admin user does not exists!');
        }

        $user->permissions()->syncWithoutDetaching([$permissionDriver->id, $permissionCar->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissionClass = app(PermissionContract::class);

        $permission = $permissionClass::findOrCreate('drivers_export', 'Водители - Экспорт');
        $permission->forceDelete();

        $permission = $permissionClass::findOrCreate('cars_export', 'Автомобили - Экспорт');
        $permission->forceDelete();
    }
}
