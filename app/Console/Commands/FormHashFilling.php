<?php

namespace App\Console\Commands;

use App\Enums\FormTypeEnum;
use App\Services\FormHash\FormHashGenerator;
use App\Services\FormHash\MedicHashData;
use App\Services\FormHash\TechHashData;
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
    protected $signature = 'forms:fill-day-hash';

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
        $this->info(Carbon::now() . ' Начало работы');
        Log::info(Carbon::now() . ' Начало работы команды по заполнению day_hash');

        $ctr = 0;
        DB::table('anketas')
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
            ->chunk(1000, function ($forms) use (&$ctr) {
                try {
                    DB::beginTransaction();

                    foreach ($forms as $form) {
                        $hash = FormHashGenerator::generate(
                            $form->type_view === FormTypeEnum::MEDIC
                                ? new MedicHashData(
                                    $form->driver_id,
                                    new \DateTimeImmutable($form->date),
                                    $form->type_view
                                )
                                : new TechHashData(
                                    $form->driver_id,
                                    $form->car_id,
                                    new \DateTimeImmutable($form->date),
                                    $form->type_view
                                )
                        );

                        DB::table('anketas')
                            ->where('id', $form->id)
                            ->update(['day_hash' => $hash]);
                    }

                    $ctr += count($forms);
                    $this->info(Carbon::now() . " Обработано $ctr записей");
                    Log::info(Carbon::now() . " Обработано $ctr записей");
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    $this->error($e->getMessage());
                }
            });
        $this->info(Carbon::now() . ' Завершение работы. Обработано всего: ' . $ctr);
        Log::info(Carbon::now() . ' Завершение работы. Обработано всего: ' . $ctr);
    }
}
