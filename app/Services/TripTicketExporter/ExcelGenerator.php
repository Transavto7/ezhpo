<?php

namespace App\Services\TripTicketExporter;

use App\Enums\TripTicket\TripTicketActionType;
use App\Enums\TripTicket\TripTicketStatus;
use App\Enums\TripTicket\TripTicketTemplateEnum;
use App\Enums\TripTicket\TripTicketType;
use App\Events\TripTickets\ChangeTripTicketStatus;
use App\Events\TripTickets\LogTripTicket;
use App\Models\TripTicket;
use App\Services\TripTicketExporter\Mappers\ItemMapperStrategy;
use App\Services\TripTicketExporter\SheetWriters\SheetWriterStrategy;
use App\Services\TripTicketExporter\ViewModels\ExportedItem3;
use App\ValueObjects\EntityId;
use Auth;
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
     * @var Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var array
     */
    private $usedTemplates = [];

    /**
     * @var int
     */
    private $globalIndex = 1;

    /**
     * @var TripTicket[]
     */
    private $tripTickets = [];

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->reader = IOFactory::createReader('Xlsx');
    }

    /**
     * @param array $ids
     * @return string
     * @throws \Exception
     */
    public function generateFileName(array $ids): string
    {
        if (count($ids) === 0) {
            throw new \Exception('Пустой перечень ПЛ для генерации имени файла');
        }

        if (count($ids) === 1) {
            return $this->generateSingleFileName(array_values($ids)[0]);
        }

        return $this->generateMultipleFileName($ids);
    }

    /**
     * @param EntityId $id
     * @return string
     * @throws \Exception
     */
    private function generateSingleFileName(EntityId $id): string
    {
        $tripTicket = TripTicket::query()->where('uuid', $id)->first();
        if (!$tripTicket) {
            throw new \Exception('ПЛ с таким UUID не найден');
        }

        $tripTicketType = $tripTicket->template_code;

        $tripTicketNumber = $tripTicket->ticket_number;

        $currentDate = date('YmdHis');

        return "PL_{$tripTicketType}_{$tripTicketNumber}_$currentDate.xlsx";
    }

    /**
     * @param EntityId[] $ids
     * @return string
     * @throws \Exception
     */
    private function generateMultipleFileName(array $ids): string
    {
        $tripTickets = TripTicket::query()
            ->select(['ticket_number'])
            ->whereIn('uuid', $ids)
            ->get();

        if ($tripTickets->count() === 0) {
            throw new \Exception('Не найдены ПЛ для генерации имени файла');
        }

        $trimmedTripTicketNumbers = substr(implode(",", $tripTickets->pluck('ticket_number')->toArray()), 0, 235);

        $currentDate = date('YmdHis');

        return "PL_{$trimmedTripTicketNumbers}_$currentDate.xlsx";
    }

    /**
     * @param EntityId[] $ids
     * @return Xlsx
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function generate(array $ids): Xlsx
    {
        $templatePath = config('trip-ticket.print.template');
        $this->spreadsheet = $this->reader->load($templatePath);

        $this->validateTemplate($this->spreadsheet);

        $this->create($ids, TripTicketTemplateEnum::s4());
        $this->create($ids, TripTicketTemplateEnum::_4p());
        $this->create($ids, TripTicketTemplateEnum::pg1());
        $this->create($ids, TripTicketTemplateEnum::_6c());
        $this->create($ids, TripTicketTemplateEnum::_3c());
        $this->create($ids, TripTicketTemplateEnum::_4o());
        $this->create($ids, TripTicketTemplateEnum::ecm2());

        $this->create3($ids);

        // copy reverse sheets
        foreach ($this->reverseSheetNames($this->usedTemplates) as $sheetName => $sheetPrefix) {
            $sheet = clone $this->spreadsheet->getSheetByName($sheetName);
            $sheet->setTitle($sheetPrefix);
            $this->spreadsheet->addSheet($sheet);
        }

        // delete template sheets
        foreach ($this->templateSheetNames() as $sheetName) {
            $sheet = $this->spreadsheet->getSheetByName($sheetName);
            $this->spreadsheet->removeSheetByIndex($this->spreadsheet->getIndex($sheet));
        }

        foreach ($this->tripTickets as $tripTicket) {
            if ($tripTicket->type === TripTicketType::IN_ADVANCE && in_array($tripTicket->status, [TripTicketStatus::ACTIVATED, TripTicketStatus::APPROVED])) {
                continue;
            }

            event(new ChangeTripTicketStatus($tripTicket, TripTicketStatus::printed()));

            if ($tripTicket->getOriginal('status') !== TripTicketStatus::PRINTED) {
                event(new LogTripTicket(Auth::user(), $tripTicket, TripTicketActionType::changeStatus()));
            }
        }

        return new Xlsx($this->spreadsheet);
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

    private function reverseSheetNames(array $usedTemplates): array
    {
        return array_reduce($usedTemplates, function (array $carry, string $template) {
            if ($template === TripTicketTemplateEnum::PG1) {
                return $carry;
            }

            $carry[config("trip-ticket.print.$template.template.reverse.sheet")] = config("trip-ticket.print.$template.template.reverse.prefix");

            return $carry;
        }, []);
    }

    private function templateSheetNames(): array
    {
        return [
            config('trip-ticket.print.4s.template.front.sheet'),
            config('trip-ticket.print.4s.template.reverse.sheet'),
            config('trip-ticket.print.3.template.front.sheet'),
            config('trip-ticket.print.3.template.reverse.sheet'),
            config('trip-ticket.print.4p.template.front.sheet'),
            config('trip-ticket.print.4p.template.reverse.sheet'),
            config('trip-ticket.print.pg1.template.front.sheet'),
            config('trip-ticket.print.6c.template.front.sheet'),
            config('trip-ticket.print.6c.template.reverse.sheet'),
            config('trip-ticket.print.3c.template.front.sheet'),
            config('trip-ticket.print.3c.template.reverse.sheet'),
            config('trip-ticket.print.4o.template.front.sheet'),
            config('trip-ticket.print.4o.template.reverse.sheet'),
            config('trip-ticket.print.ecm2.template.front.sheet'),
            config('trip-ticket.print.ecm2.template.reverse.sheet'),
        ];
    }

    private function create(array $ids, TripTicketTemplateEnum $template)
    {
        $tripTickets = TripTicket::query()
            ->whereIn('uuid', $ids)
            ->where('template_code', '=', $template->value())
            ->orderBy('start_date')
            ->orderBy('period_pl')
            ->get();

        if ($tripTickets->count()) {
            $this->usedTemplates[] = $template->value();

            foreach ($tripTickets as $tripTicket) {
                $mapper = new ItemMapperStrategy($tripTicket);
                $writer = new SheetWriterStrategy($template);

                $item = $mapper->map();
                $this->spreadsheet = $writer->createSheets($this->spreadsheet, $item, $this->globalIndex);

                $this->globalIndex++;
                $this->tripTickets[] = $tripTicket;
            }
        }
    }

    private function create3(array $ids)
    {
        $tripTickets3 = TripTicket::query()
            ->whereIn('uuid', $ids)
            ->where('template_code', '=', TripTicketTemplateEnum::_3)
            ->orderBy('start_date')
            ->orderBy('period_pl')
            ->get();

        if ($tripTickets3->count()) {
            $this->usedTemplates[] = TripTicketTemplateEnum::_3;

            foreach ($tripTickets3->chunk(2) as $couple) {
                $first = $couple->values()[0];
                $second = $couple->values()[1] ?? null;

                $leftMapper = new ItemMapperStrategy($first);
                $rightMapper = null;
                if ($second) {
                    $rightMapper = new ItemMapperStrategy($second);
                }

                $leftItem = $leftMapper->map();
                $rightItem = null;
                if ($rightMapper) {
                    $rightItem = $rightMapper->map();
                }

                $writer = new SheetWriterStrategy(TripTicketTemplateEnum::_3());
                $this->spreadsheet = $writer->createSheets(
                    $this->spreadsheet,
                    new ExportedItem3(
                        $leftItem,
                        $rightItem
                    ),
                    $this->globalIndex);

                $this->globalIndex += 2;
                $this->tripTickets[] = $first;
                if ($second) {
                    $this->tripTickets[] = $second;
                }
            }
        }
    }
}
