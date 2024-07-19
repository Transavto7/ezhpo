<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnlyOfflineInspectionsFieldToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('only_offline_medic_inspections')
                ->nullable()
                ->default(false);
        });

        FieldPrompt::query()->withTrashed()->updateOrCreate(
            [
                'type' => 'driver',
                'field' => 'only_offline_medic_inspections'
            ],
            [
                'deleted_at' => null,
                'name' => 'Блокировка прохождения МО',
                'content' => '<p><span class="ql-color-#000000">Водителю ограничен дистанционный выпуск, обратитесь к медицинскому сотруднику на Пункте Выпуска</span></p>'
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'only_offline_medic_inspections'
            ]);
        });

        FieldPrompt::query()
            ->where('type', '=', 'driver')
            ->where('field', '=', 'only_offline_medic_inspections')
            ->delete();
    }
}
