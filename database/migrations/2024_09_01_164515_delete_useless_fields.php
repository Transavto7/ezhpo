<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteUselessFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns = [
            'anketas' => [
                'contract_id',
                'contract_snapshot_id',
                'date_number_list_road',
                'date_pechat_pl',
                'type_trip',
                'questions',
                'added_to_dop',
                'added_to_mo',
                'connected_hash'
            ],
            'cars' => [
                'old_id',
                'contract_id'
            ],
            'drivers' => [
                'old_id',
                'contract_id',
                'time_of_pressure_ban',
                'time_of_alcohol_ban'
            ],
            'companies' => [
                'time_of_pressure_ban',
                'time_of_alcohol_ban'
            ]
        ];

        $foreign = [
            'anketas' => [
                'contract_id',
                'contract_snapshot_id'
            ],
            'cars' => [
                'contract_id'
            ],
            'drivers' => [
                'contract_id'
            ]
        ];

        foreach ($foreign as $table => $keys) {
            foreach ($keys as $key) {
                Schema::table($table, function (Blueprint $table) use ($key) {
                    $table->dropForeign($key);
                });
            }
        }

        foreach ($columns as $table => $keys) {
            Schema::table($table, function (Blueprint $table) use ($keys) {
                $table->dropColumn($keys);
            });
        }
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
