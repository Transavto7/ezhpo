<?php

namespace App\Services\TripTicketExporter\SheetWriters;

use App\Enums\TripTicket\LogisticsMethodEnum;
use App\Enums\TripTicket\TransportationTypeEnum;
use App\Services\QRCode\QRCodeGeneratorInterface;
use App\Services\TripTicketExporter\ViewModels\ExportedItem;
use App\Services\TripTicketExporter\ViewModels\ExportedItem6C;
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

final class SheetWriter6C implements SheetWriterInterface
{
    /**
     * @var Worksheet
     */
    private $sheet;
    /**
     * @var ExportedItem6C
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
        return config('trip-ticket.print.6c.template.front.sheet');
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param ExportedItem6C $item
     * @param int $number
     * @return Spreadsheet
     * @throws Exception
     */
    public function createSheet(Spreadsheet $spreadsheet, ExportedItem $item, int $number): Spreadsheet
    {
        $this->sheet = clone $spreadsheet->getSheetByName($this->templateSheetName());

        $title = $number . '. ' . config('trip-ticket.print.6c.template.front.prefix');

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

        $this->sheet->setCellValue('AO1', $value);

        return $this;
    }

    private function fillTripTicketNumber(): self
    {
        $number = $this->data->getTripTicket()->getTicketNumber();
        $externalNumber = $this->data->getTripTicket()->getExternalTicketNumber();

        $this->sheet->setCellValue('AE2', $externalNumber ?: $number);
        $this->sheet->setCellValue('BL3', $externalNumber ?: $number);
        $this->sheet->setCellValue('AE3', $number);
        $this->sheet->setCellValue('BL4', $number);

        return $this;
    }

    private function fillPeriod(): self
    {
        if ($this->data->getTripTicket()->getStartDate()) {
            $period = $this->data->getTripTicket()->getValidityPeriod();
            $startDate = $this->data->getTripTicket()->getStartDate();
            $endDate = $startDate->copy()->addDays($period - 1);

            $this->sheet->setCellValue('N4', $startDate->day);
            $this->sheet->setCellValue('Y4', $endDate->day);
        } else {
            $startDate = $this->data->getTripTicket()->getPeriodPl();
            $endDate = $startDate;

            $this->sheet->setCellValue('N4', null);
            $this->sheet->setCellValue('Y4', null);
        }

        $this->sheet->setCellValue('P4', trans('date.months_genitive.' . $startDate->month));
        $this->sheet->setCellValue('T4', $startDate->year);

        $this->sheet->setCellValue('AC4', trans('date.months_genitive.' . $endDate->month));
        $this->sheet->setCellValue('AJ4', $endDate->year);

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

        $this->sheet->setCellValue('A7', $value);
        $this->sheet->setCellValue('BF7', $value);

        return $this;
    }

    private function fillCar(): self
    {
        $car = $this->data->getCar();

        if (!$car) {
            return $this;
        }

        $this->sheet->setCellValue('J11', $car->getTypeAuto() . ', ' . $car->getMarkModel());
        $this->sheet->setCellValue('BF11', $car->getTypeAuto() . ', ' . $car->getMarkModel());
        $this->sheet->setCellValue('L12', $car->getGosNumber());
        $this->sheet->setCellValue('BN13', $car->getGosNumber());

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

        $this->sheet->setCellValue('E13', $driver->getFio());
        $this->sheet->setCellValue('K15', $driverLicense);
        $this->sheet->setCellValue('K16', $driver->getSnils() ?? '');

        return $this;
    }

    private function fillOdometer(): self
    {
        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('O29', $techForm->getOdometer() ?? '');
        }

        return $this;
    }

    private function fillEmployees(): self
    {
        $medicForm = $this->data->getMedicForm();
        if ($medicForm) {
            $this->sheet->setCellValue('N62', $medicForm->getUsername() ?? '');
        }

        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('AO50', $techForm->getUsername() ?? '');
        }

        $driver = $this->data->getDriver();
        if ($driver) {
            $this->sheet->setCellValue('AO56', $driver->getFio());
            $this->sheet->setCellValue('AO60', $driver->getFio());
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
            $this->sheet->getStyle('P56:X61')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
            $this->sheet->getStyle('P56:X61')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $medicStamp = $stamp->getReqName() . "\n";
        $medicStamp .= wordwrap($stamp->getLicense(), 55) . "\n";
        $medicStamp .= config('trip-ticket.print.stamps.medic.comment');

        $medicForm = $this->data->getMedicForm();

        $date = $this->getDateString($medicForm);

        $this->sheet->setCellValue('P56', $medicStamp . "\n" . $date);

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
        $drawing->setCoordinates('L57');
        $drawing->setWidth(70);
        $drawing->setHeight(70);
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($this->sheet);

        return $this;
    }

    private function fillTechStamp(): self
    {
        $techStamp = config('trip-ticket.print.stamps.tech');

        $techForm = $this->data->getTechForm();

        if (! $techForm) {
            $this->sheet->getStyle('AN42:AY49')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
            $this->sheet->getStyle('AN42:AY49')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $date = $this->getDateString($techForm);

        $this->sheet->setCellValue('AO42', $techStamp . "\n\n" . $date);

        return $this;
    }

    private function fillLogisticMethod(): self
    {
        $value = $this->data->getTripTicket()->getLogisticsMethod()->value();

        switch (true) {
            case $value === LogisticsMethodEnum::URBAN:
                $this->sheet->setCellValue('J18', '+');
                break;
            case $value === LogisticsMethodEnum::SUBURBAN:
                $this->sheet->setCellValue('T18', '+');
                break;
            case $value === LogisticsMethodEnum::LONG_DISTANCE:
                $this->sheet->setCellValue('AL18', '+');
                break;
            default:
        }

        return $this;
    }

    private function fillTransportationType(): self
    {
        $value = $this->data->getTripTicket()->getTransportationType()->value();

        switch (true) {
            case $value === TransportationTypeEnum::ORDER:
                $this->sheet->setCellValue('J20', '+');
                break;
            case $value === TransportationTypeEnum::CHILD_TRANSPORTATION:
                $this->sheet->setCellValue('T20', '+');
                break;
            case $value === TransportationTypeEnum::SELF_NEEDS:
                $this->sheet->setCellValue('AL20', '+');
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
