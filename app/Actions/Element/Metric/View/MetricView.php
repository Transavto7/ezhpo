<?php

namespace App\Actions\Element\Metric\View;

use App\Actions\Element\Metric\GenerateMetricAction;
use App\Actions\Element\Metric\Metric;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MetricView implements FromView
{
    /**
     * @var GenerateMetricAction
     */
    protected $action;
    /**
     * @var Metric[]
     */
    protected $data;

    /**
     * @param Metric[] $data
     */
    public function __construct(GenerateMetricAction $action, array $data)
    {
        $this->action = $action;
        $this->data = $data;
    }

    public function view(): View
    {
        return view('common.metric', ['action' => $this->action, 'data' => $this->data]);
    }
}
