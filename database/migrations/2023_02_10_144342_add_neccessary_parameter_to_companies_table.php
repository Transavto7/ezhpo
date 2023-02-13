<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNeccessaryParameterToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean("required_type_briefing")
                  ->default(false)
                  ->nullable(false);
        });

        Schema::table("instrs", function (Blueprint $table) {
           $table->boolean("is_default")
               ->default(false)
               ->nullable(false)
               ->comment("Is this briefing default?");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->removeColumn("required_type_briefing");
        });
        Schema::table("instrs", function (Blueprint $table) {
            $table->removeColumn("is_default");
        });
    }
}
