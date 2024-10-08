<?php

namespace App\Console\Commands;

use App\Enums\FormTypeEnum;
use App\Services\FormHash\FormHashGenerator;
use App\Services\FormHash\MedicHashData;
use App\Services\FormHash\TechHashData;
use DateTimeImmutable;
use DB;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Log;

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
    protected $description = 'Заполнение поля day_hash таблицы anketas для записей, где оно пустое';

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

        $chunkSize = config('forms.fill-day-hash-chunk-size', 50000);

        $data = DB::table('anketas')
            ->select([
                'id',
                'driver_id',
                'car_id',
                'date',
                'type_view',
            ])
            ->whereNotNull('driver_id')
            ->whereNotNull('car_id')
            ->whereNotNull('date')
            ->whereNotNull('type_view')
            ->whereNull('day_hash')
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('type_anketa', '=', FormTypeEnum::MEDIC)
                    ->orWhere('type_anketa', '=', FormTypeEnum::TECH);
            })
            ->orderByDesc('id')
            ->limit($chunkSize)
            ->get();

        $ctr = 0;
        foreach ($data->chunk(1000) as $chunk) {
            try {
                DB::beginTransaction();

                $updates = [];
                foreach ($chunk as $form) {
                    $hash = FormHashGenerator::generate(
                        $form->type_view === FormTypeEnum::MEDIC
                            ? new MedicHashData(
                            $form->driver_id,
                            new DateTimeImmutable($form->date),
                            $form->type_view
                        )
                            : new TechHashData(
                            $form->driver_id,
                            $form->car_id,
                            new DateTimeImmutable($form->date),
                            $form->type_view
                        )
                    );

                    $updates[] = [
                        'id' => $form->id,
                        'day_hash' => $hash,
                    ];
                }

                foreach ($updates as $update) {
                    DB::table('anketas')
                        ->where('id', $update['id'])
                        ->update(['day_hash' => $update['day_hash']]);
                }

                $ctr += count($chunk);
                $this->info(Carbon::now() . " Обработано $ctr записей");
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }
        }

        $this->info(Carbon::now() . ' Завершение работы. Обработано всего: ' . $ctr);
        Log::info('forms:fill-day-hash - Завершение работы. Обработано записей: ' . $ctr);
    }
}
