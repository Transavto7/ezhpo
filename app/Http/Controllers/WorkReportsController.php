<?php

namespace App\Http\Controllers;

use App\Dtos\WorkReportFilterData;
use App\Exports\WorkReportsExport;
use App\Http\Requests\WorkReportFilterRequest;
use App\Services\WorkReportService;
use Excel;
use Illuminate\View\View;

class WorkReportsController extends Controller
{
    /**
     * @var \App\Services\WorkReportService
     */
    protected WorkReportService $workReportService;

    public function __construct(WorkReportService $service)
    {
        $this->workReportService = $service;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('reports.work.index');
    }

    /**
     * @throws \ReflectionException
     * @throws \App\Values\Exceptions\ValueObjectException
     */
    public function getReport(WorkReportFilterRequest $request): array
    {
        $dto = new WorkReportFilterData($request->validated());
        return $this->workReportService->getAll($dto);
    }

    /**
     * @throws \App\Values\Exceptions\ValueObjectException
     * @throws \ReflectionException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(WorkReportFilterRequest $request)
    {
        $dto = new WorkReportFilterData($request->validated());
        return Excel::download(new WorkReportsExport($this->workReportService->getAll($dto)), 'work_reports.xlsx');
    }
}
