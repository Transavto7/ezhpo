<?php

namespace App\Console\Commands;

use App\Enums\FormTypeEnum;
use App\Models\Forms\Form;
use App\Models\Forms\TechForm;
use DateTime;
use Illuminate\Console\Command;

class FixTechFormsPlRoadNumbersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:fix-tech-pl-road-number
                            {date=2024-01-12 : Дата, с которой необходимо валидировать осмотры}
                            {--update : Обновить невалидные номера}
                            {--show : Показать невалидные номера}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Валидация и обновление автоматически сгенерированных номеров ПЛ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startDate = $this->argument('date');
        $startDateTime = DateTime::createFromFormat("Y-m-d", $startDate);
        if ($startDateTime === false || array_sum($startDateTime::getLastErrors())) {
            $this->error('Невалидный формат даты');
            return;
        }

        $updateAfterValidate = $this->option('update');
        $showInvalid = $this->option('show');

        $counters = [
            'wrong' => 0,
            'fixed' => 0
        ];

        $forms = Form::query()
            ->select([
                'forms.*',
                'tech_forms.*',
                'companies.id as company_original_id'
            ])
            ->join('tech_forms', 'tech_forms.form_uuid', '=', 'forms.uuid')
            ->join('companies', 'companies.hash_id', '=', 'forms.company_id')
            ->where('forms.type_anketa', FormTypeEnum::TECH)
            ->whereDate('forms.created_at', '>=', $startDate)
            ->whereNotNull('tech_forms.car_id')
            ->whereNotNull('tech_forms.number_list_road')
            ->get();

        $this->info(sprintf('Всего осмотров с заполненными номерами ПЛ: %s', $forms->count()));

        $forms->each(function (Form $form) use (&$counters, $updateAfterValidate, $showInvalid) {
            $numberListRoad = $form->getAttribute('number_list_road');
            $carHashId = $form->getAttribute('car_id');
            $companyId = $form->getAttribute('company_original_id');

            $numberListRoadSegments = explode('-', $numberListRoad);
            if ($numberListRoadSegments[0] !== "$companyId") {
                return;
            }

            $counters['wrong']++;
            $numberListRoadSegments[0] = $carHashId;
            $fixedNumberListRoad = implode('-', $numberListRoadSegments);

            if ($showInvalid) {
                $this->warn(sprintf(
                    '%s: %s => %s',
                    $form->getAttribute('id'),
                    $numberListRoad,
                    $fixedNumberListRoad
                ));
            }

            if (!$updateAfterValidate) {
                return;
            }

            TechForm::query()
                ->where('forms_uuid', $form->uuid)
                ->update([
                    'number_list_road' => $fixedNumberListRoad
                ]);

            $counters['fixed']++;
        });

        $this->info(sprintf('Некорректных ПЛ: %s', $counters['wrong']));
        $this->info(sprintf('Исправленных ПЛ: %s', $counters['fixed']));
    }
}
