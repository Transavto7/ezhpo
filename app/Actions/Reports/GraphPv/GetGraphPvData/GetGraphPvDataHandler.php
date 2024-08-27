<?php

namespace App\Actions\Reports\GraphPv\GetGraphPvData;

use App\Anketa;
use Illuminate\Support\Carbon;

class GetGraphPvDataHandler
{
    public function handle(GetGraphPvDataAction $action): array
    {
        $pvId = $action->getPvId();
        $formType = $action->getFormType();
        $dateFrom = $action->getDateFrom();
        $dateTo = $action->getDateTo();

        $reports = Anketa::whereIn('pv_id', $pvId)
            ->where('type_anketa', $formType)
            ->where('in_cart', 0)
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

        $reports2 = Anketa::whereIn('pv_id', $pvId)
            ->where('type_anketa', $formType)
            ->where('in_cart', 0)
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
