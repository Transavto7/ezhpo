<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdpoCrashLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sdpo_crash_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('uuid', 36)->unique();
            $table->string('type');
            $table->string('version');

            $table->jsonb('data')->nullable();

            $table->unsignedBigInteger('terminal_id');
            $table->foreign('terminal_id')->references('id')->on('users');

            $table->unsignedBigInteger('point_id');
            $table->foreign('point_id')->references('id')->on('points');

            $table->timestamp('happened_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sdpo_crash_logs');
    }
}
