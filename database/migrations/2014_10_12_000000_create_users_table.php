<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash_id')->default('000000')->unique();
            $table->integer('req_id')->nullable();
            $table->integer('role')->default(0);
            $table->integer('role_manager')->default(0);
            $table->integer('blocked')->default(0); // 0 - нет, 1 - да
            $table->integer('pv_id')->default(0); // ID-пункта выпуска
            $table->integer('pv_id_default')->default(1); // Дефолтный ID пункта выпуска
            $table->string('name')->nullable();
            $table->string('eds')->nullable();
            $table->text('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('login')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('user_post')->nullable();
            $table->string('timezone')->nullable();
            $table->text('api_token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
