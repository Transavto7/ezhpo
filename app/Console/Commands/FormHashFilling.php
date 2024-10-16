<?php

namespace App\Console\Commands;

use App\Services\FormHash\FormHashGenerator;
use App\Services\FormHash\MedicHashData;
use App\Services\FormHash\TechHashData;
use DateTimeImmutable;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormHashFilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:fill-day-hash
        {--force : Запуск команды игнорируя конфиг}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение поля day_hash таблицы forms для записей, где оно пустое';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (config('forms.fill-day-hash', false) === false && ! $this->option('force')) {
            return;
        }

        $this->info(Carbon::now() . ' Начало работы');
        Log::info('forms:fill-day-hash - Запуск команды');

        $limit = config('forms.fill-day-hash-chunk-size', 50000);

        $chunkSize = 1000;

        $counter = $this->fillMedicForms($limit, $chunkSize);
        Log::info('forms:fill-day-hash - Завершение заполнения МО. Обработано записей: ' . $counter);

        $counter = $this->fillTechForms($limit, $chunkSize);
        Log::info('forms:fill-day-hash - Завершение заполнения ТО. Обработано записей: ' . $counter);

        $this->info(Carbon::now() . ' Завершение работы.');
    }

    private function fillMedicForms(int $limit, int $chunkSize): int
    {
        $data = DB::table('medic_forms')
            ->select([
                'forms.uuid',
                'forms.driver_id',
                'dorms.date',
                'medic_forms.type_view',
            ])
            ->join('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->whereNotNull('forms.driver_id')
            ->whereNotNull('forms.date')
            ->whereNotNull('medic_forms.type_view')
            ->whereNull('medic_forms.day_hash')
            ->whereNull('forms.deleted_at')
            ->orderByDesc('forms.id')
            ->limit($limit)
            ->get();

        $counter = 0;
        foreach ($data->chunk($chunkSize) as $chunk) {
            try {
                DB::beginTransaction();

                $updates = [];

                foreach ($chunk as $form) {
                    $updates[$form->uuid] = FormHashGenerator::generate(new MedicHashData(
                        $form->driver_id,
                        new DateTimeImmutable($form->date),
                        $form->type_view
                    ));
                }

                foreach ($updates as $uuid => $hash) {
                    DB::table('medic_forms')
                        ->where('forms_uuid', $uuid)
                        ->update(['day_hash' => $hash]);
                }

                $counter += count($chunk);
                $this->info(Carbon::now() . " Обработано $counter записей МО");
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }
        }

        return $counter;
    }

    private function fillTechForms(int $limit, int $chunkSize): int
    {
        $data = DB::table('tech_forms')
            ->select([
                'forms.id',
                'forms.driver_id',
                'tech_forms.car_id',
                'dorms.date',
                'tech_forms.type_view',
            ])
            ->join('forms', 'forms.uuid', '=', 'tech_forms.forms_uuid')
            ->whereNotNull('forms.driver_id')
            ->whereNotNull('tech_forms.car_id')
            ->whereNotNull('forms.date')
            ->whereNotNull('tech_forms.type_view')
            ->whereNull('tech_forms.day_hash')
            ->whereNull('forms.deleted_at')
            ->orderByDesc('forms.id')
            ->limit($limit)
            ->get();

        $counter = 0;
        foreach ($data->chunk($chunkSize) as $chunk) {
            try {
                DB::beginTransaction();

                $updates = [];

                foreach ($chunk as $form) {
                    $updates[$form->uuid] = FormHashGenerator::generate(new TechHashData(
                        $form->driver_id,
                        $form->car_id,
                        new DateTimeImmutable($form->date),
                        $form->type_view
                    ));
                }

                foreach ($updates as $uuid => $hash) {
                    DB::table('tech_forms')
                        ->where('forms_uuid', $uuid)
                        ->update(['day_hash' => $hash]);
                }

                $counter += count($chunk);
                $this->info(Carbon::now() . " Обработано $counter записей ТО");
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }
        }

        return $counter;
    }
}
