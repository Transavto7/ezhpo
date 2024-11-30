<?php

namespace App\Services\TripTicketExporter\Mappers;

use App\Enums\TripTicketTemplateEnum;
use App\Models\TripTicket;
use App\Services\TripTicketExporter\ViewModels\ExportedItem;
use DomainException;

final class ItemMapperStrategy
{
    /**
     * @var TripTicket
     */
    private $model;
    /**
     * @var ItemMapperInterface
     */
    private $mapper;

    public function __construct(TripTicket $tripTicket)
    {
        $this->model = $tripTicket;

        switch (true) {
            case $tripTicket->template_code === TripTicketTemplateEnum::S4:
                $this->mapper = new ItemMapper4S();
                break;
            default:
                throw new DomainException('Unsupported trip ticket template code' . $tripTicket->template_code);
        }
    }

    public function map(): ExportedItem
    {
        return $this->mapper->fromEloquent($this->model);
    }
}
