<?php

namespace App\Actions\TripTicket\UpdateTripTicketForms;

use App\Models\Forms\Form;
use App\Models\TripTicket;

final class UpdateTripTicketFormsAction
{
    /**
     * @var TripTicket
     */
    private $tripTicket;
    /**
     * @var Form|null
     */
    private $medicFormId;
    /**
     * @var Form|null
     */
    private $techFormId;

    /**
     * @param TripTicket $tripTicket
     * @param Form|null $medicFormId
     * @param Form|null $techFormId
     */
    public function __construct(TripTicket $tripTicket, ?Form $medicFormId, ?Form $techFormId)
    {
        $this->tripTicket = $tripTicket;
        $this->medicFormId = $medicFormId;
        $this->techFormId = $techFormId;
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getTechFormId(): ?Form
    {
        return $this->techFormId;
    }

    public function getMedicFormId(): ?Form
    {
        return $this->medicFormId;
    }
}
