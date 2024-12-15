<?php

namespace App\Actions\TripTicket\UpdateTripTicketForm;

use App\Models\Forms\Form;
use App\Models\TripTicket;

final class UpdateTripTicketFormAction
{
    /**
     * @var TripTicket
     */
    private $tripTicket;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var Form
     */
    private $form;

    /**
     * @param TripTicket $tripTicket
     * @param string $formType
     * @param Form $form
     */
    public function __construct(TripTicket $tripTicket, string $formType, Form $form)
    {
        $this->tripTicket = $tripTicket;
        $this->formType = $formType;
        $this->form = $form;
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function getForm(): Form
    {
        return $this->form;
    }
}
