<?php

namespace App\Services\OneC\Reports;

use App\Actions\Reports\Journal\GetJournalData\GetJournalDataAction;
use App\Contractcs\GetServicesReportForCompanyByPeriodInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetServicesReportForCompanyByPeriod implements GetServicesReportForCompanyByPeriodInterface
{
    /**
     * @var Carbon
     */
    private $dateFrom;
    /**
     * @var Carbon
     */
    private $dateTo;

    private $companyHashId;

    public function handle(GetJournalDataAction $action): array
    {
        $this->dateFrom = $action->getDateFrom();
        $this->dateTo = $action->getDateTo();
        $this->companyHashId = $action->getCompanyHashId();

        return array_merge(
            $this->getMedicServices(),
            $this->getTechServices(),
            $this->getBdd(),
            $this->getReportCart(),
            $this->getPrintPl()
        );
    }

    private function getMedicServices(): array
    {
        $items = DB::table('forms')
            ->select([
                DB::raw('count(forms.id) as counter'),
                'medic_forms.type_view',
                'towns.name as town_name',
                'forms.driver_id'
            ])
            ->join('medic_forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->leftJoin('points', 'forms.point_id', '=', 'points.id')
            ->leftJoin('towns', 'points.pv_id', '=', 'towns.id')
            ->whereNull('forms.deleted_at')
            ->whereBetween('forms.date', [
                $this->dateFrom,
                $this->dateTo,
            ])
            ->where(function ($query) {
                $query->where('medic_forms.is_dop', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('medic_forms.is_dop', 1)
                            ->whereNotNull('medic_forms.result_dop');
                    });
            })
            ->groupBy(['towns.name', 'forms.driver_id', 'medic_forms.type_view'])
            ->where('forms.company_id', $this->companyHashId)
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return [
                'водитель' => $item->driver_id,
                'вид_услуги' => 'МО',
                'тип_услуги' => $item->type_view,
                'город' => $item->town_name,
                'кол_услуг' => $item->counter
            ];
        }, $items);
    }

    private function getTechServices(): array
    {
        $items = DB::table('forms')
            ->select([
                DB::raw('count(forms.id) as counter'),
                'tech_forms.type_view',
                'towns.name as town_name',
                'cars.type_auto as car_type_auto',
                'tech_forms.car_id'
            ])
            ->join('tech_forms', 'forms.uuid', '=', 'tech_forms.forms_uuid')
            ->leftJoin('cars', 'tech_forms.car_id', '=', 'cars.hash_id')
            ->leftJoin('points', 'forms.point_id', '=', 'points.id')
            ->leftJoin('towns', 'points.pv_id', '=', 'towns.id')
            ->whereNull('forms.deleted_at')
            ->whereBetween('forms.date', [
                $this->dateFrom,
                $this->dateTo,
            ])
            ->where(function ($query) {
                $query->where('tech_forms.is_dop', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('tech_forms.is_dop', 1)
                            ->whereNotNull('tech_forms.result_dop');
                    });
            })
            ->groupBy(['towns.name', 'tech_forms.car_id', 'cars.type_auto', 'tech_forms.type_view'])
            ->where('forms.company_id', $this->companyHashId)
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return [
                'авто' => $item->car_id,
                'вид_услуги' => 'ТО',
                'тип_услуги' => $item->type_view,
                'город' => $item->town_name,
                'категория_авто' => $item->car_type_auto,
                'кол_услуг' => $item->counter
            ];
        }, $items);
    }

    private function getBdd(): array
    {
        $items = DB::table('forms')
            ->select([
                DB::raw('count(forms.id) as counter'),
                'towns.name as town_name',
                'forms.driver_id'
            ])
            ->join('bdd_forms', 'forms.uuid', '=', 'bdd_forms.forms_uuid')
            ->leftJoin('points', 'forms.point_id', '=', 'points.id')
            ->leftJoin('towns', 'points.pv_id', '=', 'towns.id')
            ->whereNull('forms.deleted_at')
            ->whereBetween('forms.date', [
                $this->dateFrom,
                $this->dateTo,
            ])
            ->groupBy(['towns.name', 'forms.driver_id'])
            ->where('forms.company_id', $this->companyHashId)
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return [
                'водитель' => $item->driver_id,
                'вид_услуги' => 'БДД',
                'город' => $item->town_name ?? '',
                'кол_услуг' => $item->counter
            ];
        }, $items);
    }

    private function getReportCart(): array
    {
        $items = DB::table('forms')
            ->select([
                DB::raw('count(forms.id) as counter'),
                'towns.name as town_name',
                'forms.driver_id'
            ])
            ->join('report_cart_forms', 'forms.uuid', '=', 'report_cart_forms.forms_uuid')
            ->leftJoin('points', 'forms.point_id', '=', 'points.id')
            ->leftJoin('towns', 'points.pv_id', '=', 'towns.id')
            ->whereNull('forms.deleted_at')
            ->whereBetween('forms.date', [
                $this->dateFrom,
                $this->dateTo,
            ])
            ->groupBy(['towns.name', 'forms.driver_id'])
            ->where('forms.company_id', $this->companyHashId)
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return [
                'водитель' => $item->driver_id,
                'вид_услуги' => 'Отчеты с карты',
                'город' => $item->town_name,
                'кол_услуг' => $item->counter
            ];
        }, $items);
    }

    private function getPrintPl(): array
    {
        $items = DB::table('forms')
            ->select([
                DB::raw('sum(print_pl_forms.count_pl) as counter'),
                'towns.name as town_name',
                'forms.driver_id'
            ])
            ->join('print_pl_forms', 'forms.uuid', '=', 'print_pl_forms.forms_uuid')
            ->leftJoin('points', 'forms.point_id', '=', 'points.id')
            ->leftJoin('towns', 'points.pv_id', '=', 'towns.id')
            ->whereNull('forms.deleted_at')
            ->whereBetween('forms.date', [
                $this->dateFrom,
                $this->dateTo,
            ])
            ->groupBy(['towns.name', 'forms.driver_id'])
            ->where('forms.company_id', $this->companyHashId)
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return [
                'водитель' => $item->driver_id,
                'вид_услуги' => 'Печать ПЛ',
                'город' => $item->town_name,
                'кол_услуг' => intval($item->counter)
            ];
        }, $items);
    }
}
