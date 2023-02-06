<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixFieldsIntoDdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('d_dates', function (Blueprint $table) {
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
        });

        Schema::table('field_histories', function (Blueprint $table) {
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
        });

        \App\FieldPrompt::create([
            'type' => 'ddates',
            'field' => 'item_model',
            'name' => 'Сущность'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
}
