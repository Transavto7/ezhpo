<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolePermissionToSideBarMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('side_bar_menu_items', function (Blueprint $table) {
            $table->string('access_role')->after('access_permissions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('side_bar_menu_items', function (Blueprint $table) {
            $table->dropColumn('access_role');
        });
    }
}
