<?php

namespace App\Services;

use App\Dtos\WorkReportData;
use App\DTOs\WorkReportFilterData;
use App\WorkReport;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WorkReportService implements Contracts\ServiceInterface
{
    public function create(WorkReportData $data): array
    {
        return [
            'result' => WorkReport::create($data->toArray())
        ];
    }

    private function getAllWithFiltersQuery(WorkReportFilterData $data): Builder
    {
        return WorkReport::with(['user', 'point'])
            ->when($data->userId, function (Builder $builder) use ($data) {
                return $builder->where(['user_id' => $data->userId]);
            })
            ->when($data->pvId, function (Builder $builder) use ($data) {
                return $builder->where(['pv_id' => $data->pvId]);
            });
    }

    public function getAll(WorkReportFilterData $data): array
    {
        $reports = $this->getAllWithFiltersQuery($data)
            ->orderBy('datetime_begin', 'desc')
            ->get();

        $dateFrom = $data->dateFrom;
        $dateTo = $data->dateTo;

        if (is_null($dateFrom)) {
            $dateFrom = Carbon::now()->subMonth();
        }

        if (is_null($dateTo)) {
            $dateTo = Carbon::now();
        }

        $dates = CarbonPeriod::create($dateFrom, $dateTo)->toArray();

        return $this->prepareTableData($reports, $dates)->toArray();
    }

    private function prepareDataGrouped(Collection $reports): Collection
    {
        return $reports->groupBy('pv_id', true)
            ->map(function (Collection $item) {
                return $item->groupBy('user_id')
                    ->map(function (Collection $item) {
                        return $item->keyBy('date');
                    });
            });
    }

    /**
     * @param  \Illuminate\Support\Collection  $reports
     * @param  array  $dates
     * @param  string  $dateFormat
     * @return \Illuminate\Support\Collection
     */
    private function prepareTableData(Collection $reports, array $dates = [], string $dateFormat = 'Y-m-d'): Collection
    {
        $users = $reports->pluck('user', 'user_id');
        $points = $reports->pluck('point', 'pv_id');


        $grouped = $this->prepareDataGrouped($reports);
        $dates = array_map(function (Carbon $item) use ($dateFormat) {
            return $item->format($dateFormat);
        }, $dates);

        $firstColumn = [
            'datetime_begin' => 'Начало работы:',
            'datetime_end' => 'Окончание работы:',
            'hours' => 'Часов отработано:',
        ];

        $tableData = [];
        foreach ($grouped as $groupId => $datum) {
            $point = $points[$groupId] ?? null;
            $pointFirstRow = [
                ($point) ? $point->name : 'Название пункта выпуска',
                ...$dates,
                'Итого'
            ];

            /** @var Collection $datum */
            /** @var WorkReport[]|Collection $workReports */
            $reportsData = [];
            $userRows = [];

            foreach ($datum as $userId => $workReports) {
                $user = $users[$userId] ?? null;
                $mainUserRow = [
                    ($user) ? $user->name : 'ФИО сотрудника',
                    ...array_fill(0, count($dates) + 1, null)
                ];

                $userRows[] = $mainUserRow;

                foreach ($firstColumn as $k => $item) {
                    $rowCells = [];
                    $rowCells[] = $item;
                    foreach ($dates as $date) {
                        if (isset($workReports[$date])) {
                            $workReport = $workReports[$date];
                            $cellValue = $workReport->{$k};
                            $cellValue = ($cellValue instanceof Carbon) ?
                                $cellValue->format('H-i') : $cellValue;
                            $rowCells[] = $cellValue;
                        } else {
                            $rowCells[] = null;
                        }
                    }

                    if ($k != 'hours') {
                        $rowCells[] = null;
                    } else {
                        $rowCells[] = $workReports->sum('hours');
                    }

                    $userRows[] = $rowCells;
                }

                $reportsData[] = $userRows;
            }

            $tableData[] = [
                'pointRow' => $pointFirstRow,
                'reports' => $reportsData
            ];
        }

        return collect($tableData);
    }

}