<?php

namespace App\ViewModels\AnketaVerificationDetails;

use Carbon\Carbon;

final class AnketaVerificationHistoryItem implements \JsonSerializable
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

    public function jsonSerialize(): array
    {
        return [
            'date' => $this->date->format('d.m.Y H:i:s'),
            'isCurrentDevice' => $this->isCurrentDevice,
        ];
    }
}
