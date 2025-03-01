<?php

namespace App\Console\Commands\Forms;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateTableAnketas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:truncate-table-anketas
                            {--force : Подтверждение очистки}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка таблицы anketas после полного переноса осмотров в forms';

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
        $tableName = 'anketas';

        $tableExist = Schema::hasTable($tableName);

        if (!$tableExist) {
            $this->error('Таблица не существует!');

            return;
        }

        if (!$this->option('force')) {
            $this->error('Для подтверждения очистки таблицы - выполните команду с флагом --force! Проверьте, что вы сделали дамп перед этим!');

            return;
        }

        DB::table($tableName)->truncate();

        $this->info('Таблица успешно очищена!');
    }
}
