<?php

namespace App\Console\Commands\SplitUsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class UpdateTerminalsInMedicFormsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:update-terminals-in-medic-forms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление терминалов медицинских осмотров';

    public function handle()
    {
        $this->info('Обновление терминалов медицинских осмотров');

        $items = DB::table('medic_forms')
            ->leftJoin('terminals', 'terminals.related_user_id', '=', 'medic_forms.old_terminal_id')
            ->whereNotNull('medic_forms.old_terminal_id')
            ->select([
                'terminals.id as terminal_id',
                'medic_forms.old_terminal_id',
            ])
            ->distinct()
            ->get();

        $bar = $this->output->createProgressBar($items->count());
        $bar->start();

        $result = [];

        $items->each(function ($item) use (&$bar, &$result) {
            try {
                if (! $item->terminal_id) {
                    $result[] = [
                        'terminal_id' => $item->terminal_id,
                        'old_terminal_id' => $item->old_terminal_id,
                        'count' => 0,
                        'success' => false,
                    ];

                    return;
                }

                $count = DB::table('medic_forms')
                    ->where('old_terminal_id', '=', $item->old_terminal_id)
                    ->update([
                        'terminal_id' => $item->terminal_id,
                    ]);

                $result[] = [
                    'terminal_id' => $item->terminal_id,
                    'old_terminal_id' => $item->old_terminal_id,
                    'count' => $count,
                    'success' => true,
                ];
            } catch (\Exception $exception) {
                $result[] = [
                    'terminal_id' => $item->terminal_id,
                    'old_terminal_id' => $item->old_terminal_id,
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
                $msg = "Терминал: {$entry['terminal_id']} (old_terminal_id: {$entry['old_terminal_id']}), обновлено: {$entry['count']}";
                if (isset($entry['error'])) {
                    $msg .= ", ошибка: {$entry['error']}";
                }
                $this->warn($msg);
            } else {
                $this->line("Терминал: {$entry['terminal_id']} (old_terminal_id: {$entry['old_terminal_id']}), обновлено: {$entry['count']}");
            }
        });
    }
}


