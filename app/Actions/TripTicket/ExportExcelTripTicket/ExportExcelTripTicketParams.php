<?php

namespace App\Actions\TripTicket\ExportExcelTripTicket;

use App\ValueObjects\EntityId;

class ExportExcelTripTicketParams
{
    /**
     * @var EntityId
     */
    private $uuid;

    /**
     * @param EntityId $uuid
     */
    public function __construct(EntityId $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): EntityId
    {
        return $this->uuid;
    }


}
