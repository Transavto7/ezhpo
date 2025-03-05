<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWorkday extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workdays', function (Blueprint $table) {
            $table->renameColumn('is_allowed_work', 'admitted');
            $table->dropColumn('is_manual');
            $table->string('flag_pak', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workdays', function (Blueprint $table) {
            $table->dropColumn('flag_pak');
            $table->boolean('is_manual')->default(false);
            $table->renameColumn('admitted', 'is_allowed_work');
        });
    }
}
