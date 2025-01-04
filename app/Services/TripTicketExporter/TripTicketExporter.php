<?php

namespace App\Services\TripTicketExporter;

use App\ValueObjects\EntityId;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class TripTicketExporter
{
    /**
     * @var ExcelGenerator
     */
    private $generator;

    /**
     * @param ExcelGenerator $generator
     */
    public function __construct(ExcelGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param EntityId $id
     * @return Xlsx
     * @throws Exception
     */
    public function export(EntityId $id): Xlsx
    {
        return $this->generator->generate([$id]);
    }

    /**
     * @param EntityId[] $ids
     * @return Xlsx
     * @throws Exception
     */
    public function massExport(array $ids): Xlsx
    {
        return $this->generator->generate($ids);
    }

    /**
     * @param EntityId[] $ids
     * @throws Exception
     */
//    private function generateExcel(array $ids): StreamedResponse
//    {
//        $spreadsheet = IOFactory::load($this->getTemplatePath());
//
//        $tripTickets = TripTicket::whereIn('uuid', $ids)->get();
//
//        $number = 1;
//        foreach ($tripTickets as $tripTicket) {
//            switch (true) {
//                case $tripTicket->template_code === TripTicketTemplateEnum::S4:
//                    $templateSheet = $spreadsheet->getSheetByName(config('trip-ticket.print.template.front.sheet'));
//                    $prefix = config('trip-ticket.print.template.front.prefix');
//
//                    $dataWriter = new SheetWriter4S();
//                    $itemMapper = new ItemMapper4S();
//
//                    break;
//                default:
//                    throw new DomainException('Unsupported trip ticket template code' . $tripTicket->template_code);
//            }
//
//            $newSheet = clone $templateSheet;
//            $newSheet = $dataWriter->fillSheet($newSheet, $itemMapper->fromEloquent($tripTicket));
//
//            $title = $prefix;
//            if ($tripTicket->ticket_number) {
//                $title .= ' (' . $tripTicket->ticket_number . ')';
//            }
//            $title .= ' - '.$number;
//            $number++;
//
//            $newSheet->setTitle($title);
//
//            $spreadsheet->addSheet($newSheet);
//        }
//
//        $writer = new Xlsx($spreadsheet);
//
//        $response = new StreamedResponse(
//            function () use ($writer) {
//                $writer->save('php://output');
//            }
//        );
//
//        $templateSheet = $spreadsheet->getSheetByName(config('trip-ticket.print.4s.template.front.sheet'));
//        $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($templateSheet));
//
//        $now = Carbon::now()->format('Y_m_d_H:i:s');
//
//        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        $response->headers->set(
//            'Content-Disposition',
//            "attachment;filename=\"НПА_$now.xlsx\""
//        );
//        $response->headers->set('Cache-Control', 'max-age=0');
//
//        return $response;
//    }
}
