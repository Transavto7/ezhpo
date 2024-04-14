<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSortFieldToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_prompts', function (Blueprint $table) {
            $table->integer('sort')->default(0);
        });

        $sort = 0;
        $resultSort = 0;

        FieldPrompt::query()
            ->where('type', '=', 'medic')
            ->where('field', '!=', 'alcometer_result')
            ->get()
            ->each(function (FieldPrompt $fieldPrompt) use (&$sort, &$resultSort)  {
                $fieldPrompt->update(['sort' => $sort]);
                $sort++;

                if ($fieldPrompt->field === 'proba_alko') {
                    $resultSort = $sort;
                    $sort++;
                }
            });

        /** @var FieldPrompt  $fieldPrompt */
        $fieldPrompt = FieldPrompt::query()
            ->where('type', '=', 'medic')
            ->where('field', '=', 'alcometer_result')
            ->first();

        if ($fieldPrompt && $resultSort !== 0) {
            $fieldPrompt->update(['sort' => $resultSort]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('field_prompts', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
}
