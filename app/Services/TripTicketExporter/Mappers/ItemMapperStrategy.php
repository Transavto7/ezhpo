<?php

namespace App\Services\TripTicketExporter\Mappers;

use App\Enums\TripTicket\TripTicketTemplateEnum;
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
            case $tripTicket->template_code === TripTicketTemplateEnum::_3:
                $this->mapper = new ItemMapper3();
                break;
            case $tripTicket->template_code === TripTicketTemplateEnum::_4P:
                $this->mapper = new ItemMapper4P();
                break;
            case $tripTicket->template_code === TripTicketTemplateEnum::PG1:
                $this->mapper = new ItemMapperPG1();
                break;
            case $tripTicket->template_code === TripTicketTemplateEnum::_6C:
                $this->mapper = new ItemMapper6C();
                break;
            case $tripTicket->template_code === TripTicketTemplateEnum::_3C:
                $this->mapper = new ItemMapper3C();
                break;
            case $tripTicket->template_code === TripTicketTemplateEnum::_4O:
                $this->mapper = new ItemMapper4O();
                break;
            case $tripTicket->template_code === TripTicketTemplateEnum::ECM2:
                $this->mapper = new ItemMapperECM2();
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
