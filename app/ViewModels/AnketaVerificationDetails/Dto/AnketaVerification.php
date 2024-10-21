<?php

namespace App\ViewModels\AnketaVerificationDetails\Dto;

use Carbon\Carbon;

final class AnketaVerification
{
    /**
     * @var Carbon
     */
    private $date;
    /**
     * @var bool
     */
    private $isCurrentDevice;

    /**
     * @param Carbon $date
     * @param bool $isCurrentDevice
     */
    public function __construct(Carbon $date, bool $isCurrentDevice)
    {
        $this->date = $date;
        $this->isCurrentDevice = $isCurrentDevice;
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function isCurrentDevice(): bool
    {
        return $this->isCurrentDevice;
    }
}
