<?php

namespace App\Services\TripTicketExporter;

use App\Models\TripTicket;
use App\Services\TripTicketExporter\ExcelGenerator\TripTicketExcelGenerator;
use App\Services\TripTicketExporter\Mapper\TripTickerMapper;
use App\ValueObjects\EntityId;
use DomainException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TripTicketExporter
{
    /**
     * @var TripTickerMapper
     */
    private $mapper;

    /**
     * @param TripTickerMapper $mapper
     */
    public function __construct(TripTickerMapper $mapper)
    {
        $this->mapper = $mapper;
    }


    public function export(EntityId $id): BinaryFileResponse
    {
        $tripTicket = TripTicket::where('uuid', '=', $id);

        if (!$tripTicket->exists()) {
            throw new DomainException('Путевой лист не найден');
        }

        $tripTicket = $tripTicket->first();

        $exportData = $this->mapper->fromEloquent($tripTicket);

        $export = new TripTicketExcelGenerator($exportData);

        return Excel::download($export, 'Путевой лист.xlsx');
    }
}
