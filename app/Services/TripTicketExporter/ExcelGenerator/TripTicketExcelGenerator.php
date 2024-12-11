<?php

namespace App\Services\TripTicketExporter\ExcelGenerator;

use App\Enums\TripTicketTemplateEnum;
use App\Services\TripTicketExporter\ViewModels\ExportData;
use DomainException;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Files\LocalTemporaryFile;

class TripTicketExcelGenerator implements WithEvents
{
    /**
     * @var ExportStrategy
     */
    private $strategy;

    /**
     * @var ExportData
     */
    private $exportData;

    /**
     * @param ExportData $exportData
     */
    public function __construct(ExportData $exportData)
    {
        $this->exportData = $exportData;

        switch (true) {
            case $exportData->getTemplateCode()->value() === TripTicketTemplateEnum::S4:
                $this->strategy = new TripTicket4sExport();
                break;
            default:
                throw new DomainException('Unsupported trip ticket template code: ' . $exportData->getTemplateCode()->value());
        }
    }

    /**
     * Export data
     * @return array
     */
    public function registerEvents(): array
    {
        $templatePath = $this->strategy->getTemplate();

        if (!file_exists($templatePath)) {
            throw new DomainException('Template trip ticket does not exist: ' . $templatePath);
        }

        return [
            BeforeExport::class => function(BeforeExport $event) use ($templatePath) {
                $event->writer->reopen(new LocalTemporaryFile($templatePath),Excel::XLSX);

                $sheet = $event->writer->getSheetByIndex(0);
                $sheet = $this->strategy->fillSheet($sheet, $this->exportData);

                return $event->getWriter()->getSheetByIndex(0);
            }
        ];
    }
}
