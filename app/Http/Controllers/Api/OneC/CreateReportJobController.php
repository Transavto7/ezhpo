<?php

namespace App\Http\Controllers\Api\OneC;

use App\Actions\Reports\OneC\Create\ReportAction;
use App\Actions\Reports\OneC\Create\ReportHandler;
use App\Actions\Reports\OneC\Create\ReportPayload;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReportJobRequest;
use App\Jobs\CreateReport;
use Auth;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Response;

final class CreateReportJobController extends Controller
{
    public function __invoke(CreateReportJobRequest $request, ReportHandler $handler)
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

            CreateReport::dispatch($report);

            return response()->json([
                'id' => $report->uuid
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
