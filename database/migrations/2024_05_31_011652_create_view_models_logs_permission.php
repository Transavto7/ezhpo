<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class CreateViewModelsLogsPermission extends Migration
{
    const PERMISSIONS = [
        'contract_logs_read' => 'Договор - Просмотр журнала действий',
        'drivers_logs_read' => 'Водители - Просмотр журнала действий',
        'cars_logs_read' => 'Автомобили - Просмотр журнала действий',
        'company_logs_read' => 'Компании - Просмотр журнала действий',
        'service_logs_read' => 'Услуги - Просмотр журнала действий'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        /** @var Role $role */
        $role = Role::query()
            ->where('name', 'admin')
            ->first();

        if (empty($role)) {
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

            $role->permissions()->syncWithoutDetaching([$permission->id]);

            $user->permissions()->syncWithoutDetaching([$permission->id]);
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
