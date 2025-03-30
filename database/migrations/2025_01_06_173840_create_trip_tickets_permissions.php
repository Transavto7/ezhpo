<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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

            /** @var Role $tech */
            $tech = Role::query()
                ->where('name', 'tech')
                ->first();

            /** @var Role $medic */
            $medic = Role::query()
                ->where('name', 'medic')
                ->first();

            /** @var Role $manager */
            $manager = Role::query()
                ->where('name', 'manager')
                ->first();

            /** @var Role $engineerBdd */
            $engineerBdd = Role::query()
                ->where('name', 'engineer_bdd')
                ->first();

            /** @var Role $branchHead */
            $branchHead = Role::query()
                ->where('name', 'role_223581000')
                ->first();

            /** @var Role $commercialHead */
            $commercialHead = Role::query()
                ->where('name', 'role_262761000')
                ->first();

            /** @var Role $client */
            $client = Role::query()
                ->where('name', 'client')
                ->first();

            /** @var User $user */
            $user = User::query()
                ->withoutGlobalScopes()
                ->where('login', User::DEFAULT_USER_LOGIN)
                ->first();

            $permissionClass = app(PermissionContract::class);

            foreach (self::PERMISSIONS as $slug => $title) {
                $permission = $permissionClass::findOrCreate($slug, $title);

                $permissionId = $permission->id;

                $admin && $admin->permissions()->syncWithoutDetaching([$permissionId]);
                $user && $user->permissions()->syncWithoutDetaching([$permissionId]);

                if (in_array($slug, self::MEDIC_TECH_PERMISSIONS)) {
                    $medic && $medic->permissions()->syncWithoutDetaching([$permissionId]);
                    $tech && $tech->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if ($slug === 'trip_tickets_create_medic_form') {
                    $medic && $medic->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if ($slug === 'trip_tickets_create_tech_form') {
                    $tech && $tech->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if (in_array($slug, self::MANAGER_ENGINEER_BRANCH_HEAD_PERMISSIONS)) {
                    $manager && $manager->permissions()->syncWithoutDetaching([$permissionId]);
                    $engineerBdd && $engineerBdd->permissions()->syncWithoutDetaching([$permissionId]);
                    $branchHead && $branchHead->permissions()->syncWithoutDetaching([$permissionId]);
                }

                if (in_array($slug, self::CLIENT_COMMERCIAL_HEAD_PERMISSIONS)) {
                    $client && $client->permissions()->syncWithoutDetaching([$permissionId]);
                    $commercialHead && $commercialHead->permissions()->syncWithoutDetaching([$permissionId]);
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
