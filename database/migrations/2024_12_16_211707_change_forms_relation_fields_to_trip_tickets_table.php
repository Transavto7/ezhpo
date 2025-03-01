<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFormsRelationFieldsToTripTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->dropForeign('trip_tickets_medic_form_id_foreign');
            $table->dropForeign('trip_tickets_tech_form_id_foreign');
            $table->dropColumn('medic_form_id');
            $table->dropColumn('tech_form_id');
        });

        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('medic_form_id')->nullable()->after('validity_period');
            $table->foreign('medic_form_id')->references('id')->on('forms');

            $table->unsignedBigInteger('tech_form_id')->nullable()->after('driver_id');
            $table->foreign('tech_form_id')->references('id')->on('forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->dropForeign('trip_tickets_medic_form_id_foreign');
            $table->dropForeign('trip_tickets_tech_form_id_foreign');
            $table->dropColumn('medic_form_id');
            $table->dropColumn('tech_form_id');
        });

        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->string('medic_form_id')->nullable()->after('validity_period');
            $table->foreign('medic_form_id')->references('uuid')->on('forms');

            $table->string('tech_form_id')->nullable()->after('driver_id');
            $table->foreign('tech_form_id')->references('uuid')->on('forms');
        });
    }
}
