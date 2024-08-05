<?php

namespace App\Actions\Element\Metric\View;

use App\Actions\Element\Metric\Metric;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MetricView implements FromView
{
    /**
     * @var Metric[]
     */
    protected $data;

    /**
     * @param Metric[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('common.metric', ['data' => $this->data]);
    }
}
