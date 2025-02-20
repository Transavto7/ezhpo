<?php

namespace App\Services\TripTicketExporter\SheetWriters;

use App\Enums\TripTicket\TripTicketTemplateEnum;
use App\Services\QRCode\QRCodeImageGenerator;
use App\Services\TripTicketExporter\ViewModels\ExportedItem;
use DomainException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class SheetWriterStrategy
{
    /**
     * @var SheetWriterInterface
     */
    private $sheetWriter;

    public function __construct(TripTicketTemplateEnum $templateCode)
    {
        $qrCodeGenerator = new QRCodeImageGenerator();

        switch (true) {
            case $templateCode->value() === TripTicketTemplateEnum::S4:
                $this->sheetWriter = new SheetWriter4S($qrCodeGenerator);
                break;
            case $templateCode->value() === TripTicketTemplateEnum::_3:
                $this->sheetWriter = new SheetWriter3($qrCodeGenerator);
                break;
            default:
                throw new DomainException('Unsupported trip ticket template code' . $templateCode->value());
        }
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param ExportedItem $item
     * @param int $number
     * @return Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function createSheets(Spreadsheet $spreadsheet, ExportedItem $item, int $number): Spreadsheet
    {
        return $this->sheetWriter->createSheet($spreadsheet, $item, $number);
    }
}
