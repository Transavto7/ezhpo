<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class AddDeletePermissionForWorkdays extends Migration
{
    const PERMISSIONS = [
        'employees_workdays_trash' => 'Рабочие смены - Удаление',
    ];

    const ROLES = [
        'admin',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            /** @var Role $role */
            $roles = Role::query()
                ->whereIn('name', self::ROLES)
                ->get();

            if ($roles->count() !== count(self::ROLES)) {
                throw new Exception('Some roles not found!');
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

                $roles->each(function ($role) use ($permissionId) {
                    $role->permissions()->syncWithoutDetaching([$permissionId]);
                });

                $user->permissions()->syncWithoutDetaching([$permissionId]);
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws Throwable
     */
    public function down()
    {
        DB::beginTransaction();

        try {
            $permissionClass = app(PermissionContract::class);

            foreach (self::PERMISSIONS as $slug => $title) {
                $permission = $permissionClass::findOrCreate($slug, $title);

                $permission->forceDelete();
            }
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
