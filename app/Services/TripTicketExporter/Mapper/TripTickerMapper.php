<?php

namespace App\Services\TripTicketExporter\Mapper;

use App\Enums\FormTypeEnum;
use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\Models\Forms\Form;
use App\Models\Forms\TechForm;
use App\Models\TripTicket;
use App\Services\TripTicketExporter\ViewModels\CarViewModel;
use App\Services\TripTicketExporter\ViewModels\CompanyViewModel;
use App\Services\TripTicketExporter\ViewModels\DriverViewModel;
use App\Services\TripTicketExporter\ViewModels\ExportData;
use App\Services\TripTicketExporter\ViewModels\FormViewModel;
use App\Services\TripTicketExporter\ViewModels\TripTicketViewModel;
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
            $company = new CompanyViewModel($model->company->name, $model->company->where_call);
        }

        $driver = null;
        if ($model->driver) {
            $driverLicenseDate = null;
            if ($model->driver->driver_license_issued_at) {
                $driverLicenseDate = Carbon::parse($model->driver->driver_license_issued_at);
            }

            $driver = new DriverViewModel(
                $model->driver->hash_id,
                $model->driver->fio,
                $model->driver->driver_license,
                $driverLicenseDate,
                $model->driver->snils,
            );
        }

        $car = null;
        if ($model->car) {
            $car = new CarViewModel(
                $model->car->hash_id,
                $model->car->gos_number,
                $model->car->mark_model,
                $model->car->type_auto,
            );
        }

        $medicForm = null;
        if ($model->medicForm) {
            $medicForm = $this->getTypedForm($model->medicForm);
        }

        $techForm = null;
        if ($model->techForm) {
            $techForm = $this->getTypedForm($model->techForm);
        }

        return new ExportData(
            TripTicketTemplateEnum::fromString($model->template_code),
            $tripTicket,
            $company,
            $driver,
            $car,
            $medicForm,
            $techForm
        );
    }

    private function getTypedForm(Form $form): ?FormViewModel
    {
        $odometer = null;

        if ($form->type_anketa === FormTypeEnum::TECH) {
            $typedForm = TechForm::find($form->uuid)->first();
            $odometer = $typedForm->odometer;
        }

        $username = null;
        if ($form->user) {
            $username = $form->user->name;
        }

        $date = $form->date;
        if ($date) {
            $date = Carbon::parse($date);
        }

        return new FormViewModel(
            $date,
            $username,
            $odometer
        );
    }
}
