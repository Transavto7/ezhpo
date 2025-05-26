<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \App\Role $medicRole */
        $medicRole = \App\Role::query()
            ->where('name', '=', 'medic')
            ->first();

        \App\Role::query()
            ->whereIn('name', ['head_operator_sdpo', 'operator_sdpo'])
            ->delete();
        $permission = \Spatie\Permission\Models\Permission::query()->updateOrCreate(['name' => 'approval_queue_view_all'],
            [
                'name' => 'approval_queue_view_all',
                'guard_name' => 'Очередь утверждения - Просмотр всего',
            ]
        );

        $medicRole->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('field_prompts')
            ->where('type', '=', 'company')
            ->where('field', '=', 'responsible_id')
            ->update([
                'field' => 'user_id',
            ]);
    }
}
