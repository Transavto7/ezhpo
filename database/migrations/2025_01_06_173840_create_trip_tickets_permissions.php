<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Contracts\Permission as PermissionContract;

class CreateTripTicketsPermissions extends Migration
{
    const PERMISSIONS = [
        'trip_tickets_read' => 'Путевые листы - Просмотр',
        'trip_tickets_create' => 'Путевые листы - Создание',
        'trip_tickets_edit' => 'Путевые листы - Редактирование',
        'trip_tickets_delete' => 'Путевые листы - Удаление',
        'trip_tickets_trash' => 'Путевые листы - Корзина',
        'trip_tickets_create_medic_form' => 'Путевые листы - Создание МО через ПЛ',
        'trip_tickets_create_tech_form' => 'Путевые листы - Создание ТО через ПЛ',
        'trip_tickets_export' => 'Путевые листы - Экспорт',
        'trip_tickets_export_prikaz' => 'Путевые листы - Экспорт приказ',
        'trip_tickets_print_trip_ticket' => 'Путевые листы - Печать',
    ];

    const MEDIC_TECH_PERMISSIONS = [
        'trip_tickets_read',
        'trip_tickets_create',
        'trip_tickets_edit',
        'trip_tickets_delete',
        'trip_tickets_trash',
        'trip_tickets_print_trip_ticket',
    ];

    const MANAGER_ENGINEER_BRANCH_HEAD_PERMISSIONS = [
        'trip_tickets_read',
        'trip_tickets_export',
        'trip_tickets_export_prikaz',
    ];

    const CLIENT_COMMERCIAL_HEAD_PERMISSIONS = [
        'trip_tickets_read',
        'trip_tickets_export_prikaz',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        DB::beginTransaction();

        try {
            /** @var Role $admin */
            $admin = Role::query()
                ->where('name', 'admin')
                ->first();

            if (empty($admin)) {
                throw new Exception('Default admin role does not exists!');
            }

            /** @var Role $tech */
            $tech = Role::query()
                ->where('name', 'tech')
                ->first();

            if (empty($tech)) {
                throw new Exception('Role tech does not exists!');
            }

            /** @var Role $medic */
            $medic = Role::query()
                ->where('name', 'medic')
                ->first();

            if (empty($medic)) {
                throw new Exception('Role medic does not exists!');
            }

            /** @var Role $manager */
            $manager = Role::query()
                ->where('name', 'manager')
                ->first();

            if (empty($manager)) {
                throw new Exception('Role manager does not exists!');
            }

            /** @var Role $engineerBdd */
            $engineerBdd = Role::query()
                ->where('name', 'engineer_bdd')
                ->first();

            if (empty($engineerBdd)) {
                throw new Exception('Role engineer_bdd does not exists!');
            }

            /** @var Role $branchHead */
            $branchHead = Role::query()
                ->where('name', 'role_223581000')
                ->first();

            if (empty($branchHead)) {
                throw new Exception('Role role_223581000 does not exists!');
            }

            /** @var Role $commercialHead */
            $commercialHead = Role::query()
                ->where('name', 'role_262761000')
                ->first();

            if (empty($commercialHead)) {
                throw new Exception('Role role_262761000 does not exists!');
            }

            /** @var Role $client */
            $client = Role::query()
                ->where('name', 'client')
                ->first();

            if (empty($client)) {
                throw new Exception('Role client does not exists!');
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

                $admin->permissions()->syncWithoutDetaching([$permissionId]);
                $user->permissions()->syncWithoutDetaching([$permissionId]);

                if (in_array($slug, self::MEDIC_TECH_PERMISSIONS)) {
                    $medic->permissions()->syncWithoutDetaching([$permissionId]);
                    $tech->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if ($slug === 'trip_tickets_create_medic_form') {
                    $medic->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if ($slug === 'trip_tickets_create_tech_form') {
                    $tech->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if (in_array($slug, self::MANAGER_ENGINEER_BRANCH_HEAD_PERMISSIONS)) {
                    $manager->permissions()->syncWithoutDetaching([$permissionId]);
                    $engineerBdd->permissions()->syncWithoutDetaching([$permissionId]);
                    $branchHead->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if (in_array($slug, self::CLIENT_COMMERCIAL_HEAD_PERMISSIONS)) {
                    $client->permissions()->syncWithoutDetaching([$permissionId]);
                    $commercialHead->permissions()->syncWithoutDetaching([$permissionId]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws Exception
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
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
