<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class FixElementsRelationsDuplicatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elements:fix-relations-duplicate
                            {--delete : Удалить дубли}
                            {--show : Показать дубли}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Поиск дублей в связях M:N и их удаление';

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
        $delete = $this->option('delete');
        $showDuplicates = $this->option('show');

        $pivots = [
            'driver_contact_pivot' => [
                'driver_id',
                'contract_id'
            ],
            'car_contact_pivot' => [
                'car_id',
                'contract_id'
            ],
            'contract_service' => [
                'contract_id',
                'service_id'
            ]
        ];

        try {
            DB::beginTransaction();

            foreach ($pivots as $pivot => $keys) {
                $select = [DB::raw('count(id) as counter')];
                foreach ($keys as $key) {
                    $select[] = $key;
                }

                $duplicates = DB::table($pivot)
                    ->select($select)
                    ->groupBy($keys)
                    ->having('counter', '>', 1)
                    ->get();

                if ($showDuplicates) {
                    $this->info(sprintf('%s - %s duplicates', $pivot, count($duplicates)));
                }

                foreach ($duplicates as $duplicate) {
                    if ($showDuplicates) {
                        $this->info(json_encode($duplicate));
                    }

                    $queryToDelete = DB::table($pivot);

                    foreach ($keys as $key) {
                        $queryToDelete->where($key, $duplicate->$key);
                    }

                    if ($delete) {
                        $queryToDelete->limit($duplicate->counter - 1)->delete();
                    }
                }
            }

            if ($delete) {
                $this->info('All duplicates deleted');
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->error($exception->getMessage());
        }
    }
}
