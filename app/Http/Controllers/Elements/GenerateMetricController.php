<?php

namespace App\Http\Controllers\Elements;

use App\Actions\Element\Metric\GenerateMetricAction;
use App\Actions\Element\Metric\GenerateMetricHandler;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GenerateMetricController extends Controller
{
    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function __invoke(Request $request): BinaryFileResponse
    {
        $handler = new GenerateMetricHandler(
            new GenerateMetricAction(
                Carbon::parse($request->input('start')),
                Carbon::parse($request->input('end')),
            )
        );

        return $handler->generate();
    }
}
