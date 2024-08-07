<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Drivers\ImportObjects;

use App\ValueObjects\Gender;
use Carbon\Carbon;

final class ImportedDriver
{
    /** @var string */
    private $companyInn;

    /** @var string */
    private $fullName;

    /** @var Carbon */
    private $birthday;

    /** @var string|null */
    private $companyName;

    /** @var Gender|null */
    private $gender;

    /** @var string|null */
    private $phone;

    /** @var string|null */
    private $snils;

    /** @var string|null */
    private $license;

    /** @var Carbon|null */
    private $licenseIssuedAt;

    /**
     * @param string $companyInn
     * @param string $fullName
     * @param Carbon $birthday
     * @param string|null $companyName
     * @param Gender|null $gender
     * @param string|null $phone
     * @param string|null $snils
     * @param string|null $license
     * @param Carbon|null $licenseIssuedAt
     */
    public function __construct(
        string     $companyInn,
        string  $fullName,
        Carbon  $birthday,
        ?string $companyName,
        ?Gender $gender,
        ?string $phone,
        ?string $snils,
        ?string $license,
        ?Carbon $licenseIssuedAt
    )
    {
        $this->companyInn = $companyInn;
        $this->fullName = $fullName;
        $this->birthday = $birthday;
        $this->companyName = $companyName;
        $this->gender = $gender;
        $this->phone = $phone;
        $this->snils = $snils;
        $this->license = $license;
        $this->licenseIssuedAt = $licenseIssuedAt;
    }


    public function getCompanyInn(): string
    {
        return $this->companyInn;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getBirthday(): Carbon
    {
        return $this->birthday;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getSnils(): ?string
    {
        return $this->snils;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function getLicenseIssuedAt(): ?Carbon
    {
        return $this->licenseIssuedAt;
    }

    public function toArray(): array
    {
        return [
            'fio' => $this->getFullName(),
            'year_birthday' => $this->getBirthday()->format('Y-m-d'),
            'gender' => $this->getGender() ? $this->getGender()->value() : Gender::male()->value(),
            'phone' => $this->getPhone(),
            'snils' => $this->getSnils(),
            'driver_license' => $this->getLicense(),
            'driver_license_issued_at' => $this->getLicenseIssuedAt() ?
                $this->getLicenseIssuedAt()->format('Y-m-d')
                : null,
        ];
    }
}
