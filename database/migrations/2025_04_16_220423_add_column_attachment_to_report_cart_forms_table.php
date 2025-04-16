<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAttachmentToReportCartFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_cart_forms', function (Blueprint $table) {
            $table->jsonb('attachment')->nullable();
        });

        $attachment = [
            'attributes' => [
                'type' => 'report_cart',
                'field' => 'attachment'
            ],
            'values' => [
                'name' => 'Отчет',
                'content' => '<p>Файл отчета</p>',
                'sort' => FieldPrompt::where('type', '=', 'report_cart')->orderByDesc('sort')->first()->sort + 1,
            ]
        ];

        FieldPrompt::query()->updateOrCreate($attachment['attributes'], $attachment['values']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_cart_forms', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        DB::table('field_prompts')
            ->where('type',  '=', 'report_cart')
            ->where('field',  '=', 'attributes')
            ->delete();
    }
}
