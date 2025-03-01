<?php

namespace App\Console\Commands\Forms;

use App\Anketa;
use App\GenerateHashIdTrait;
use Illuminate\Console\Command;

class FixFormTypeViewsCommand extends Command
{
    use GenerateHashIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:fix-type-views
                            {--save : Обновить типы}
                            {--show : Показать количество}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание пользователей из водителей';

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
        $save = $this->option('save');
        $show = $this->option('show');

        $types = [
            'Предрейсовый/Предсменный' => [
                'Предрейсовый',
                'Предсменный',
                'предрейсовый/Предсменный',
                'Предрейсовый/предсменный',
                'предрейсовый/предсменный'
            ],
            'Послерейсовый/Послесменный' => [
                'Послерейсовый',
                'Послесменный',
                'послерейсовый/Послесменный',
                'Послерейсовый/послесменый',
                'Послерейсовый/послесменый',
                'послерейсовый/послесменный'
            ]
        ];

        foreach ($types as $validType => $invalidTypes) {
            $invalidCount = Anketa::whereIn('type_view', $invalidTypes)->count();

            if ($show) {
                $this->info("Невалидных типов для типа '$validType' - $invalidCount");
            }

            if ($save) {
                Anketa::whereIn('type_view', $invalidTypes)->update(
                    [
                        'type_view' => $validType
                    ]
                );
            }
        }
    }
}
