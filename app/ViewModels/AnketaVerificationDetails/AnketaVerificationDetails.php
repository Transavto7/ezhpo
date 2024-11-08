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
    private $anketaPeriod;
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
     * @param Carbon|null $anketaPeriod
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
        ?Carbon $anketaPeriod,
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
        $this->anketaPeriod = $anketaPeriod;
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

    public function getAnketaPeriod(): ?Carbon
    {
        return $this->anketaPeriod;
    }

    public function getFormattedAnketaPeriod(): ?string
    {
        if (!$this->anketaPeriod) {
            return '';
        }

        $months = [
            '1' => 'Январь',
            '2' => 'Февраль',
            '3' => 'Март',
            '4' => 'Апрель',
            '5' => 'Май',
            '6' => 'Июнь',
            '7' => 'Июль',
            '8' => 'Август',
            '9' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь',
        ];

        if (array_key_exists($this->anketaPeriod->month, $months)) {
            return $months[$this->anketaPeriod->month] . ' ' . $this->anketaPeriod->year;
        }

        return '';
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
