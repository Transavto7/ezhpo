<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnketaVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anketa_verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('anketa_uuid', 36)->index();
            $table->dateTime('verification_date');
            $table->string('client_hash');
            $table->string('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anketa_verifications');
    }
}
