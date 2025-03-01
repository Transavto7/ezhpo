<?php

namespace App\Http\Controllers;

use App\Actions\Reports\Dynamic\GetDynamicData\GetDynamicDataAction;
use App\Actions\Reports\Dynamic\GetDynamicData\GetDynamicDataHandler;
use App\Actions\Reports\GraphPv\GetGraphPvData\GetGraphPvDataAction;
use App\Actions\Reports\GraphPv\GetGraphPvData\GetGraphPvDataHandler;
use App\Actions\Reports\Journal\GetJournalData\GetJournalDataAction;
use App\Actions\Reports\Journal\GetJournalData\GetJournalDataHandler;
use App\Company;
use App\Events\UserActions\ClientReportRequest;
use App\Exports\ReportJournalExport;
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

    public function index(Request $request)
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

    public function getDynamic(Request $request, string $journal, GetDynamicDataHandler $handler)
    {
        if ($request->town_id || $request->pv_id || $request->order_by) {
            event(new ClientReportRequest($request->user(), "dynamic_$journal"));
        }

        $data = $handler->handle(new GetDynamicDataAction(
            $journal,
            $request->pv_id,
            $request->town_id,
            $request->order_by,
            $request->company_id
        ));

        return view('reports.dynamic.index', $data);
    }

    public function getJournalData(Request $request, GetJournalDataHandler $handler): JsonResponse
    {
        $dateFrom = Carbon::parse($request->month)->startOfMonth();
        $dateTo = Carbon::parse($request->month)->endOfMonth();
        $companyID = $request->input('company_id');

        if (!$companyID || !$dateTo || !$dateFrom) {
            return response()->json(null, 404);
        }

        event(new ClientReportRequest($request->user('api'), 'service_report_request'));

        return response()->json($handler->handle(new GetJournalDataAction($companyID, $dateFrom, $dateTo)));
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

    public function getReport(Request $request)
    {
        $data = $request->all();

        $type_report = $request->type_report;

        $date_from = $data['date_from'] ?? Carbon::now()->startOfYear();
        $date_to = $data['date_to'] ?? Carbon::now();

        $company_fields = config('elements.Driver.fields.company_id');
        $company_fields['getFieldKey'] = 'hash_id';

        $pv_fields = config('elements.Company.fields.pv_id');
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
}
