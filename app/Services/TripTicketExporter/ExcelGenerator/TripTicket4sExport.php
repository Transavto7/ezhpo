<?php

namespace App\Services\TripTicketExporter\ExcelGenerator;

use App\Services\TripTicketExporter\ViewModels\ExportData;
use Maatwebsite\Excel\Sheet;

class TripTicket4sExport implements ExportStrategy
{
    public function getTemplate(): string
    {
        return public_path('templates/trip-tickets/4s.xlsx');
    }

    public function fillSheet(Sheet $sheet, ExportData $data): Sheet
    {
        $sheet->setCellValue('A1', 'test');
        return $sheet;
    }
}
