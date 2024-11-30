<?php

namespace App\Services\TripTicketExporter\SheetWriters;

use App\Services\TripTicketExporter\ViewModels\ExportedItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface SheetWriterInterface
{
    public function templateSheetName(): string;

    public function createSheet(Spreadsheet $spreadsheet, ExportedItem $item, int $number): Spreadsheet;
}
