<?php

namespace App\Console\Commands\SplitUsers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class FixTerminalInSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:fix-terminal-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FIX: terminals in terminal_settings';

    public function handle()
    {
        $terminals = DB::table('terminals')
            ->select([
                'id',
                'related_user_id',
            ])
            ->get();

        $terminals->each(function ($item) use (&$transferredCount) {
            DB::beginTransaction();

            try {
                $this->info('FIX терминала: [old_terminal_id = '.$item->related_user_id.']');

                $count = DB::table('terminal_settings')
                    ->where('old_terminal_id', '=', $item->related_user_id)
                    ->whereNull('terminal_id')
                    ->update([
                        'terminal_id' => $item->id,
                    ]);
                $this->comment("Обновлено записей [terminal_settings]: $count");
                $transferredCount += $count;

                $this->info("Настройка изменена\n");

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->error("Ошибка при обработке related_user_id {$item->related_user_id}: ".$exception->getMessage()."\n");
            }
        });

        $this->info('Исправлено: '.$transferredCount);
    }
}
