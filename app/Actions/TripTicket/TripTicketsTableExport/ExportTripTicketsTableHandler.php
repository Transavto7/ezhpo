<?php

namespace App\Actions\TripTicket\TripTicketsTableExport;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ExportTripTicketsTableHandler implements FromView, WithBatchInserts, WithChunkReading
{
    private $tripTickets;

    private $fields;

    public function __construct($tripTickets, $fields)
    {
        $this->tripTickets = $tripTickets;
        $this->fields = $fields;
    }

    public function view(): View
    {
        $logisticsMethods = LogisticsMethodEnum::labels();
        $transportationTypes = TransportationTypeEnum::labels();
        $templateCodes = TripTicketTemplateEnum::labels();

        foreach ($this->tripTickets as $tripTicket) {
            $tripTicket->logistics_method = $logisticsMethods[$tripTicket->logistics_method];
            $tripTicket->transportation_type = $transportationTypes[$tripTicket->transportation_type];
            $tripTicket->template_code = $templateCodes[$tripTicket->template_code];
            $tripTicket->start_date = $tripTicket->start_date
                ? Carbon::parse($tripTicket->start_date)->format('d.m.Y')
                : null;
            $tripTicket->period_pl = $tripTicket->period_pl
                ? Carbon::parse($tripTicket->period_pl)->format('m.Y')
                : null;
        }

        return view('home-export', [
            'data' => $this->tripTickets,
            'fields' => $this->fields
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
