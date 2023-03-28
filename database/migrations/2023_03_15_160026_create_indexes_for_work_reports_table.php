<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexesForWorkReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_reports', function (Blueprint $table) {
            $table->index('user_id', 'work_reports_user_id_idx');
            $table->index('pv_id', 'work_reports_pv_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_reports', function (Blueprint $table) {
            $table->dropIndex('work_reports_pv_id_idx');
            $table->dropIndex('work_reports_user_id_idx');
        });
    }
}
