<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Drivers\ImportObjects;

use App\Actions\Element\Import\Core\ErrorObject;

final class ErrorDriver implements ErrorObject
{
    /** @var string|null */
    private $companyInn;

    /** @var string|null */
    private $fullName;

    /** @var string|null */
    private $birthday;

    /** @var string|null */
    private $companyName;

    /** @var string|null */
    private $gender;

    /** @var string|null */
    private $phone;

    /** @var string|null */
    private $snils;

    /** @var string|null */
    private $license;

    /** @var string|null */
    private $licenseIssuedAt;

    /** @var string */
    private $description;

    /**
     * @param string|null $companyInn
     * @param string|null $fullName
     * @param string|null $birthday
     * @param string|null $companyName
     * @param string|null $gender
     * @param string|null $phone
     * @param string|null $snils
     * @param string|null $license
     * @param string|null $licenseIssuedAt
     * @param string $description
     */
    public function __construct(
        $companyInn,
        $fullName,
        $birthday,
        $companyName,
        $gender,
        $phone,
        $snils,
        $license,
        $licenseIssuedAt,
        string  $description
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
        $this->description = $description;
    }

    public function toArray(): array
    {
        return [
            $this->companyInn,
            $this->fullName,
            $this->birthday,
            $this->companyName,
            $this->gender,
            $this->phone,
            $this->snils,
            $this->license,
            $this->licenseIssuedAt,
            $this->description
        ];
    }

    public static function fromImportedDriver(ImportedDriver $importedDriver, $reason): self
    {
        return new self(
            $importedDriver->getCompanyInn(),
            $importedDriver->getFullName(),
            $importedDriver->getBirthday()->format('d.m.Y'),
            $importedDriver->getCompanyName(),
            optional($importedDriver->getGender())->value(),
            $importedDriver->getPhone(),
            $importedDriver->getSnils(),
            $importedDriver->getLicense(),
            optional($importedDriver->getLicenseIssuedAt())->format('d.m.Y'),
            $reason
        );
    }
}
