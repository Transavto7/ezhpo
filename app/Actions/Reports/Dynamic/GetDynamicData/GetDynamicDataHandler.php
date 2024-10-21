<?php

namespace App\Actions\Reports\Dynamic\GetDynamicData;

use App\Company;
use App\Enums\FormTypeEnum;
use App\Models\Forms\Form;
use App\Point;
use App\Town;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class GetDynamicDataHandler
{
    protected function getMonth(): array
    {
        $months = [];
        $periodStart = Carbon::now()->subMonths(11);
        $period = CarbonPeriod::create($periodStart, '1 month', Carbon::now());
        foreach ($period as $month) {
            $months[] = $month->format('F');
        }

        return array_reverse($months);
    }

    public function handle(GetDynamicDataAction $action): array
    {
        $companyId = $action->getCompanyId();
        $pvId = $action->getPointId();
        $townId = $action->getTownId();
        $journal = $action->getJournal();
        $orderBy = $action->getOrderBy();

        $months = $this->getMonth();

        $towns = Town::get(['hash_id', 'id', 'name']);
        $points = Point::get(['hash_id', 'id', 'name', 'pv_id']);

        if ($companyId) {
            $selectedCompanies = Company::query()
                ->select([
                    'hash_id',
                    'name'
                ])
                ->whereIn('hash_id', $companyId)
                ->get();

            $companies = Company::query()
                ->select([
                    'hash_id',
                    'name'
                ])
                ->whereNotIn('hash_id', $companyId)
                ->limit(100)
                ->get()
                ->concat($selectedCompanies);
        } else {
            $companies = Company::query()
                ->select([
                    'hash_id',
                    'name'
                ])
                ->limit(100)
                ->get();
        }

        $result = [];
        $total = [];

        if ($townId || $pvId || $orderBy) {
            if ($journal === 'all') {
                $formTypes = [
                    FormTypeEnum::MEDIC,
                    FormTypeEnum::TECH
                ];
            } else {
                $formTypes = [
                    $journal
                ];
            }

            $dateFrom = Carbon::now()->subMonths(11)->firstOfMonth()->startOfDay();
            $dateTo = Carbon::now()->lastOfMonth()->endOfDay();

            $forms = collect([]);

            foreach ($formTypes as $formType) {
                $formDetailsTable = Form::$relatedTables[$formType];
                $query = Form::query()
                    ->select([
                        'forms.*',
                        "$formDetailsTable.period_pl as period_pl",
                        'companies.name as company_name'
                    ])
                    ->join($formDetailsTable, 'forms.uuid', '=', "$formDetailsTable.forms_uuid")
                    ->leftJoin('companies', 'forms.company_id', '=', 'companies.hash_id');

                if ($orderBy === 'created') {
                    $query->whereBetween('forms.created_at', [
                        $dateFrom,
                        $dateTo,
                    ]);
                } else {
                    $query->where(function ($q) use ($formDetailsTable, $dateFrom, $dateTo) {
                        $q->where(function ($q) use ($dateFrom, $dateTo) {
                            $q->whereNotNull('forms.date')
                                ->whereBetween('forms.date', [
                                    $dateFrom,
                                    $dateTo,
                                ]);
                        })->orWhere(function ($q) use ($formDetailsTable, $dateFrom, $dateTo) {
                            $q->whereNull('forms.date')
                                ->whereBetween("$formDetailsTable.period_pl", [
                                    $dateFrom->format('Y-m'),
                                    $dateTo->format('Y-m'),
                                ]);
                        });
                    });
                }

                if ($pvId) {
                    $query->where(function ($query) use ($pvId) {
                        foreach ($pvId as $point) {
                            $query->orWhere('forms.point_id', $point->id);
                        }

                        return $query;
                    });
                }

                if ($townId) {
                    $query->leftJoin('points', 'forms.point_id', '=', 'points.id');
                    $query->where(function ($query) use ($townId) {
                        foreach ($townId as $town) {
                            $query->orWhere('points.pv_id', $town);
                        }

                        return $query;
                    });
                }

                if ($companyId) {
                    $query = $query->whereIn('forms.company_id', $companyId);
                }

                $forms = $forms->merge($query->get());
            }

            foreach ($forms->groupBy('company_id') as $companyId => $formsByCompany) {
                $company = $formsByCompany->where('company_id', '=', $companyId)->first();
                $result[$companyId]['name'] = $company->company_name;

                for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
                    $dateFrom = Carbon::now()->subMonths($monthIndex)->firstOfMonth()->startOfDay();
                    $dateTo = Carbon::now()->subMonths($monthIndex)->lastOfMonth()->endOfDay();
                    $date = Carbon::now()->subMonths($monthIndex);

                    if ($orderBy === 'created') {
                        $count = $formsByCompany
                            ->whereBetween('created_at', [
                                $dateFrom,
                                $dateTo,
                            ])
                            ->count();
                    } else {
                        $formsByCompanyWithDateCount = $formsByCompany
                            ->where('date', '!=', null)
                            ->whereBetween('date', [
                                $dateFrom,
                                $dateTo,
                            ])
                            ->count();

                        $formsByCompanyWithPeriodCount = $formsByCompany
                            ->where('date', null)
                            ->whereBetween('period_pl', [
                                $dateFrom->format('Y-m'),
                                $dateTo->format('Y-m'),
                            ])
                            ->count();

                        $count = $formsByCompanyWithDateCount + $formsByCompanyWithPeriodCount;
                    }

                    $result[$companyId][$date->format('F')] = $count;
                    $total[$date->format('F')] = ($total[$date->format('F')] ?? 0) + $count;
                }
            }
        }

        return [
            'months' => $months,
            'companies' => $result ?? null,
            'total' => $total ?? null,
            'towns' => $towns,
            'company_id' => $companies,
            'points' => $points,
            'journal' => $journal
        ];
    }
}
