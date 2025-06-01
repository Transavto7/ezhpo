<?php

namespace App\Console\Commands\ExportForms;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class ImportMedicFormsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-medic-forms-from-test {--file= : Путь к файлу с JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Импорт осмотров';

    public function handle()
    {
        $filePath = $this->option('file');

        if (! $filePath) {
            $this->error('Не указан путь к файлу. Используйте опцию --file');
            return 1;
        }

        if (! file_exists($filePath)) {
            $this->error("Файл не найден: $filePath");

            return 1;
        }

        $json = file_get_contents($filePath);

        $data = json_decode($json, true);

        $this->info('Начало импорта осмотров');

        $insertedFormUuids = [];

        DB::beginTransaction();

        try {
            foreach ($data as $item) {
                $form = $item['form'];
                $medicForm = $item['medic_form'];
                $logs = $item['logs'];

                unset($form['id']);

                DB::table('forms')->insert($form);
                DB::table('medic_forms')->insert($medicForm);

                foreach ($logs as $log) {
                    unset($log['id']);
                    DB::table('form_events')->insert($log);
                }

                $insertedFormUuids[] = $form['uuid'];
            }

             DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->error($exception->getMessage());
        }

        $logData = [
            'insertedFormUuids' => $insertedFormUuids,
        ];

        $json = json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'form_import_log_'.Carbon::now()->format('Y-m-d_h-i-s').'.json';

        file_put_contents(storage_path("app/public/$fileName"), $json);

        $this->info("\nИмпорт завершен");
    }
}
