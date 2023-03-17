<?php

namespace App\Http\Controllers;

use App\Dtos\WorkReportFilterData;
use App\Http\Requests\WorkReportFilterRequest;
use App\Services\WorkReportService;
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

    public function getReport(WorkReportFilterRequest $request): array
    {
        $dto = new WorkReportFilterData($request->validated());
        return $this->workReportService->getAll($dto);
    }
}
