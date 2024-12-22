<?php

namespace App\Console\Commands;

use App\Services\SyncFieldPrompts\SyncFieldPromptsService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportFieldPromptsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'field-prompts:import
                            {--file= : Путь к файлу с импортируемыми записями}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импорт полей field_prompts';

    /**
     * @var SyncFieldPromptsService
     */
    private $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SyncFieldPromptsService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $fileArg = $this->option('file');

        if (!$fileArg) {
            $this->error('Необходимо указать путь к файлу');
            return;
        }

        DB::beginTransaction();

        try {
            $fileContent = file_get_contents($fileArg);
            $items = json_decode($fileContent, true);

            $this->service->import($items);

            $this->info('Импорт успешно выполнен');

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            $this->error("Ошибка: " . $exception->getMessage());
        }
    }
}
