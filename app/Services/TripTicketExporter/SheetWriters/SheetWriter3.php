<?php

namespace App\Services\TripTicketExporter\SheetWriters;

use App\Enums\TripTicket\LogisticsMethodEnum;
use App\Enums\TripTicket\TransportationTypeEnum;
use App\Services\QRCode\QRCodeGeneratorInterface;
use App\Services\TripTicketExporter\ViewModels\ExportedItem;
use App\Services\TripTicketExporter\ViewModels\ExportedItem4S;
use App\Services\TripTicketExporter\ViewModels\MedicFormViewModel;
use App\Services\TripTicketExporter\ViewModels\TechFormViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class SheetWriter3 implements SheetWriterInterface
{
    /**
     * @var Worksheet
     */
    private $sheet;
    /**
     * @var ExportedItem4S
     */
    private $data;

    /**
     * @var QRCodeGeneratorInterface
     */
    private $qrCodeGenerator;

    /**
     * @param QRCodeGeneratorInterface $qrCodeGenerator
     */
    public function __construct(QRCodeGeneratorInterface $qrCodeGenerator)
    {
        $this->qrCodeGenerator = $qrCodeGenerator;
    }

    public function templateSheetName(): string
    {
        return config('trip-ticket.print.3.template.front.sheet');
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param ExportedItem4S $item
     * @param int $number
     * @return Spreadsheet
     * @throws Exception
     */
    public function createSheet(Spreadsheet $spreadsheet, ExportedItem $item, int $number): Spreadsheet
    {
        $this->sheet = clone $spreadsheet->getSheetByName($this->templateSheetName());

        $title = $number . '. ' . config('trip-ticket.print.3.template.front.prefix');

        if ($item->getTripTicket()->getTicketNumber()) {
            $title .= ' (' . $item->getTripTicket()->getTicketNumber() . ')';
        }

        $this->sheet->setTitle($title);

        $spreadsheet->addSheet($this->sheet);

        $this->data = $item;

        $this->fillIds()
            ->fillTripTicketNumber()
            ->fillPeriod()
            ->fillCompany()
            ->fillCar()
            ->fillDriver()
            ->fillOdometer()
            ->fillEmployees()
            ->fillMedicStamp()
            ->fillTechStamp()
            ->fillLogisticMethod()
            ->fillTransportationType();

        return $spreadsheet;
    }

    private function fillIds(): self
    {
        $value = '';

        $driver = $this->data->getDriver();
        $car = $this->data->getCar();

        if ($driver) {
            $value = "ID - " . $driver->getId() . " Водитель\n";
        }

        if ($car) {
            $value .= "ID - " . $car->getId() . " Автомобиль";
        }

        $this->sheet->setCellValue('BY1', $value);
        $this->sheet->setCellValue('FT1', $value);

        return $this;
    }

    private function fillTripTicketNumber(): self
    {
        $number = $this->data->getTripTicket()->getTicketNumber();
        $externalNumber = $this->data->getTripTicket()->getExternalTicketNumber();

        $this->sheet->setCellValue('BD2', $externalNumber ?: $number);
        $this->sheet->setCellValue('EY2', $externalNumber ?: $number);
        $this->sheet->setCellValue('BD3', $number);
        $this->sheet->setCellValue('EY3', $number);

        return $this;
    }

    private function fillPeriod(): self
    {
        if ($this->data->getTripTicket()->getStartDate()) {
            $period = $this->data->getTripTicket()->getValidityPeriod();
            $startDate = $this->data->getTripTicket()->getStartDate();
            $endDate = $startDate->copy()->addDays($period - 1);

            $this->sheet->setCellValue('U4', $startDate->day);
            $this->sheet->setCellValue('DP4', $startDate->day);
            $this->sheet->setCellValue('BA4', $endDate->day);
            $this->sheet->setCellValue('EV4', $endDate->day);
        } else {
            $startDate = $this->data->getTripTicket()->getPeriodPl();
            $endDate = $startDate;

            $this->sheet->setCellValue('U4', null);
            $this->sheet->setCellValue('DP4', null);
            $this->sheet->setCellValue('BA4', null);
            $this->sheet->setCellValue('EV4', null);
        }

        $this->sheet->setCellValue('Z4', trans('date.months_genitive.' . $startDate->month));
        $this->sheet->setCellValue('DU4', trans('date.months_genitive.' . $startDate->month));
        $this->sheet->setCellValue('AK4', $startDate->year);
        $this->sheet->setCellValue('EF4', $startDate->year);

        $this->sheet->setCellValue('BF4', trans('date.months_genitive.' . $endDate->month));
        $this->sheet->setCellValue('FA4', trans('date.months_genitive.' . $endDate->month));
        $this->sheet->setCellValue('BP4', $endDate->year);
        $this->sheet->setCellValue('FK4', $endDate->year);

        return $this;
    }

    private function fillCompany(): self
    {
        $company = $this->data->getCompany();

        $companyStringItems = [
            $company->getName(),
        ];

        if ($company->getAddress()) {
            $companyStringItems[] = $company->getAddress();
        }

        if ($company->getOgrn()) {
            $companyStringItems[] = 'ОГРН ' . $company->getOgrn();
        }

        if ($company->getWhereCall()) {
            $companyStringItems[] = 'тел. ' . $company->getWhereCall();
        }

        $value = implode(", ", $companyStringItems);

        $this->sheet->setCellValue('C6', $value);
        $this->sheet->setCellValue('CX6', $value);

        return $this;
    }

    private function fillCar(): self
    {
        $car = $this->data->getCar();

        if (!$car) {
            return $this;
        }

        $this->sheet->setCellValue('V10', $car->getTypeAuto() . ', ' . $car->getMarkModel());
        $this->sheet->setCellValue('DQ10', $car->getTypeAuto() . ', ' . $car->getMarkModel());
        $this->sheet->setCellValue('AC11', $car->getGosNumber());
        $this->sheet->setCellValue('DX11', $car->getGosNumber());

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

        $this->sheet->setCellValue('K12', $driver->getFio());
        $this->sheet->setCellValue('DF12', $driver->getFio());
        $this->sheet->setCellValue('Z14', $driverLicense);
        $this->sheet->setCellValue('DU14', $driverLicense);
        $this->sheet->setCellValue('Z15', $driver->getSnils() ?? '');
        $this->sheet->setCellValue('DU15', $driver->getSnils() ?? '');

        return $this;
    }

    private function fillOdometer(): self
    {
        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('AW27', $techForm->getOdometer() ?? '');
            $this->sheet->setCellValue('ER27', $techForm->getOdometer() ?? '');
        }

        return $this;
    }

    private function fillEmployees(): self
    {
        $medicForm = $this->data->getMedicForm();
        if ($medicForm) {
            $this->sheet->setCellValue('Z45', $medicForm->getUsername() ?? '');
            $this->sheet->setCellValue('DU45', $medicForm->getUsername() ?? '');
        }

        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('BW45', $techForm->getUsername() ?? '');
            $this->sheet->setCellValue('FR45', $techForm->getUsername() ?? '');
        }

        $driver = $this->data->getDriver();
        if ($driver) {
            $this->sheet->setCellValue('Z52', $driver->getFio());
            $this->sheet->setCellValue('Z55', $driver->getFio());
            $this->sheet->setCellValue('DU52', $driver->getFio());
            $this->sheet->setCellValue('DU55', $driver->getFio());
        }

        return $this;
    }

    public function fillMedicStamp(): self
    {
        $stamp = null;
        if ($this->data->getMedicForm()) {
            $stamp = $this->data->getMedicForm()->getStamp();
        }

        if (! $stamp) {
            $this->sheet->getStyle('M36:AU44')->getFill()->setFillType(Fill::FILL_NONE);
            $this->sheet->getStyle('DH36:EP44')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $medicStamp = $stamp->getReqName() . "\n";
        $medicStamp .= wordwrap($stamp->getLicense(), 55) . "\n";
        $medicStamp .= config('trip-ticket.print.3.stamps.medic.comment');

        $medicForm = $this->data->getMedicForm();

        $date = $this->getDateString($medicForm);

        $this->sheet->setCellValue('N37', $medicStamp . "\n\n" . $date);
        $this->sheet->setCellValue('DI37', $medicStamp . "\n\n" . $date);

        $url = route('anketa.verification.page', [
            'uuid' => $this->data->getMedicForm()->getUuid(),
        ]);

        $qrCodeFileName = 'qrcodes/medic_' . $this->data->getMedicForm()->getUuid() . '.jpg';
        $qrCodeFilePath = Storage::disk('public')->path($qrCodeFileName);

        $this->qrCodeGenerator->generate($url, QRCodeGeneratorInterface::VERSION_6, $qrCodeFilePath);

        $drawing = new Drawing();
        $drawing->setName('Маркировка осмотра');
        $drawing->setDescription('Маркировка осмотра');
        $drawing->setPath($qrCodeFilePath);
        $drawing->setCoordinates('B38');
        $drawing->setWidth(55);
        $drawing->setHeight(55);
        $drawing->setWorksheet($this->sheet);

        $drawing = new Drawing();
        $drawing->setName('Маркировка осмотра');
        $drawing->setDescription('Маркировка осмотра');
        $drawing->setPath($qrCodeFilePath);
        $drawing->setCoordinates('CW38');
        $drawing->setWidth(55);
        $drawing->setHeight(55);
        $drawing->setWorksheet($this->sheet);

        return $this;
    }

    private function fillTechStamp(): self
    {
        $techStamp = config('trip-ticket.print.3.stamps.tech');

        $techForm = $this->data->getTechForm();

        if (! $techForm) {
            $this->sheet->getStyle('BJ36:CR44')->getFill()->setFillType(Fill::FILL_NONE);
            $this->sheet->getStyle('FE36:GM44')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $date = $this->getDateString($techForm);

        $this->sheet->setCellValue('BK37', $techStamp . "\n\n" . $date);
        $this->sheet->setCellValue('FF37', $techStamp . "\n\n" . $date);

        return $this;
    }

    private function fillLogisticMethod(): self
    {
        $value = $this->data->getTripTicket()->getLogisticsMethod()->value();

        switch (true) {
            case $value === LogisticsMethodEnum::URBAN:
                $this->sheet->setCellValue('B19', '+');
                $this->sheet->setCellValue('CW19', '+');
                break;
            case $value === LogisticsMethodEnum::SUBURBAN:
                $this->sheet->setCellValue('B20', '+');
                $this->sheet->setCellValue('CW20', '+');
                break;
            case $value === LogisticsMethodEnum::LONG_DISTANCE:
                $this->sheet->setCellValue('B21', '+');
                $this->sheet->setCellValue('CW21', '+');
                break;
            default:
        }

        return $this;
    }

    private function fillTransportationType(): self
    {
        $value = $this->data->getTripTicket()->getTransportationType()->value();

        switch (true) {
            case $value === TransportationTypeEnum::REGULAR:
                $this->sheet->setCellValue('T19', '+');
                $this->sheet->setCellValue('DO19', '+');
                break;
            case $value === TransportationTypeEnum::TAXI:
                $this->sheet->setCellValue('T20', '+');
                $this->sheet->setCellValue('DO20', '+');
                break;
            case $value === TransportationTypeEnum::CONTRACT:
                $this->sheet->setCellValue('T21', '+');
                $this->sheet->setCellValue('DO21', '+');
                break;
            case $value === TransportationTypeEnum::ORDER:
                $this->sheet->setCellValue('BE19', '+');
                $this->sheet->setCellValue('EZ19', '+');
                break;
            case $value === TransportationTypeEnum::SELF_NEEDS:
                $this->sheet->setCellValue('BE20', '+');
                $this->sheet->setCellValue('EZ20', '+');
                break;
            default:
        }

        return $this;
    }

    private function getFormDate(?Carbon $date, bool $hasDay = true, bool $hasTime = true): string
    {
        if (!$date) {
            return 'Дата: _____ ________ 20__   Время: __:__';
        }

        $value = 'Дата: ' . ($hasDay ? $date->day : '_____');
        $value .= ' ' . trans('date.months_genitive.' . $date->month);
        $value .= ' ' . $date->year;
        $value .= '    Время: ' . ($hasTime ? $date->format('H:i') : '__:__');

        return $value;
    }

    /**
     * @param MedicFormViewModel|TechFormViewModel $formViewModel
     * @return string
     */
    private function getDateString($formViewModel): string
    {
        if ($formViewModel->getDate()) {
            $date = $this->getFormDate($formViewModel->getDate());
        } else if ($formViewModel->getPeriodPl()) {
            $date = $this->getFormDate($formViewModel->getPeriodPl(), false, false);
        } else {
            $date = $this->getFormDate(null);
        }

        return $date;
    }
}
