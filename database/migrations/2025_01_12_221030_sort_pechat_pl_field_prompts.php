<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SortPechatPlFieldPrompts extends Migration
{
    private $fields = [
        'id',
        'company_name',
        'company_id',
        'date',
        'driver_fio',
        'period_pl',
        'count_pl',
        'user_name',
        'user_eds',
        'pv_id',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->fields as $sort => $field) {
            FieldPrompt::query()
                ->where('type', 'pechat_pl')
                ->where('field', '=', $field)
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
        foreach ($this->fields as $field) {
            FieldPrompt::query()
                ->where('type', 'pechat_pl')
                ->where('field', '=', $field)
                ->update(['sort' => 0]);
        }
    }
}
