<?php

use App\Car;
use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class UpdateCarTypesEnum extends Migration
{
    protected $enumElements = [
        'В и грузовые автомобили до 3.5 т.' => 'В - легковые и грузовые автомобили до 3,5 тн',
        'С (свыше 3.5 т.)' => 'С - Грузовые т/с от 3,5 тн',
        'D' => 'D - автобусы',
        'E' => 'Е - прицепы',
    ];

    protected $title = [
        'old' => 'Тип автомобиля',
        'new' => 'Тип т\с'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FieldPrompt::query()
            ->where('type', 'car')
            ->where('name', $this->title['old'])
            ->update(['name' => $this->title['new']]);

        foreach ($this->enumElements as $oldValue => $newValue) {
            Car::query()
                ->withTrashed()
                ->where('type_auto', $oldValue)
                ->update(['type_auto' => $newValue]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        FieldPrompt::query()
            ->where('type', 'car')
            ->where('name', $this->title['new'])
            ->update(['name' => $this->title['old']]);

        foreach ($this->enumElements as $oldValue => $newValue) {
            Car::query()
                ->withTrashed()
                ->where('type_auto', $newValue)
                ->update(['type_auto' => $oldValue]);
        }
    }
}
