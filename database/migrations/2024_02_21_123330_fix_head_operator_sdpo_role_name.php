<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;

class FixHeadOperatorSdpoRoleName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $name = 'Старший оператор СДПО';
        $slug = 'head_operator_sdpo';

        Role::query()->withTrashed()->updateOrCreate(
            [
                'guard_name' => $name
            ],
            [
                'name' => $slug,
                'deleted_at' => null
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
