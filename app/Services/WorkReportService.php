<?php

namespace App\Services;

use App\Dtos\WorkReportData;
use App\DTOs\WorkReportFilterData;
use App\Point;
use App\User;
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

        if (is_null($data->dateFrom) or is_null($data->dateTo)) {
            $dates = CarbonPeriod::create(now()->subMonth(), now())->toArray();
        } else {
            $dates = CarbonPeriod::create(
                $data->dateFrom->getValue(),
                $data->dateTo->getValue()
            )->toArray();
        }



        return $this->prepareTableData($reports, $dates)
            ->toArray();
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

        $rowDatum = [];
        foreach ($grouped as $groupId => $datum) {
            /** @var Point $point */
            $point = $points[$groupId];
            $rowDatum[$groupId]['pointData'] = [
                $point->name,
                ...$dates,
                'Итого'
            ];

            foreach ($datum as $userId => $reportsData) {
                /** @var User $user*/
                $user = $users[$userId];
                $userRowsHeaders = [
                    'name' => '',
                    'begin' => 'Начало работы',
                    'end' => 'Окончание работы',
                    'hours' => 'Часов отработано'
                ];

                foreach ($userRowsHeaders as $slug => $name)  {
                    /** @var WorkReport $report */
                    if ($slug === 'name') {
                        $rowDatum[$groupId]['reports'][] = [
                            $user->name,
                            ...array_fill(0, count($dates) + 1, null)
                        ];
                    } elseif ($slug === 'begin') {
                        $beginRows = [];
                        foreach ($dates as $date) {
                            if (isset($reportsData[$date])) {
                                $report = $reportsData[$date];
                                $beginRows[] = $report->datetime_begin->format('H-i');
                            } else {
                                $beginRows[] = null;
                            }
                        }
                        $beginRows[] = null;
                        $rowDatum[$groupId]['reports'][] = [
                            $name,
                            ...$beginRows
                        ];
                    } elseif ($slug === 'end') {
                        $endRows = [];
                        foreach ($dates as $date) {
                            if (isset($reportsData[$date])) {
                                $report = $reportsData[$date];
                                $endRows[] = $report->datetime_end->format('H-i');
                            } else {
                                $endRows[] = null;
                            }
                        }
                        $endRows[] = null;
                        $rowDatum[$groupId]['reports'][] = [
                            $name,
                            ...$endRows
                        ];
                    } elseif ($slug === 'hours') {
                        $hoursRows = [];
                        $hours = 0;
                        foreach ($dates as $date) {
                            if (isset($reportsData[$date])) {
                                $report = $reportsData[$date];
                                $hoursRows[] = $report->hours. 'ч.';
                                $hours += $report->hours;
                            } else {
                                $hoursRows[] = null;
                            }
                        }
                        $hoursRows[] = $hours. 'ч.';
                        $rowDatum[$groupId]['reports'][] = [
                            $name,
                            ...$hoursRows
                        ];
                    }

                }
            }
        }
        return collect($rowDatum);
    }

}