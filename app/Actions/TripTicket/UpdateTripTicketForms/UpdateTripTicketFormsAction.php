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
    private $medicForm;
    /**
     * @var Form|null
     */
    private $techForm;

    /**
     * @param TripTicket $tripTicket
     * @param Form|null $medicForm
     * @param Form|null $techForm
     */
    public function __construct(TripTicket $tripTicket, ?Form $medicForm, ?Form $techForm)
    {
        $this->tripTicket = $tripTicket;
        $this->medicForm = $medicForm;
        $this->techForm = $techForm;
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getTechForm(): ?Form
    {
        return $this->techForm;
    }

    public function getMedicForm(): ?Form
    {
        return $this->medicForm;
    }
}
