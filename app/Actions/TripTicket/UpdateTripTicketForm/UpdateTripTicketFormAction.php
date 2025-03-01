<?php

namespace App\Actions\TripTicket\UpdateTripTicketForm;

use App\Models\Forms\Form;
use App\Models\TripTicket;
use DateTimeImmutable;
use Exception;

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
     * @var DateTimeImmutable|null
     */
    private $startDate;

    /**
     * @param TripTicket $tripTicket
     * @param Form $form
     * @throws Exception
     */
    public function __construct(TripTicket $tripTicket, Form $form)
    {
        $this->tripTicket = $tripTicket;
        $this->startDate = new DateTimeImmutable($form->date);
        $this->formType = $form->getAttribute('type_anketa');
        $this->form = $form;
        $this->driverId = $form->getAttribute('driver_id');
        $this->carId = $form->details->getAttribute('car_id');
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
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
