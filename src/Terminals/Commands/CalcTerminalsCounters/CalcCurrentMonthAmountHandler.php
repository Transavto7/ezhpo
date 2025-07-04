<?php

namespace Src\Terminals\Commands\CalcTerminalsCounters;

use App\Models\Forms\MedicForm;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Src\Terminals\Eloquent\TerminalsCounters;
use Src\Terminals\Enums\TerminalsCounterTypeEnum;

final class CalcCurrentMonthAmountHandler
{
    public function handle(): int
    {
        $type = TerminalsCounterTypeEnum::currentMonthAmount();

        TerminalsCounters::query()
            ->where('type', $type)
            ->delete();

        $currentMonthAmount = MedicForm::query()
            ->select([
                DB::raw('count(forms.id) as count'),
                'medic_forms.terminal_id',
            ])
            ->leftJoin('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->where('forms.created_at', '>', Carbon::now()->startOfMonth())
            ->whereNotNull('medic_forms.terminal_id')
            ->groupBy(['medic_forms.terminal_id'])
            ->get()
            ->each(function ($item) use ($type) {
                TerminalsCounters::create([
                    'terminal_id' => $item->terminal_id,
                    'count' => $item->count,
                    'type' => $type
                ]);
            });

        return $currentMonthAmount->count();
    }
}
