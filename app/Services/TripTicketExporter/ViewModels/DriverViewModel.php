<?php

namespace App\Services\TripTicketExporter\ViewModels;

use Carbon\Carbon;

final class DriverViewModel
{
    /**
     * @var string
     */
    private $id;
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
     * @param string $id
     * @param string $fio
     * @param string|null $driverLicense
     * @param Carbon|null $driverLicenseDate
     * @param string|null $snils
     */
    public function __construct(
        string  $id,
        string  $fio,
        ?string $driverLicense,
        ?Carbon $driverLicenseDate,
        ?string $snils
    )
    {
        $this->id = $id;
        $this->fio = $fio;
        $this->driverLicense = $driverLicense;
        $this->driverLicenseDate = $driverLicenseDate;
        $this->snils = $snils;
    }

    public function getId(): string
    {
        return $this->id;
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
