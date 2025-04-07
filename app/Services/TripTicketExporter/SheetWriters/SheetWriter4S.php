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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class SheetWriter4S implements SheetWriterInterface
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
        return config('trip-ticket.print.4s.template.front.sheet');
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

        $title = $number . '. ' . config('trip-ticket.print.4s.template.front.prefix');

        $title .= $item->getTripTicket()->getExternalTicketNumber()
            ? ' (' . $item->getTripTicket()->getExternalTicketNumber() . ')'
            : ' (' . $item->getTripTicket()->getTicketNumber() . ')';

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
            $value .= 'ID - ' . $car->getId() . ' Автомобиль';
        }

        $this->sheet->setCellValue('DZ1', $value);

        return $this;
    }

    private function fillTripTicketNumber(): self
    {
        $number = $this->data->getTripTicket()->getTicketNumber();
        $externalNumber = $this->data->getTripTicket()->getExternalTicketNumber();

        $this->sheet->setCellValue('CZ3', $externalNumber ?: $number);
        $this->sheet->setCellValue('CZ4', $number);

        return $this;
    }

    private function fillPeriod(): self
    {
        if ($this->data->getTripTicket()->getStartDate()) {
            $period = $this->data->getTripTicket()->getValidityPeriod();
            $startDate = $this->data->getTripTicket()->getStartDate();
            $endDate = $startDate->copy()->addDays($period - 1);

            $this->sheet->setCellValue('AV5', $startDate->day);
            $this->sheet->setCellValue('CT5', $endDate->day);
        } else {
            $startDate = $this->data->getTripTicket()->getPeriodPl();
            $endDate = $startDate;

            $this->sheet->setCellValue('AV5', null);
            $this->sheet->setCellValue('CT5', null);
        }

        $this->sheet->setCellValue('BF5', trans('date.months_genitive.' . $startDate->month));
        $this->sheet->setCellValue('BY5', $startDate->year);

        $this->sheet->setCellValue('DC5', trans('date.months_genitive.' . $endDate->month));
        $this->sheet->setCellValue('DU5', $endDate->year);

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

        $this->sheet->setCellValue('J6', $value);

        return $this;
    }

    private function fillCar(): self
    {
        $car = $this->data->getCar();

        if (!$car) {
            return $this;
        }

        $this->sheet->setCellValue('R8', $car->getTypeAuto() . ', ' . $car->getMarkModel());
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
        $stamp = null;
        if ($this->data->getMedicForm()) {
            $stamp = $this->data->getMedicForm()->getStamp();
        }

        if (! $stamp) {
            $this->sheet->getStyle('Q43:BA50')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
            $this->sheet->getStyle('Q43:BA50')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $medicStamp = $stamp->getReqName() . "\n";
        $medicStamp .= wordwrap($stamp->getLicense(), 55) . "\n";
        $medicStamp .= config('trip-ticket.print.4s.stamps.medic.comment');

        $medicForm = $this->data->getMedicForm();

        $date = $this->getDateString($medicForm);

        $this->sheet->setCellValue('R44', $medicStamp . "\n" . $date);

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
        $drawing->setCoordinates('A43');
        $drawing->setWidth(70);
        $drawing->setHeight(70);
        $drawing->setOffsetX(15);
        $drawing->setOffsetY(10);
        $drawing->setWorksheet($this->sheet);

        return $this;
    }

    private function fillTechStamp(): self
    {
        $techStamp = config('trip-ticket.print.4s.stamps.tech');

        $techForm = $this->data->getTechForm();

        if (! $techForm) {
            $this->sheet->getStyle('BX43:DH50')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
            $this->sheet->getStyle('BX43:DH50')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $date = $this->getDateString($techForm);

        $this->sheet->setCellValue('BY44', $techStamp . "\n\n" . $date);

        return $this;
    }

    private function fillLogisticMethod(): self
    {
        $value = $this->data->getTripTicket()->getLogisticsMethod()->value();

        switch (true) {
            case $value === LogisticsMethodEnum::URBAN:
                $this->sheet->setCellValue('A22', '+');
                break;
            case $value === LogisticsMethodEnum::SUBURBAN:
                $this->sheet->setCellValue('A25', '+');
                break;
            case $value === LogisticsMethodEnum::LONG_DISTANCE:
                $this->sheet->setCellValue('A27', '+');
                break;
            default:
        }

        return $this;
    }

    private function fillTransportationType(): self
    {
        $value = $this->data->getTripTicket()->getTransportationType()->value();

        switch (true) {
            case $value === TransportationTypeEnum::SELF_NEEDS:
                $this->sheet->setCellValue('T22', '+');
                break;
            case $value === TransportationTypeEnum::SPECIAL_VEHICLE:
                $this->sheet->setCellValue('T25', '+');
                break;
            case $value === TransportationTypeEnum::CONTRACT:
                $this->sheet->setCellValue('BJ22', '+');
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
