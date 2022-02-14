<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('old_id')->nullable();
            $table->string('hash_id')->default('000000')->unique();
            $table->integer('company_id')->default(0);
            $table->string('fio')->default('Тест');
            $table->text('photo')->nullable();
            $table->date('year_birthday')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->default('Мужской');
            $table->string('payment_form')->nullable(); // Форма оплаты

            $table->string('products_id')->nullable(); // ID товаров через запятую

            $table->string('count_pl')->nullable(); // Количество выданных ПЛ
            $table->string('note')->nullable(); // Примечание
            $table->string('procedure_pv')->default('Фактовый'); // Порядок выпуска

            $table->date('date_bdd')->nullable(); // Дата БДД
            $table->date('date_prmo')->nullable(); // Дата ПРМО
            $table->date('date_report_driver')->nullable(); // Дата снятия отчета с карты водителя
            $table->date('time_card_driver')->nullable(); // Срок действия карты водителя

            // Группа риска (множественное поле)
            $table->string('group_risk')->nullable();

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
        Schema::dropIfExists('drivers');
    }
}
