<?php

use App\Enums\FormLogActionTypesEnum;
use App\Models\FormEvent;
use App\Models\Forms\MedicForm;
use Illuminate\Database\Migrations\Migration;

class FillEmptyModelTypeInFormEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FormEvent::query()
            ->where('event_type', FormLogActionTypesEnum::SET_FEEDBACK)
            ->whereNull('model_type')
            ->update([
                'model_type' => MedicForm::class
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        FormEvent::query()
            ->where('event_type', FormLogActionTypesEnum::SET_FEEDBACK)
            ->update([
                'model_type' => null
            ]);
    }
}
