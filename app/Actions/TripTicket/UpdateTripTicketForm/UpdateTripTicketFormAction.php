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
     * @var string|null
     */
    private $driverId;

    /**
     * @var string|null
     */
    private $carId;

    /**
     * @param TripTicket $tripTicket
     * @param string $formType
     * @param Form $form
     * @param string|null $driverId
     * @param string|null $carId
     */
    public function __construct(TripTicket $tripTicket, string $formType, Form $form, ?string $driverId, ?string $carId = null)
    {
        $this->tripTicket = $tripTicket;
        $this->formType = $formType;
        $this->form = $form;
        $this->driverId = $driverId;
        $this->carId = $carId;
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

    public function getDriverId(): ?string
    {
        return $this->driverId;
    }

    public function getCarId(): ?string
    {
        return $this->carId;
    }
}
