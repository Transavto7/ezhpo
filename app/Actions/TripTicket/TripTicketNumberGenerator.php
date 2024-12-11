<?php

namespace App\Actions\TripTicket;

use App\Services\TicketNumberGenerator\TicketNumberGenerator;
use Exception;

abstract class TripTicketNumberGenerator
{
    /**
     * @var TicketNumberGenerator
     */
    private $generator;

    /**
     * @param TicketNumberGenerator $generator
     */
    public function __construct(TicketNumberGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @throws Exception
     */
    public function nextTicketNumber(string $companyId): int
    {
        return $this->generator->generateWithSettings($companyId);
    }
}
