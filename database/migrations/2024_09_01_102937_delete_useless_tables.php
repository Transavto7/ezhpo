<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class DeleteUselessTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('anketa_services_discount_snapshot_contracts');
        Schema::dropIfExists('companies_2');
        Schema::dropIfExists('contract_anketa_snapshot');
        Schema::dropIfExists('side_bar_menu_items');
        Schema::dropIfExists('work_reports');
        Schema::dropIfExists('notifies');
        Schema::dropIfExists('notify_statuses');
        Schema::dropIfExists('services');
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
