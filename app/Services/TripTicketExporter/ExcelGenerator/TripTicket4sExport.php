<?php

namespace App\Services\TripTicketExporter\ExcelGenerator;

use App\Services\TripTicketExporter\ViewModels\ExportData;
use App\Services\TripTicketExporter\ViewModels\FormViewModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Sheet;

class TripTicket4sExport implements ExportStrategy
{
    /**
     * @var Sheet
     */
    private $sheet;
    /**
     * @var ExportData
     */
    private $data;

    public function getTemplate(): string
    {
        return public_path('templates/trip-tickets/4s.xlsx');
    }

    public function fillSheet(Sheet $sheet, ExportData $data): Sheet
    {
        $this->sheet = $sheet;
        $this->data = $data;

        $this->fillIds()
            ->fillTripTicketNumber()
            ->fillPeriod()
            ->fillCompany()
            ->fillCar()
            ->fillDriver()
            ->fillOdometer()
            ->fillEmployees()
            ->fillMedicStamp()
            ->fillTechStamp();

        return $this->sheet;
    }

    private function fillIds(): self
    {
        $value = '';

        $driver = $this->data->getDriver();
        $car = $this->data->getCar();

        if ($driver) {
            $value = "ID - ".$driver->getId()." Водитель\n";
        }

        if ($car) {
            $value .= 'ID - '.$car->getId().' Автомобиль';
        }

        $this->sheet->setCellValue('DZ1', $value);

        return $this;
    }

    private function fillTripTicketNumber(): self
    {
        $number = $this->data->getTripTicket()->getTicketNumber();
        $this->sheet->setCellValue('CZ3', $number);

        return $this;
    }

    private function fillPeriod(): self
    {
        $period = $this->data->getTripTicket()->getValidityPeriod();
        $startDate = $this->data->getTripTicket()->getStartDate();
        $endDate = $startDate->copy()->addDays($period);

        $this->sheet->setCellValue('AV5', $startDate->day);
        $this->sheet->setCellValue('BF5', trans('date.months_genitive.' . $startDate->month));
        $this->sheet->setCellValue('CB5', $startDate->year);

        $this->sheet->setCellValue('CT5', $endDate->day);
        $this->sheet->setCellValue('DC5', trans('date.months_genitive.' . $endDate->month));
        $this->sheet->setCellValue('DX5', $endDate->year);

        return $this;
    }

    private function fillCompany(): self
    {
        $company = $this->data->getCompany();
        $value = $company->getName();

        if ($company->getWhereCall()) {
            $value .= ', тел. '.$company->getWhereCall();
        }

        $value .= '.';

        $this->sheet->setCellValue('J6', $value);

        return $this;
    }

    private function fillCar(): self
    {
        $car = $this->data->getCar();

        if (!$car) {
            return $this;
        }

        $this->sheet->setCellValue('R8', $car->getTypeAuto().', '.$car->getMarkModel());
        $this->sheet->setCellValue('AE9', $car->getGosNumber());

        return $this;
    }

    private function fillDriver(): self
    {
        $driver = $this->data->getDriver();

        if (!$driver) {
            return $this;
        }

        $driverLicense = '';
        if ($driver->getDriverLicense()) {
            $driverLicense = $driver->getDriverLicense();
        }

        if ($driver->getDriverLicenseDate()) {
            $driverLicense .= ' от ' . $driver->getDriverLicenseDate()->format('d.m.Y');
        }

        $this->sheet->setCellValue('H10', $driver->getFio());
        $this->sheet->setCellValue('Y12', $driverLicense);
        $this->sheet->setCellValue('S13', $driver->getSnils() ?? '');

        return $this;
    }

    private function fillOdometer(): self
    {
        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('EO13', $techForm->getOdometer() ?? '');
        }

        return $this;
    }

    private function fillEmployees(): self
    {
        $medicForm = $this->data->getMedicForm();
        if ($medicForm) {
            $this->sheet->setCellValue('Z52', $medicForm->getUsername() ?? '');
        }

        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('CG52', $techForm->getUsername() ?? '');
        }

        $driver = $this->data->getDriver();
        if ($driver) {
            $this->sheet->setCellValue('EY47', $driver->getFio());
            $this->sheet->setCellValue('EY52', $driver->getFio());
        }

        return $this;
    }

    public function fillMedicStamp(): self
    {
        $medicStamp = config('trip-ticket.stamps.medic');

        $date = $this->getFormDate($this->data->getMedicForm());

        $this->sheet->setCellValue('R44', $medicStamp."\n\n".$date);

        return $this;
    }

    public function fillTechStamp(): self
    {
        $techStamp = config('trip-ticket.stamps.permit');

        $date = $this->getFormDate($this->data->getTechForm());

        $this->sheet->setCellValue('BY44', $techStamp."\n\n".$date);

        return $this;
    }

    private function getFormDate(?FormViewModel $form): string
    {
        if ($form && $form->getDate()) {
            $date = $form->getDate();

            $value = 'Дата: '.$date->day;
            $value .= ' '.trans('date.months_genitive.'.$date->month);
            $value .= ' '.$date->year;
            $value .= '    Время: '.$date->format('H:i');

            return $value;
        }

        return 'Дата: _____ ________ 20__   Время: __:__';
    }
}
