<?php

namespace App\Services\TripTicketExporter\ExcelGenerator;

use App\Services\TripTicketExporter\ViewModels\ExportData;
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
            ->fillEmployees()
            ->fillStamps();

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

    private function fillEmployees(): self
    {
        $this->sheet->setCellValue('Z52', $this->data->getMedicFormUserName() ?? '');
        $this->sheet->setCellValue('CG52', $this->data->getTechFormUserName() ?? '');

        $driver = $this->data->getDriver();
        if ($driver) {
            $this->sheet->setCellValue('EY47', $driver->getFio());
            $this->sheet->setCellValue('EY52', $driver->getFio());
        }

        return $this;
    }

    public function fillStamps(): self
    {
        $now = Carbon::now();

        $companyStamp = config('trip-ticket.stamps.company');
        $permitStamp = config('trip-ticket.stamps.permit');

        $date = 'Дата: '.$now->day;
        $date .= ' '.trans('date.months_genitive.'.$now->month);
        $date .= ' '.$now->year;
        $date .= '    Время: '.$now->format('H:i');

        $this->sheet->setCellValue('R44', $companyStamp."\n\n".$date);
        $this->sheet->setCellValue('BY44', $permitStamp."\n\n".$date);

        return $this;
    }
}
