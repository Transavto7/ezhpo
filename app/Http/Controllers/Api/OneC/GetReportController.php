<?php

namespace App\Http\Controllers\Api\OneC;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Exception;
use Storage;
use Symfony\Component\HttpFoundation\Response;

final class GetReportController extends Controller
{
    public function __invoke(string $id)
    {
        if (!user()->access('integration_1c_read')) {
            return response()->json([
                'message' => 'Forbidden'
            ])->setStatusCode(Response::HTTP_FORBIDDEN);
        }

        try {
            $report = Report::where('uuid', $id)->firstOrFail();

            switch (true) {
                case $report->status === ReportStatus::CREATED:
                    return response()->json([
                        'status' => ReportStatus::CREATED,
                        'message' => 'Отчет в очереди на выполнение',
                    ], Response::HTTP_ACCEPTED);
                case $report->status === ReportStatus::PROCESSING:
                    return response()->json([
                        'status' => ReportStatus::PROCESSING,
                        'message' => 'Отчет формируется',
                    ], Response::HTTP_ACCEPTED);
                case $report->status === ReportStatus::DELETED:
                case $report->status === ReportStatus::READY && ! Storage::disk('report')->exists($report->path):
                    return response()->json([
                        'status' => ReportStatus::DELETED,
                        'message' => 'Отчет просрочен, запросите повторное формирование',
                    ], Response::HTTP_NOT_FOUND);
                case $report->status === ReportStatus::READY && Storage::disk('report')->exists($report->path):
                    return response()->json([
                        'status' => ReportStatus::READY,
                        'message' => 'Отчет готов',
                        'content' =>  json_decode(file_get_contents(Storage::disk('report')->path($report->path)), true)
                    ], Response::HTTP_OK);
                case $report->status === ReportStatus::ERROR:
                    return response()->json([
                        'status' => ReportStatus::ERROR,
                        'message' => 'При формировании отчета произошла ошибка',
                    ], Response::HTTP_OK);
                default:
                    throw new Exception('Invalid report status');
            }
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
