<?php

namespace Src\MedicalReference\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Src\MedicalReference\Dto\BaseDto;
use Str;

class MedicalReferenceExporter implements MedicalReferenceExporterInterface
{

    public function export(BaseDto $baseDto)
    {
        $certificateXml = view('medical-reference::certificate', [
            'base' => $baseDto,
        ])->render();

        $fileName = '086u-'. Str::uuid()->toString().'-'.Carbon::now()->format(config('external-system.date_format'));

        Storage::disk('medical-reference')->put($fileName, $certificateXml);

        if(Storage::disk('medical-reference')->exists($fileName)) {
            // Получаем содержимое файла
            $fileContent = Storage::disk('medical-reference')->get($fileName);

            // Определение заголовков для скачивания
            $headers = [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '.xml"',
            ];

            // Возвращаем файл как HTTP-ответ
            $response = response($fileContent, 200, $headers);
        } else {
            // Обработка ошибки если файл не найден
            $response = response("File not found", 404);
        }

        Storage::disk('medical-reference')->delete($fileName);

        return $response;
    }
}
