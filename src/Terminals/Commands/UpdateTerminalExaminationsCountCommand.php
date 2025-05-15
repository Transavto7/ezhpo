<?php

namespace Src\Terminals\Commands;

use App\Models\Forms\MedicForm;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateTerminalExaminationsCountCommand extends Command
{
    /**
     * Название команды.
     *
     * @var string
     */
    protected $signature = 'terminals:update-examinations-count';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Обновляет количество осмотров для каждого терминала СДПО';

    /**
     * Выполнение команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Начинаем обновление статистики терминалов СДПО...');

        $terminals = DB::table('terminals')->get();

        $this->output->progressStart(count($terminals));

        $lastMonthAmount = MedicForm::query()
            ->select([
                DB::raw('count(forms.id) as count'),
                'medic_forms.terminal_id',
            ])
            ->leftJoin('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->where('forms.created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
            ->where('forms.created_at', '<=', Carbon::now()->startOfMonth())
            ->whereNotNull('medic_forms.terminal_id')
            ->groupBy(['medic_forms.terminal_id'])
            ->get()
            ->pluck('count', 'terminal_id')
            ->toArray();

        $monthAmount = MedicForm::query()
            ->select([
                DB::raw('count(forms.id) as count'),
                'medic_forms.terminal_id',
            ])
            ->leftJoin('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->where('forms.created_at', '>', Carbon::now()->startOfMonth())
            ->whereNotNull('medic_forms.terminal_id')
            ->groupBy(['medic_forms.terminal_id'])
            ->get()
            ->pluck('count', 'terminal_id')
            ->toArray();

        foreach ($terminals as $terminal) {

            // Обновляем данные в таблице терминалов
            DB::table('terminals')
                ->where('id', $terminal->id)
                ->update([
                    'month_amount' => $monthAmount[$terminal->id] ?? 0,
                    'last_month_amount' => $lastMonthAmount[$terminal->id] ?? 0,
                ]);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Статистика терминалов СДПО успешно обновлена!');

        return 0;
    }
}
