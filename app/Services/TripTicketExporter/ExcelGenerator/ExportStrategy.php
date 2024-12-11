<?php

namespace App\Services\TripTicketExporter\ExcelGenerator;

use App\Services\TripTicketExporter\ViewModels\ExportData;
use Maatwebsite\Excel\Sheet;

interface ExportStrategy
{
    public function getTemplate(): string;

    public function fillSheet(Sheet $sheet, ExportData $data): Sheet;
}
