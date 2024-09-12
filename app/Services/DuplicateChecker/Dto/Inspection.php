<?php

namespace App\Services\DuplicateChecker\Dto;

use DateTimeImmutable;

class Inspection
{
    /**
     * @var string
     */
    protected $driverId;

    /**
     * @var DateTimeImmutable
     */
    protected $date;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $formType;

    /**
     * @param string $driverId
     * @param DateTimeImmutable $date
     * @param string $type
     * @param string $formType
     */
    public function __construct(string $driverId, DateTimeImmutable $date, string $type, string $formType)
    {
        $this->driverId = $driverId;
        $this->date = $date;
        $this->type = $type;
        $this->formType = $formType;
    }

    public function getDriverId(): string
    {
        return $this->driverId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFormType(): string
    {
        return $this->formType;
    }
}
