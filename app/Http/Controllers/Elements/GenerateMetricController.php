<?php

namespace App\Http\Controllers\Elements;

use App\Actions\Element\Metric\GenerateMetricAction;
use App\Actions\Element\Metric\GenerateMetricHandler;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class GenerateMetricController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $handler = new GenerateMetricHandler(
                new GenerateMetricAction(
                    Carbon::parse($request->input('start')),
                    Carbon::parse($request->input('end')),
                )
            );

            return $handler->generate();
        } catch (Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
