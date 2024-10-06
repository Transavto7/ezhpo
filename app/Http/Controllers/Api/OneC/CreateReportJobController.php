<?php

namespace App\Http\Controllers\Api\OneC;

use App\Actions\Reports\OneC\Create\ReportAction;
use App\Actions\Reports\OneC\Create\ReportHandler;
use App\Actions\Reports\OneC\Create\ReportPayload;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use Auth;
use DateTimeImmutable;
use Exception;

class CreateReportJobController extends Controller
{
    public function __invoke(ReportRequest $request, ReportHandler  $handler)
    {
        try {
            $report = $handler->handle(
                new ReportAction(
                    ReportType::report(),
                    ReportStatus::created(),
                    Auth::user()->id,
                    new ReportPayload(
                        new DateTimeImmutable($request->input('date_to')),
                        new DateTimeImmutable($request->input('date_from')),
                        $request->input('company_id'),
                    )
                )
            );

            // todo: dispatch job

            return response()->json($report->uuid);
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }
}
