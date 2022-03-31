<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash_id')->default('000000')->unique();
            $table->string('req_id')->default(0);
            $table->string('user_id')->nullable(); // Ответственный
            $table->string('pv_id')->nullable(); // Пункт выпуска
            $table->string('town_id')->nullable(); // Город
            $table->bigInteger('inn')->nullable(); // ИНН
            $table->string('products_id')->nullable(); // ID товаров через запятую

            $table->string('where_call')->nullable(); // Кому звонить при отстранении”, тип - текст. Маска - сотовый номер телефона

            $table->longText('name');
            //$table->string('payment_form')->nullable();
            $table->string('procedure_pv')->default('Фактовый'); // Форма выпуска

            $table->longText('note')->nullable(); // Примечание

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
        Schema::dropIfExists('companies');
    }
}
