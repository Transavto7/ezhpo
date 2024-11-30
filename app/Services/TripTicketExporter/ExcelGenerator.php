<?php

namespace App\Services\TripTicketExporter;

use App\Enums\TripTicketTemplateEnum;
use App\Models\TripTicket;
use App\Services\TripTicketExporter\Mappers\ItemMapperStrategy;
use App\Services\TripTicketExporter\SheetWriters\SheetWriterStrategy;
use App\ValueObjects\EntityId;
use DomainException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class ExcelGenerator
{
    /**
     * @var IReader
     */
    private $reader;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->reader = IOFactory::createReader('Xlsx');
    }

    /**
     * @param EntityId[] $ids
     * @return Xlsx
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function generate(array $ids): Xlsx
    {
        $templatePath = config('trip-ticket.print.template');
        $spreadsheet = $this->reader->load($templatePath);

        $this->validateTemplate($spreadsheet);

        $tripTickets = TripTicket::query()->whereIn('uuid', $ids)->get();

        foreach ($tripTickets as $index => $tripTicket) {
            $mapper = new ItemMapperStrategy($tripTicket);
            $writer = new SheetWriterStrategy(TripTicketTemplateEnum::fromString($tripTicket->template_code));

            $item = $mapper->map();
            $spreadsheet = $writer->createSheets($spreadsheet, $item, $index + 1);
        }

        // copy reverse sheets
        foreach ($this->reverseSheetNames() as $sheetName => $sheetPrefix) {
            $sheet = clone $spreadsheet->getSheetByName($sheetName);
            $sheet->setTitle($sheetPrefix);
            $spreadsheet->addSheet($sheet);
        }

        // delete template sheets
        foreach ($this->templateSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($sheet));
        }

        return new Xlsx($spreadsheet);
    }

    private function validateTemplate(Spreadsheet $spreadsheet)
    {
        $sheets = $spreadsheet->getAllSheets();
        $titles = array_map(function (Worksheet $sheet) {
            return $sheet->getTitle();
        }, $sheets);

        foreach ($this->templateSheetNames() as $sheetName) {
            if (!in_array($sheetName, $titles)) {
                throw new DomainException("Template has no required sheet with name '$sheetName'");
            }
        }
    }

    private function reverseSheetNames(): array
    {
        return [
            config('trip-ticket.print.4s.template.reverse.sheet') => config('trip-ticket.print.4s.template.reverse.prefix')
        ];
    }

    private function templateSheetNames(): array
    {
        return [
            config('trip-ticket.print.4s.template.front.sheet'),
            config('trip-ticket.print.4s.template.reverse.sheet'),
        ];
    }
}
