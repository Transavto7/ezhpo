<?php

namespace App\Console\Commands\SplitUsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class UpdateOperatorsInMedicFormsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:update-operators-in-medic-forms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление операторов медицинских осмотров';

    public function handle()
    {
        $this->info('Обновление операторов медицинских осмотров');

        $items = DB::table('medic_forms')
            ->leftJoin('employees', 'employees.related_user_id', '=', 'medic_forms.old_operator_id')
            ->whereNotNull('medic_forms.old_operator_id')
            ->select([
                'employees.id as operator_id',
                'medic_forms.old_operator_id',
            ])
            ->distinct()
            ->get();

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        $result = [];

        $items->each(function ($item) use (&$bar, &$result) {
            try {
                if (! $item->operator_id) {
                    $result[] = [
                        'operator_id' => $item->operator_id,
                        'old_operator_id' => $item->old_operator_id,
                        'count' => 0,
                        'success' => false,
                    ];

                    return;
                }

                $count = DB::table('medic_forms')
                    ->where('old_operator_id', '=', $item->old_operator_id)
                    ->update([
                        'operator_id' => $item->operator_id,
                    ]);

                $result[] = [
                    'operator_id' => $item->operator_id,
                    'old_operator_id' => $item->old_operator_id,
                    'count' => $count,
                    'success' => true,
                ];
            } catch (\Exception $exception) {
                $result[] = [
                    'operator_id' => $item->operator_id,
                    'old_operator_id' => $item->old_operator_id,
                    'count' => 0,
                    'success' => false,
                    'error' => $exception->getMessage(),
                ];
            } finally {
                $bar->advance();
            }
        });

        $bar->finish();

        $this->line('');
        $this->line('');
        $this->info('Результаты обновления:');

        collect($result)->each(function ($entry) {
            if (!$entry['success']) {
                $msg = "Оператор: {$entry['operator_id']} (old_operator_id: {$entry['old_operator_id']}), обновлено: {$entry['count']}";
                if (isset($entry['error'])) {
                    $msg .= ", ошибка: {$entry['error']}";
                }
                $this->warn($msg);
            } else {
                $this->line("Оператор: {$entry['operator_id']} (old_operator_id: {$entry['old_operator_id']}), обновлено: {$entry['count']}");
            }
        });
    }
}
