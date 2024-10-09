<?php

namespace App\Http\Controllers\Api\OneC;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Exception;
use Storage;
use Symfony\Component\HttpFoundation\Response;

class GetReportController extends Controller
{
    public function __invoke(string $id)
    {
        try {
            $report = Report::where('uuid', $id)->firstOrFail();

            switch (true) {
                case $report->status === ReportStatus::CREATED:
                    return response()->json([
                        'status' => 'Отчет в очереди на выполнение'
                    ], Response::HTTP_ACCEPTED);
                case $report->status === ReportStatus::PROCESSING:
                    return response()->json([
                        'status' => 'Отчет формируется'
                    ], Response::HTTP_ACCEPTED);
                case $report->status === ReportStatus::DELETED:
                case $report->status === ReportStatus::READY && ! Storage::disk('report')->exists($report->path):
                    return response()->json([
                        'status' => 'Отчет просрочен, запросите повторное формирование'
                    ], Response::HTTP_NOT_FOUND);
                case $report->status === ReportStatus::READY && Storage::disk('report')->exists($report->path):
                    return response()->json([
                        'status' => 'Отчет готов',
                        'content' =>  json_decode(file_get_contents(Storage::disk('report')->path($report->path)), true)
                    ]);
                default:
                    throw new Exception('Undefined report');
            }
        } catch (Exception $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }
}
