<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class CreateFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_prompts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('field');
            $table->text('content')->nullable();
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Add new permissions
        Permission::updateOrCreate([
            'name'        => 'field_prompt_read',
            'guard_name' => 'Подсказки полей - Просмотр',
        ]);
        Permission::updateOrCreate([
            'name'        => 'field_prompt_edit',
            'guard_name' => 'Подсказки полей - Редактирование',
        ]);
        Permission::updateOrCreate([
            'name'        => 'field_prompt_create',
            'guard_name' => 'Подсказки полей - Добавление',
        ]);
        Permission::updateOrCreate([
            'name'        => 'field_prompt_delete',
            'guard_name' => 'Подсказки полей - Удаление',
        ]);
        Permission::updateOrCreate([
            'name'        => 'field_prompt_trash',
            'guard_name' => 'Подсказки полей - Карзина',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_prompts');
    }
}
