<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSideBarMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('side_bar_menu_items', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Идентификатор');
            $table->string('title')->comment('Название');
            $table->string('route_name')->comment('Имя маршрута');
            $table->string('slug')->comment('Короткое имя');
            $table->string('access_permissions')->nullable()->comment('Права доступа для отображения');
            $table->string('css_class')->nullable()->comment('Стили отображения');
            $table->text('tooltip_prompt')->comment('Всплывающая подсказка');
            $table->bigInteger('parent_id')->nullable()->unsigned()->comment('Указатель на родительский элемент меню');
            $table->tinyInteger('is_header')->default(0);
            $table->string('icon_class')->nullable()->comment('Класс иконки у элемента меню');
            $table->string('webhook_variable')->nullable()->comment('Название переменной, содержащей значение счетчика');
            $table->integer('sort')->default(100)->comment('Сортировка меню');
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
        Schema::dropIfExists('side_bar_menu_items');
    }
}
