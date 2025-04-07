<?php

use App\FieldPrompt;
use App\Models\TripTicket;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnExternalNumberToTripTicketsTableAndFieldPrompts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->string('external_number')->nullable();
        });

        $externalNumber = [
            'attributes' => [
                'type' => TripTicket::SLUG,
                'field' => 'external_number'
            ],
            'values' => [
                'name' => 'Внешний номер ПЛ',
                'content' => '<p>Внешний номер ПЛ</p>',
                'sort' => 0,
            ]
        ];

        FieldPrompt::query()->updateOrCreate($externalNumber['attributes'], $externalNumber['values']);

        $fields = [
            'ticket_number',
            'external_number',
            'created_at',
            'company_name',
            'start_date',
            'period_pl',
            'validity_period',
            'medic_form_id',
            'driver_name',
            'tech_form_id',
            'car_number',
            'logistics_method',
            'transportation_type',
            'template_code',
            'photos',
            'type',
            'status',
            'user_name',
        ];

        foreach ($fields as $sort => $field) {
            FieldPrompt::query()
                ->where('type', TripTicket::SLUG)
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
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->dropColumn('external_number');
        });

        DB::table('field_prompts')
            ->where([
                'type' => TripTicket::SLUG,
                'field' => 'external_number',
            ])
            ->delete();
    }
}
