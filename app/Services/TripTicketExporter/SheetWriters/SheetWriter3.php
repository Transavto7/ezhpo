<?php

namespace App\Services\TripTicketExporter\SheetWriters;

use App\Enums\TripTicket\LogisticsMethodEnum;
use App\Enums\TripTicket\TransportationTypeEnum;
use App\Services\QRCode\QRCodeGeneratorInterface;
use App\Services\TripTicketExporter\ViewModels\ExportedItem;
use App\Services\TripTicketExporter\ViewModels\ExportedItem3;
use App\Services\TripTicketExporter\ViewModels\ExportedTripTicketItem;
use App\Services\TripTicketExporter\ViewModels\MedicFormViewModel;
use App\Services\TripTicketExporter\ViewModels\TechFormViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
     * @var ExportedTripTicketItem
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
     * @param ExportedItem3 $item
     * @param int $number
     * @return Spreadsheet
     * @throws Exception
     */
    public function createSheet(Spreadsheet $spreadsheet, ExportedItem $item, int $number): Spreadsheet
    {
        $this->sheet = clone $spreadsheet->getSheetByName($this->templateSheetName());

        /**
         * @var ExportedTripTicketItem $left
         * @var ExportedTripTicketItem $right
         */
        $left = $item->getLeftTripTicket();
        $right = $item->getRightTripTicket();

        $title = (! $right
            ? $number
            : $number.'-'.($number + 1))
            . '. ' . config('trip-ticket.print.3.template.front.prefix');

        if (! $right) {
            $title .= $left->getTripTicket()->getExternalTicketNumber()
                ? ' (' . $left->getTripTicket()->getExternalTicketNumber() . ')'
                : ' (' . $left->getTripTicket()->getTicketNumber() . ')';
        } else {
            $title .= $left->getTripTicket()->getExternalTicketNumber()
                ? ' (' . $left->getTripTicket()->getExternalTicketNumber().', '
                : ' (' . $left->getTripTicket()->getTicketNumber().', ';
            $title .= $right->getTripTicket()->getExternalTicketNumber()
                ? $right->getTripTicket()->getExternalTicketNumber() . ')'
                : $right->getTripTicket()->getTicketNumber() . ')';
        }

        $this->sheet->setTitle($title);

        $spreadsheet->addSheet($this->sheet);

        $this->data = $left;

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

        if ($right) {
            $this->data = $right;

            $this->fillIds(false)
                ->fillTripTicketNumber(false)
                ->fillPeriod(false)
                ->fillCompany(false)
                ->fillCar(false)
                ->fillDriver(false)
                ->fillOdometer(false)
                ->fillEmployees(false)
                ->fillMedicStamp(false)
                ->fillTechStamp(false)
                ->fillLogisticMethod(false)
                ->fillTransportationType(false);
        }

        return $spreadsheet;
    }

    private function fillIds(bool $left = true): self
    {
        $driver = $this->data->getDriver();
        $car = $this->data->getCar();

        if ($driver) {
            $value = "ID - " . $driver->getId() . " Водитель\n";
            $this->sheet->setCellValue($left ? 'BY2' : 'FT2', $value);
            $this->sheet->getStyle($left ? 'BY2' : 'FT2')
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_BOTTOM)
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        if ($car) {
            $value = "ID - " . $car->getId() . " Автомобиль";
            $this->sheet->setCellValue($left ? 'BY4' : 'FT4', $value);
            $this->sheet->getStyle($left ? 'BY4' : 'FT4')
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_BOTTOM)
                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        return $this;
    }

    private function fillTripTicketNumber(bool $left = true): self
    {
        $number = $this->data->getTripTicket()->getTicketNumber();
        $externalNumber = $this->data->getTripTicket()->getExternalTicketNumber();

        $this->sheet->setCellValue($left ? 'BD2' : 'EY2', $externalNumber ?: $number);
        $this->sheet->setCellValue($left ? 'BD3' : 'EY3', $number);

        return $this;
    }

    private function fillPeriod(bool $left = true): self
    {
        if ($this->data->getTripTicket()->getStartDate()) {
            $period = $this->data->getTripTicket()->getValidityPeriod();
            $startDate = $this->data->getTripTicket()->getStartDate();
            $endDate = $startDate->copy()->addDays($period - 1);

            $this->sheet->setCellValue($left ? 'U4' : 'DP4', $startDate->day);
            $this->sheet->setCellValue($left ? 'BA4' : 'EV4', $endDate->day);
        } else {
            $startDate = $this->data->getTripTicket()->getPeriodPl();
            $endDate = $startDate;

            $this->sheet->setCellValue($left ? 'U4' : 'DP4', null);
            $this->sheet->setCellValue($left ? 'BA4' : 'EV4', null);
        }

        $this->sheet->setCellValue($left ? 'Z4' : 'DU4', trans('date.months_genitive.' . $startDate->month));
        $this->sheet->setCellValue($left ? 'AK4' : 'EF4', $startDate->year);

        $this->sheet->setCellValue($left ? 'BF4' : 'FA4', trans('date.months_genitive.' . $endDate->month));
        $this->sheet->setCellValue($left ? 'BP4' : 'FK4', $endDate->year);

        return $this;
    }

    private function fillCompany(bool $left = true): self
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

        $this->sheet->setCellValue($left ? 'C6' : 'CX6', $value);

        return $this;
    }

    private function fillCar(bool $left = true): self
    {
        $car = $this->data->getCar();

        if (!$car) {
            return $this;
        }

        $this->sheet->setCellValue($left ? 'V10' : 'DQ10', $car->getTypeAuto() . ', ' . $car->getMarkModel());
        $this->sheet->setCellValue($left ? 'AC11' : 'DX11', $car->getGosNumber());

        return $this;
    }

    private function fillDriver(bool $left = true): self
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

        $this->sheet->setCellValue($left ? 'K12' : 'DF12', $driver->getFio());
        $this->sheet->setCellValue($left ? 'Z14' : 'DU14', $driverLicense);
        $this->sheet->setCellValue($left ? 'Z15' : 'DU15', $driver->getSnils() ?? '');

        return $this;
    }

    private function fillOdometer(bool $left = true): self
    {
        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue($left ? 'AW27' : 'ER27', $techForm->getOdometer() ?? '');
        }

        return $this;
    }

    private function fillEmployees(bool $left = true): self
    {
        $medicForm = $this->data->getMedicForm();
        if ($medicForm) {
            $this->sheet->setCellValue($left ? 'Z45' : 'DU45', $medicForm->getUsername() ?? '');
        }

        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue($left ? 'BW45' : 'FR45', $techForm->getUsername() ?? '');
        }

        $driver = $this->data->getDriver();
        if ($driver) {
            $this->sheet->setCellValue($left ? 'Z52' : 'DU52', $driver->getFio());
            $this->sheet->setCellValue($left ? 'Z55' : 'DU55', $driver->getFio());
        }

        return $this;
    }

    public function fillMedicStamp(bool $left = true): self
    {
        $stamp = null;
        if ($this->data->getMedicForm()) {
            $stamp = $this->data->getMedicForm()->getStamp();
        }

        if (! $stamp) {
            $this->sheet->getStyle($left ? 'M36:AU44' : 'DH36:EP44')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $medicStamp = $stamp->getReqName() . "\n";
        $medicStamp .= wordwrap($stamp->getLicense(), 55) . "\n";
        $medicStamp .= config('trip-ticket.print.stamps.medic.comment');

        $medicForm = $this->data->getMedicForm();

        $date = $this->getDateString($medicForm);

        $this->sheet->setCellValue($left ? 'N37' : 'DI37', $medicStamp . "\n" . $date);

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
        $drawing->setCoordinates($left ? 'B38' : 'CW38');
        $drawing->setWidth(55);
        $drawing->setHeight(55);
        $drawing->setWorksheet($this->sheet);

        return $this;
    }

    private function fillTechStamp(bool $left = true): self
    {
        $techStamp = config('trip-ticket.print.stamps.tech');

        $techForm = $this->data->getTechForm();

        if (! $techForm) {
            $this->sheet->getStyle($left ? 'BJ36:CR44' : 'FE36:GM44')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $date = $this->getDateString($techForm);

        $this->sheet->setCellValue($left ? 'BK37' : 'FF37', $techStamp . "\n\n" . $date);

        return $this;
    }

    private function fillLogisticMethod(bool $left = true): self
    {
        $value = $this->data->getTripTicket()->getLogisticsMethod()->value();

        switch (true) {
            case $value === LogisticsMethodEnum::URBAN:
                $this->sheet->setCellValue($left ? 'B19' : 'CW19', '+');
                break;
            case $value === LogisticsMethodEnum::SUBURBAN:
                $this->sheet->setCellValue($left ? 'B20' : 'CW20', '+');
                break;
            case $value === LogisticsMethodEnum::LONG_DISTANCE:
                $this->sheet->setCellValue($left ? 'B21' : 'CW21', '+');
                break;
            default:
        }

        return $this;
    }

    private function fillTransportationType(bool $left = true): self
    {
        $value = $this->data->getTripTicket()->getTransportationType()->value();

        switch (true) {
            case $value === TransportationTypeEnum::REGULAR:
                $this->sheet->setCellValue($left ? 'T19' : 'DO19', '+');
                break;
            case $value === TransportationTypeEnum::TAXI:
                $this->sheet->setCellValue($left ? 'T20' : 'DO20', '+');
                break;
            case $value === TransportationTypeEnum::CONTRACT:
                $this->sheet->setCellValue($left ? 'T21' : 'DO21', '+');
                break;
            case $value === TransportationTypeEnum::ORDER:
                $this->sheet->setCellValue($left ? 'BE19' : 'EZ19', '+');
                break;
            case $value === TransportationTypeEnum::SELF_NEEDS:
                $this->sheet->setCellValue($left ? 'BE20' : 'EZ20', '+');
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
