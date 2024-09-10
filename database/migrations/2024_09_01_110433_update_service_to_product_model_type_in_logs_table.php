<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateServiceToProductModelTypeInLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('logs')
            ->where('model_type', 'App\Models\Service')
            ->update([
                'model_type' => 'App\Product'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('logs')
            ->where('model_type', 'App\Product')
            ->update([
                'model_type' => 'App\Models\Service'
            ]);
    }
}
