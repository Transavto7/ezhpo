<?php

namespace App\Actions\Reports\GraphPv\GetGraphPvData;

use App\Models\Forms\Form;
use Illuminate\Support\Carbon;

class GetGraphPvDataHandler
{
    public function handle(GetGraphPvDataAction $action): array
    {
        $pvId = $action->getPvId();
        $formType = $action->getFormType();
        $dateFrom = $action->getDateFrom();
        $dateTo = $action->getDateTo();

        $reports = Form::query()
            //TODO: нужен left join
            ->whereIn('point_id', $pvId)
            ->where('type_anketa', $formType)
            ->where(function ($q) use ($dateFrom, $dateTo) {
                $q->where(function ($q) use ($dateFrom, $dateTo) {
                    $q->whereNotNull('date')
                        ->whereBetween('date', [
                            $dateFrom,
                            $dateTo
                        ]);
                })->orWhere(function ($q) use ($dateFrom, $dateTo) {
                    $q->whereNull('date')
                        ->whereBetween('period_pl', [
                            Carbon::parse($dateFrom)->format('Y-m'),
                            Carbon::parse($dateTo)->format('Y-m'),
                        ]);
                });
            });

        $reports2 = Form::query()
            ->whereIn('point_id', $pvId)
            ->where('type_anketa', $formType)
            ->whereBetween("created_at", [
                $dateFrom,
                $dateTo,
            ]);

        return [
            'reports' => $reports->get(),
            'reports2' => $reports2->get()
        ];
    }
}
