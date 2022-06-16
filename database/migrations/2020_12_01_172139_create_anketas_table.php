<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnketasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anketas', function (Blueprint $table) {
            $table->bigIncrements('id');
            /**
             * Общие поля
             */
            $table->string('type_anketa')->default('medic'); // 0 - медицинский осмотр, 1 - технический осмотр
            $table->integer('user_id')->default(0);
            $table->string('user_eds')->nullable();
            $table->string('user_name')->nullable();

            $table->text('pv_id')->nullable();

            $table->integer('driver_id')->nullable();
            $table->string('driver_group_risk')->nullable();
            $table->string('driver_fio')->nullable();
            $table->string('driver_gender')->nullable();
            $table->date('driver_year_birthday')->nullable();

            $table->string('car_id')->nullable();
            $table->string('car_mark_model')->nullable();
            $table->string('car_gos_number')->nullable();

            /**
             * Новые поля от 07.11
             */
            $table->string('complaint')->default('Нет'); // Жалобы
            $table->string('condition_visible_sliz')->default('Без особенностей');
            $table->string('condition_koj_pokr')->default('Без особенностей');

            $table->timestamp('date')->nullable();
            $table->string('number_list_road')->nullable();


            $table->string('type_view')->default('Предрейсовый'); // Тип осмотра

            // НОВЫЕ ПОЛЯ БЕЗ СВЯЗЕЙ ОТ 07.08
            $table->integer('company_id')->nullable();
            $table->longText('company_name')->nullable();

            /**
             * Медосмотр
             */
            $table->string('tonometer')->nullable();
            $table->float('t_people')->nullable();
            $table->string('proba_alko')->default('Отрицательно'); // Проба на алкоголь
            $table->string('test_narko')->default('Не проводился'); // Тест на наркотики
            $table->string('med_view')->default('В норме'); // Мед показания
            $table->string('admitted')->default('Не допущен'); // Мед показания

            // ПАК - 12.04
            $table->integer('pulse')->nullable();
            $table->integer('alcometer_mode')->default(0);
            $table->integer('alcometer_result')->nullable();
            $table->text('type_trip')->nullable();
            $table->text('questions')->nullable();

            /**
             * Техосмотр ПАК
             */
            $table->string('odometer')->nullable();
            $table->string('point_reys_control')->default('Исправен');

            /**
             * Системные поля
             */
            $table->integer('in_cart')->default(0);

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
        Schema::dropIfExists('anketas');
    }
}
