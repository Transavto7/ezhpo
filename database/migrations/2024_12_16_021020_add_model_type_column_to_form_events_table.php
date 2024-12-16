<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModelTypeColumnToFormEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('form_events', 'model_type')) {
            return;
        }

        Schema::table('form_events', function (Blueprint $table) {
            $table->string('model_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_events', function (Blueprint $table) {
            $table->dropColumn('model_type');
        });
    }
}
