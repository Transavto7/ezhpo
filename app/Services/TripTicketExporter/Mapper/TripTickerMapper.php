<?php

namespace App\Services\TripTicketExporter\Mapper;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\Models\TripTicket;
use App\Services\TripTicketExporter\ViewModels\Car;
use App\Services\TripTicketExporter\ViewModels\Company;
use App\Services\TripTicketExporter\ViewModels\Driver;
use App\Services\TripTicketExporter\ViewModels\ExportData;
use App\Services\TripTicketExporter\ViewModels\TripTicket as TripTicketViewModel;
use Illuminate\Support\Carbon;

class TripTickerMapper
{
    public function fromEloquent(TripTicket $model): ExportData
    {
        $tripTicket = new TripTicketViewModel(
            $model->ticket_number,
            Carbon::parse($model->start_date),
            $model->validity_period,
            LogisticsMethodEnum::fromString($model->logistics_method),
            TransportationTypeEnum::fromString($model->transportation_type)
        );

        $company = null;
        if ($model->company) {
            $company = new Company($model->company->name, $model->company->where_call);
        }

        $driver = null;
        if ($model->driver) {
            $driverLicenseDate = null;
            if ($model->driver->driver_license_issued_at) {
                $driverLicenseDate = Carbon::parse($model->driver->driver_license_issued_at);
            }

            $driver = new Driver(
                $model->driver->hash_id,
                $model->driver->fio,
                $model->driver->driver_license,
                $driverLicenseDate,
                $model->driver->snils,
            );
        }

        $car = null;
        if ($model->car) {
            $car = new Car(
                $model->car->hash_id,
                $model->car->gos_number,
                $model->car->mark_model,
                $model->car->type_auto,
            );
        }

        return new ExportData(
            TripTicketTemplateEnum::fromString($model->template_code),
            $tripTicket,
            $company,
            $driver,
            $car
        );
    }
}
