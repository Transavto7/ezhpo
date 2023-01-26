<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('hash_id')->default('000000')->unique();
            $table->longText('name'); // Название твоара
            $table->string('type_product')->nullable();
            $table->string('unit')->default('шт.'); // Единица измереения
            $table->integer('price_unit')->default(0); // Стоимость за единицу

            $table->string('type_anketa')->nullable();
            $table->string('type_view')->nullable();

            $table->integer('essence')->nullable();

            $table->integer('deleted_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('services');
    }
}
