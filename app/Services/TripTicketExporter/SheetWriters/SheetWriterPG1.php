<?php

namespace App\Services\TripTicketExporter\SheetWriters;

use App\Enums\TripTicket\LogisticsMethodEnum;
use App\Enums\TripTicket\TransportationTypeEnum;
use App\Services\QRCode\QRCodeGeneratorInterface;
use App\Services\TripTicketExporter\ViewModels\ExportedItem;
use App\Services\TripTicketExporter\ViewModels\ExportedItemPG1;
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

final class SheetWriterPG1 implements SheetWriterInterface
{
    /**
     * @var Worksheet
     */
    private $sheet;
    /**
     * @var ExportedItemPG1
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
        return config('trip-ticket.print.pg1.template.front.sheet');
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param ExportedItemPG1 $item
     * @param int $number
     * @return Spreadsheet
     * @throws Exception
     */
    public function createSheet(Spreadsheet $spreadsheet, ExportedItem $item, int $number): Spreadsheet
    {
        $this->sheet = clone $spreadsheet->getSheetByName($this->templateSheetName());

        $title = $number . '. ' . config('trip-ticket.print.pg1.template.front.prefix');

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

        $this->sheet->setCellValue('BB1', $value);

        return $this;
    }

    private function fillTripTicketNumber(): self
    {
        $number = $this->data->getTripTicket()->getTicketNumber();
        $externalNumber = $this->data->getTripTicket()->getExternalTicketNumber();

        $this->sheet->setCellValue('AC1', $externalNumber ? $externalNumber." ($number)": $number);

        return $this;
    }

    private function fillPeriod(): self
    {
        if ($this->data->getTripTicket()->getStartDate()) {
            $period = $this->data->getTripTicket()->getValidityPeriod();
            $startDate = $this->data->getTripTicket()->getStartDate();
            $endDate = $startDate->copy()->addDays($period - 1);

            $this->sheet->setCellValue('U3', $startDate->day);
            $this->sheet->setCellValue('BA3', $endDate->day);
        } else {
            $startDate = $this->data->getTripTicket()->getPeriodPl();
            $endDate = $startDate;

            $this->sheet->setCellValue('U3', null);
            $this->sheet->setCellValue('BA3', null);
        }

        $this->sheet->setCellValue('Z3', trans('date.months_genitive.' . $startDate->month));
        $this->sheet->setCellValue('AL3', $startDate->year);

        $this->sheet->setCellValue('BF3', trans('date.months_genitive.' . $endDate->month));
        $this->sheet->setCellValue('BS3', $endDate->year);

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

        $this->sheet->setCellValue('K7', $value);

        return $this;
    }

    private function fillCar(): self
    {
        $car = $this->data->getCar();

        if (!$car) {
            return $this;
        }

        $this->sheet->setCellValue('K14', $car->getTypeAuto() . ', ' . $car->getMarkModel());
        $this->sheet->setCellValue('AW14', $car->getGosNumber());

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

        $this->sheet->setCellValue('BO5', $driver->getFio());
        $this->sheet->setCellValue('BO7', $driverLicense);
        $this->sheet->setCellValue('BO9', $driver->getSnils() ?? '');

        return $this;
    }

    private function fillOdometer(): self
    {
        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('BK30', $techForm->getOdometer() ?? '');
        }

        return $this;
    }

    private function fillEmployees(): self
    {
        $medicForm = $this->data->getMedicForm();
        if ($medicForm) {
            $this->sheet->setCellValue('Z26', $medicForm->getUsername() ?? '');
        }

        $techForm = $this->data->getTechForm();
        if ($techForm) {
            $this->sheet->setCellValue('BX27', $techForm->getUsername() ?? '');
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
            $this->sheet->getStyle('R18:AU25')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
            $this->sheet->getStyle('R18:AU25')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $medicStamp = $stamp->getReqName() . "\n";
        $medicStamp .= wordwrap($stamp->getLicense(), 55) . "\n";
        $medicStamp .= config('trip-ticket.print.stamps.medic.comment');

        $medicForm = $this->data->getMedicForm();

        $date = $this->getDateString($medicForm);

        $this->sheet->setCellValue('S19', $medicStamp . "\n" . $date);

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
        $drawing->setCoordinates('D19');
        $drawing->setWidth(60);
        $drawing->setHeight(60);
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
            $this->sheet->getStyle('BP18:CS23')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_NONE);
            $this->sheet->getStyle('BP18:CS23')->getFill()->setFillType(Fill::FILL_NONE);

            return $this;
        }

        $date = $this->getDateString($techForm);

        $this->sheet->setCellValue('BQ19', $techStamp . "\n\n" . $date);

        return $this;
    }

    private function fillLogisticMethod(): self
    {
        $value = $this->data->getTripTicket()->getLogisticsMethod()->value();

        $this->sheet->setCellValue('K11', LogisticsMethodEnum::getLabel($value));

        return $this;
    }

    private function fillTransportationType(): self
    {
        $value = $this->data->getTripTicket()->getTransportationType()->value();

        $this->sheet->setCellValue('BM11', TransportationTypeEnum::getLabel($value));

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
