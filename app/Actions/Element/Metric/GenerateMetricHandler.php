<?php

namespace App\Actions\Element\Metric;

use App\Actions\Element\Metric\View\MetricView;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GenerateMetricHandler
{
    /**
     * @var MetricRepository
     */
    private $repository;

    /**
     * @var GenerateMetricAction
     */
    protected $action;

    /**
     * @param GenerateMetricAction $action
     */
    public function __construct(GenerateMetricAction $action)
    {
        $this->repository = new MetricRepository();
        $this->action = $action;
    }

    /**
     * @return BinaryFileResponse
     */
    public function generate(): BinaryFileResponse
    {
        return Excel::download(new MetricView($this->repository->get()), 'metric.xlsx');
    }
}
