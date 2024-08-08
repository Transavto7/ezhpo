<?php

namespace App\Http\Controllers;

use App\Actions\Reports\GraphPv\GetGraphPvData\GetGraphPvDataAction;
use App\Actions\Reports\GraphPv\GetGraphPvData\GetGraphPvDataHandler;
use App\Actions\Reports\Journal\GetJournalData\GetJournalDataAction;
use App\Actions\Reports\Journal\GetJournalData\GetJournalDataHandler;
use App\Anketa;
use App\Company;
use App\Events\UserActions\ClientReportRequest;
use App\Exports\ReportJournalExport;
use App\Point;
use App\Town;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public $reports = [
        'journal' => 'Отчет по услугам компании',
        'graph_pv' => 'График работы пунктов выпуска'
    ];

    public function showJournal(Request $request)
    {
        $company = null;

        if ($request->has('company_id')) {
            $company = Company::query()
                ->select([
                    'id',
                    'hash_id',
                    'name'
                ])
                ->where('hash_id', $request->input('company_id'))
                ->first();
        }

        return view('reports.journal.index', [
            'company' => $company
        ]);
    }

    public function getDynamic(Request $request, $journal)
    {
        $months = [];
        $periodStart = Carbon::now()->subMonths(11);
        $period = CarbonPeriod::create($periodStart, '1 month', Carbon::now());
        foreach ($period as $month) {
            $months[] = $month->format('F');
        }
        $months = array_reverse($months);

        if ($request->town_id || $request->pv_id || $request->order_by) {
            event(new ClientReportRequest($request->user(), "dynamic_$journal"));

            $date_from = Carbon::now()->subMonths(11)->firstOfMonth()->startOfDay();
            $date_to = Carbon::now()->lastOfMonth()->endOfDay();
            $result = [];
            $total = [];

            $forms = Anketa::query();

            if ($journal !== 'all') {
                $forms = $forms->where('type_anketa', $journal);
            } else {
                $forms = $forms->whereIn('type_anketa', ['tech', 'medic']);
            }

            $forms = $forms->where('in_cart', 0);

            if ($request->order_by === 'created') {
                $forms = $forms->whereBetween('created_at', [
                    $date_from,
                    $date_to,
                ]);
            } else {
                $forms = $forms->where(function ($q) use ($date_from, $date_to) {
                    $q->where(function ($q) use ($date_from, $date_to) {
                        $q->whereNotNull('date')
                            ->whereBetween('date', [
                                $date_from,
                                $date_to,
                            ]);
                    })->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('date')
                            ->whereBetween('period_pl', [
                                $date_from->format('Y-m'),
                                $date_to->format('Y-m'),
                            ]);
                    });
                });
            }

            if ($request->pv_id) {
                $points = Point::whereIn('id', $request->pv_id)->get();
                $forms->where(function ($query) use ($points) {
                    foreach ($points as $point) {
                        $query = $query
                            ->orWhere('anketas.pv_id', $point->name)
                            ->orWhere('anketas.point_id', $point->id);
                    }

                    return $query;
                });
            } else if ($request->town_id) {
                $points = Point::whereIn('pv_id', $request->town_id)->get();
                $forms->where(function ($query) use ($points) {
                    foreach ($points as $point) {
                        $query = $query
                            ->orWhere('anketas.pv_id', $point->name)
                            ->orWhere('anketas.point_id', $point->id);
                    }

                    return $query;
                });
            }

            if ($request->company_id) {
                $forms = $forms->whereIn('company_id', $request->company_id);
            }

            $forms = $forms->get();

            foreach ($forms->groupBy('company_id') as $companies => $formsByCompany) {
                $company = $formsByCompany->where('company_name', '!=', null)->first();
                if ($company) {
                    $result[$companies]['name'] = $company->company_name;
                } else {
                    $result[$companies]['name'] = 'Неизвестная компания';
                }

                for ($monthIndex = 0; $monthIndex < 12; $monthIndex++) {
                    $date_from = Carbon::now()->subMonths($monthIndex)->firstOfMonth()->startOfDay();
                    $date_to = Carbon::now()->subMonths($monthIndex)->lastOfMonth()->endOfDay();
                    $date = Carbon::now()->subMonths($monthIndex);

                    if ($request->order_by === 'created') {
                        $count = $formsByCompany
                            ->whereBetween('created_at', [
                                $date_from,
                                $date_to,
                            ])
                            ->count();
                    } else {
                        $formsByCompanyWithDateCount = $formsByCompany
                            ->where('date', '!=', null)
                            ->whereBetween('date', [
                                $date_from,
                                $date_to,
                            ])
                            ->count();

                        $formsByCompanyWithPeriodCount = $formsByCompany
                            ->where('date', null)
                            ->whereBetween('period_pl', [
                                $date_from->format('Y-m'),
                                $date_to->format('Y-m'),
                            ])
                            ->count();

                        $count = $formsByCompanyWithDateCount + $formsByCompanyWithPeriodCount;
                    }

                    $result[$companies][$date->format('F')] = $count;
                    $total[$date->format('F')] = ($total[$date->format('F')] ?? 0) + $count;
                }
            }
        }

        $towns = Town::get(['hash_id', 'id', 'name']);
        $points = Point::get(['hash_id', 'id', 'name', 'pv_id']);

        if ($request->company_id) {
            $selectedCompanies = Company::query()
                ->select([
                    'hash_id',
                    'name'
                ])
                ->whereIn('hash_id', $request->company_id)
                ->get();

            $companies = Company::query()
                ->select([
                    'hash_id',
                    'name'
                ])
                ->whereNotIn('hash_id', $request->company_id)
                ->limit(100)
                ->get()
                ->concat($selectedCompanies);
        } else {
            $companies = Company::select('hash_id', 'name')->limit(100)->get();
        }

        return view('reports.dynamic.medic.index', [
            'months' => $months,
            'companies' => $result ?? null,
            'total' => $total ?? null,
            'towns' => $towns,
            'company_id' => $companies,
            'points' => $points,
            'journal' => $journal
        ]);
    }

    public function exportJournalData(Request $request, GetJournalDataHandler $handler)
    {
        if ($request->has('month')) {
            $date_from = Carbon::parse($request->month)->startOfMonth();
            $date_to = Carbon::parse($request->month)->endOfMonth();
        } else {
            $date_from = Carbon::parse($request->date_from)->startOfDay();
            $date_to = Carbon::parse($request->date_to)->endOfDay();
        }

        $companyID = $request->input('company_id');

        if (!$companyID || !$date_to || !$date_from) {
            return response()->json(null, 404);
        }

        event(new ClientReportRequest($request->user('api'), 'service_report_request'));

        return Excel::download(new ReportJournalExport($handler->handle(new GetJournalDataAction($companyID, $date_from, $date_to))), 'export.xlsx');
    }

    public function getJournalData(Request $request, GetJournalDataHandler $handler): JsonResponse
    {
        if ($request->has('month')) {
            $date_from = Carbon::parse($request->month)->startOfMonth();
            $date_to = Carbon::parse($request->month)->endOfMonth();
        } else {
            $date_from = Carbon::parse($request->date_from)->startOfDay();
            $date_to = Carbon::parse($request->date_to)->endOfDay();
        }

        $companyID = $request->input('company_id');

        if (!$companyID || !$date_to || !$date_from) {
            return response()->json(null, 404);
        }

        event(new ClientReportRequest($request->user('api'), 'service_report_request'));

        return response()->json($handler->handle(new GetJournalDataAction($companyID, $date_from, $date_to)));
    }

    public function getGraphPvData(Request $request, GetGraphPvDataHandler $handler): JsonResponse
    {
        event(new ClientReportRequest($request->user('api'), 'graph_pv'));

        $formType = $request->input('type_anketa');
        $pvId = $request->input('pv_id', [0]);

        $dateFrom = Carbon::now()->startOfYear();
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        }

        $dateTo = Carbon::now();
        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->input('date_to'))->endOfDay();
        }

        return response()->json($handler->handle(new GetGraphPvDataAction($pvId, $formType, $dateFrom, $dateTo)));
    }

    public function GetReport(Request $request)
    {
        $data = $request->all();

        $type_report = $request->type_report;

        $date_from = $data['date_from'] ?? Carbon::now()->startOfYear();
        $date_to = $data['date_to'] ?? Carbon::now();

        $company_fields = config('elements.Driver.fields.company_id');
        $company_fields['getFieldKey'] = 'hash_id';

        $pv_fields = config('elements.Company.fields.pv_id');
        $pv_fields['getFieldKey'] = 'name';
        $pv_fields['multiple'] = 1;

        return view('pages.reports.all', [
            'title' => $this->reports[$type_report] ?? '',
            'reports' => null,
            'reports2' => null,
            'company_fields' => $company_fields,
            'pv_fields' => $pv_fields,
            'type_report' => $type_report,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'date_field' => 'date',
            'company_id' => $data['company_id'] ?? 0,
            'pv_id' => $data['pv_id'] ?? 0,
            'data' => []
        ]);
    }
}
