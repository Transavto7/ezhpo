<?php

namespace App\Services\TripTicketExporter\ValueObjects;

use App\Driver as DriverModel;
use Carbon\Carbon;

class Driver
{
    /**
     * @var string
     */
    private $fio;
    /**
     * @var string|null
     */
    private $driverLicense;
    /**
     * @var Carbon|null
     */
    private $driverLicenseDate;
    /**
     * @var string|null
     */
    private $snils;

    /**
     * @param string $fio
     * @param string|null $driverLicense
     * @param Carbon|null $driverLicenseDate
     * @param string|null $snils
     */
    private function __construct(
        string  $fio,
        ?string $driverLicense,
        ?Carbon $driverLicenseDate,
        ?string $snils
    )
    {
        $this->fio = $fio;
        $this->driverLicense = $driverLicense;
        $this->driverLicenseDate = $driverLicenseDate;
        $this->snils = $snils;
    }

    public static function fromEloquent(DriverModel $driver): self
    {
        $driverLicenseData = null;
        if ($driver->date_driver_license) {
            $driverLicenseData = Carbon::parse($driver->date_driver_license);
        }

        return new self(
            $driver->fio,
            $driver->driver_license,
            $driverLicenseData,
            $driver->snils
        );
    }

    public function getFio(): string
    {
        return $this->fio;
    }

    public function getDriverLicense(): ?string
    {
        return $this->driverLicense;
    }

    public function getDriverLicenseDate(): ?Carbon
    {
        return $this->driverLicenseDate;
    }

    public function getSnils(): ?string
    {
        return $this->snils;
    }

}
