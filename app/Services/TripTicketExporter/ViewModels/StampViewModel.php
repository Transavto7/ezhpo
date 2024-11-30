<?php

namespace App\Services\TripTicketExporter\ViewModels;

class StampViewModel
{
    /**
     * @var string
     */
    private $reqName;

    /**
     * @var string
     */
    private $license;

    /**
     * @param string $reqName
     * @param string $license
     */
    public function __construct(string $reqName, string $license)
    {
        $this->reqName = $reqName;
        $this->license = $license;
    }

    /**
     * @return string
     */
    public function getReqName(): string
    {
        return $this->reqName;
    }

    /**
     * @return string
     */
    public function getLicense(): string
    {
        return $this->license;
    }
}
