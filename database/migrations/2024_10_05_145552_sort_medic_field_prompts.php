<?php

use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class SortMedicFieldPrompts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fields = [
            'id',
            'company_name',
            'company_id',
            'date',
            'period_pl',
            'driver_fio',
            'date_prmo',
            'realy',
            'driver_group_risk',
            'type_view',
            'proba_alko',
            'alcometer_result',
            'driver_gender',
            'driver_year_birthday',
            'complaint',
            'condition_visible_sliz',
            'condition_koj_pokr',
            't_people',
            'tonometer',
            'pulse',
            'test_narko',
            'admitted',
            'protokol_path',
            'user_name',
            'user_eds',
            'created_at',
            'driver_id',
            'photos',
            'videos',
            'med_view',
            'pv_id',
            'flag_pak',
            'is_dop',
        ];

        foreach ($fields as $sort => $field) {
            FieldPrompt::query()
                ->where('type', FormTypeEnum::MEDIC)
                ->where('field', $field)
                ->update(['sort' => $sort]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
