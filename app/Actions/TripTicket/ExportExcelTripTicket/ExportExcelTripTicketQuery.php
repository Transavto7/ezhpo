<?php

namespace App\Actions\TripTicket\ExportExcelTripTicket;

use App\Models\TripTicket;
use App\Services\TripTicketExporter\TripTicketExporter;
use App\Services\TripTicketExporter\TripTicketExporterParams;
use App\Services\TripTicketExporter\ValueObjects\Car;
use App\Services\TripTicketExporter\ValueObjects\Company;
use App\Services\TripTicketExporter\ValueObjects\Driver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExportExcelTripTicketQuery
{
    /**
     * @var TripTicketExporter
     */
    private $exporter;

    /**
     * @param TripTicketExporter $exporter
     */
    public function __construct(TripTicketExporter $exporter)
    {
        $this->exporter = $exporter;
    }

    public function get(ExportExcelTripTicketParams $params)
    {
        $tripTicket = TripTicket::where('uuid', '=', $params->getUuid());

        if (!$tripTicket->exists()) {
            throw new NotFoundHttpException();
        }

        $tripTicket = $tripTicket->first();

        $company = null;
        if ($tripTicket->company) {
            $company = Company::fromEloquent($tripTicket->company);
        }

        $car = null;
        if ($tripTicket->car) {
            $car = Car::fromEloquent($tripTicket->car);
        }

        $driver = null;
        if ($tripTicket->driver) {
            $driver = Driver::fromEloquent($tripTicket->driver);
        }

        $this->exporter->export(new TripTicketExporterParams(
            \App\Services\TripTicketExporter\ValueObjects\TripTicket::fromEloquent($tripTicket),
            $company,
            $tripTicket->driver_id,
            $driver,
            $tripTicket->car_id,
            $car,
        ));
    }
}
