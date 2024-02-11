<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Enums\FormTypeEnum;
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

        $forms = Anketa::query()
            ->select([
                'anketas.*',
                'companies.id as company_original_id'
            ])
            ->join('companies', 'companies.hash_id', '=', 'anketas.company_id')
            ->where('anketas.type_anketa', FormTypeEnum::TECH)
            ->whereDate('anketas.created_at', '>=', $startDate)
            ->whereNotNull('anketas.car_id')
            ->whereNotNull('anketas.number_list_road')
            ->get();

        $this->info(sprintf('Всего осмотров с заполненными номерами ПЛ: %s', $forms->count()));

        $forms->each(function (Anketa $form) use (&$counters, $updateAfterValidate, $showInvalid) {
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

            $form->setAttribute('number_list_road', $fixedNumberListRoad);
            $form->save();

            $counters['fixed']++;
        });

        $this->info(sprintf('Некорректных ПЛ: %s', $counters['wrong']));
        $this->info(sprintf('Исправленных ПЛ: %s', $counters['fixed']));
    }
}
