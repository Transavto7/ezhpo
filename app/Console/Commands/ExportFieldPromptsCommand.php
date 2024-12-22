<?php

namespace App\Console\Commands;

use App\Services\SyncFieldPrompts\SyncFieldPromptsService;
use DateTimeImmutable;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ExportFieldPromptsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'field-prompts:export
                            {--date= : Записи, созданные до этой даты (включительно), будут экспортированы, формат даты YYYY-MM-DD}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Экспорт полей field_prompts';

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
        $dataArg = $this->option('date');

        if (!$dataArg) {
            $this->error('Необходимо указать дату');
            return;
        }

        $data = DateTimeImmutable::createFromFormat('Y-m-d', $dataArg);

        if (!$data) {
            $this->error('Некорректный формат даты');
            return;
        }

        try {
            $items = $this->service->exportBeforeDate($data);

            $now = new DateTimeImmutable();
            $fileName = 'field_prompts_'.$now->format('Y-m-d_h-i-s').'.json';
            $filePath = 'field_prompts/'.$fileName;

            Storage::disk('public')->put($filePath, json_encode($items, JSON_PRETTY_PRINT));

            $this->info('Экспорт успешно выполнен:');
            $this->warn(Storage::disk('public')->path($filePath));

        } catch (Exception $exception) {
            $this->error("Ошибка: " . $exception->getMessage());
        }
    }
}
