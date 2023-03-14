<?php

namespace App\Services;

use App\Dtos\WorkReportData;
use App\DTOs\WorkReportFilterData;
use App\WorkReport;
use Illuminate\Database\Eloquent\Builder;

class WorkReportService implements Contracts\ServiceInterface
{
    public function create(WorkReportData $data): array
    {
        return [
            'result' => WorkReport::create($data->toArray())
        ];
    }

    private function getAllWithFiltersQuery(WorkReportFilterData $data) : Builder
    {
        return WorkReport::with(['user', 'point'])
            ->when(($data->dateFrom or $data->dateTo), function (Builder $builder) use ($data) {
                $dateFrom = $data->dateFrom;
                $dateTo = $data->dateTo;

                if (is_null($dateFrom)) {
                    $dateFrom = now()->subMonth();
                }

                if (is_null($dateTo)) {
                    $dateTo = now();
                }

                return $builder->whereBetween('date', [$dateFrom, $dateTo]);
            })->when($data->userId, function (Builder $builder) use ($data) {
                return $builder->where(['user_id' => $data->userId]);
            })->when($data->pvId, function (Builder $builder) use ($data) {
                return $builder->where(['pv_id' => $data->pvId]);
            });
    }

    public function getAll(WorkReportFilterData $data): array
    {
        return [
            'work_reports' => $this->getAllWithFiltersQuery($data)->paginate($data->perPage),
        ];
    }
}