<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('old_id')->nullable();
            $table->string('hash_id')->default('000000')->unique();
            $table->integer('company_id')->default(0);
            $table->string('gos_number')->default('0');
            $table->string('mark_model')->default('-');
            $table->string('type_auto')->default('Не выбрано');
            $table->string('products_id')->nullable(); // ID товаров через запятую
            $table->string('trailer')->default(0);
            $table->date('date_osago')->nullable();
            $table->date('date_prto')->nullable(); // Дата ПРТО
            $table->date('date_techview')->nullable(); // Дата техосмотра
            $table->date('time_skzi')->nullable(); // Срок действия СКЗИ
            $table->string('payment_form')->nullable(); // Форма оплаты
            $table->string('count_pl')->nullable(); // Количество выданных ПЛ
            $table->string('note')->nullable(); // Примечание
            $table->string('procedure_pv')->default('Фактовый'); // Порядок выпуска

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
        Schema::dropIfExists('cars');
    }
}
