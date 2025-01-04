<?php

namespace App\Services\TripTicketExporter\Mappers;

use App\Models\TripTicket;
use App\Services\TripTicketExporter\ViewModels\ExportedItem;

interface ItemMapperInterface
{
    public function fromEloquent(TripTicket $tripTicket): ExportedItem;
}
