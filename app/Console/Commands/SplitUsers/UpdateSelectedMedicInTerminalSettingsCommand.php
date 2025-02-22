<?php

namespace App\Console\Commands\SplitUsers;

use App\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Src\Terminals\Eloquent\TerminalSettings;

final class UpdateSelectedMedicInTerminalSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:update-selected-medic-in-terminal-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление ID медика у настроек терминалов';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        DB::beginTransaction();

        $unsuccessfulIds = [];

        try {
            TerminalSettings::all()
                ->each(function (TerminalSettings $item) use (&$unsuccessfulIds) {
                    $settings = json_decode($item->getOriginal('settings'), true);

                    $userId = $settings['main']['selected_medic'];

                    if (! $userId) {
                        return;
                    }

                    $employee = Employee::withTrashed()->where('related_user_id', '=', $userId)->first();

                    if (! $employee) {
                        $this->warn('Не найден сотрудник для пользователя с id='.$userId);
                        $unsuccessfulIds[] = $item->id;

                        return;
                    }

                    $settings['main']['selected_medic'] = $employee->id;

                    DB::table('terminal_settings')
                        ->where('id', $item->id)
                        ->update([
                            'settings' => json_encode($settings),
                        ]);

                    $this->info('['.$item->id.']: замена пользователя id='.$userId.' на сотрудника id='.$employee->id);
                });

            if (count($unsuccessfulIds)) {
                $this->warn("\nЗаписи, у которых не удалось заменить ID медика:");
                $this->line(implode("\n", $unsuccessfulIds));
            }

            DB::commit();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            DB::rollBack();
            throw $exception;
        }
    }
}
