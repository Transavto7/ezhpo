<?php

namespace App\ViewModels\AnketaVerificationDetails;

use App\Enums\AnketLabelingType;
use Carbon\Carbon;

final class AnketaVerificationDetails
{
    /**
     * @var bool
     */
    private $verified;
    /**
     * @var string
     */
    private $anketaUuid;
    /**
     * @var string
     */
    private $anketaId;
    /**
     * @var AnketLabelingType
     */
    private $anketaType;
    /**
     * @var string|null
     */
    private $anketaNumber;
    /**
     * @var string|null
     */
    private $companyName;
    /**
     * @var Carbon|null
     */
    private $anketaDate;
    /**
     * @var string|null
     */
    private $driverName;
    /**
     * @var string|null
     */
    private $carGosNumber;

    /**
     * @param bool $verified
     * @param string $anketaUuid
     * @param string $anketaId
     * @param AnketLabelingType $anketaType
     * @param string|null $anketaNumber
     * @param string|null $companyName
     * @param Carbon|null $anketaDate
     * @param string|null $driverName
     * @param string|null $carGosNumber
     */
    public function __construct(
        bool $verified,
        string $anketaUuid,
        string $anketaId,
        AnketLabelingType $anketaType,
        ?string $anketaNumber,
        ?string $companyName,
        ?Carbon $anketaDate,
        ?string $driverName,
        ?string $carGosNumber
    )
    {
        $this->verified = $verified;
        $this->anketaUuid = $anketaUuid;
        $this->anketaId = $anketaId;
        $this->anketaType = $anketaType;
        $this->anketaNumber = $anketaNumber;
        $this->companyName = $companyName;
        $this->anketaDate = $anketaDate;
        $this->driverName = $driverName;
        $this->carGosNumber = $carGosNumber;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function getAnketaUuid(): string
    {
        return $this->anketaUuid;
    }

    public function getAnketaId(): string
    {
        return $this->anketaId;
    }

    public function getAnketaType(): AnketLabelingType
    {
        return $this->anketaType;
    }

    public function getAnketaNumber(): ?string
    {
        return $this->anketaNumber;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getAnketaDate(): ?Carbon
    {
        return $this->anketaDate;
    }

    public function getDriverName(): ?string
    {
        return $this->driverName;
    }

    public function getCarGosNumber(): ?string
    {
        return $this->carGosNumber;
    }
}
