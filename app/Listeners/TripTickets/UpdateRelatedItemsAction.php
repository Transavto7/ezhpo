<?php

namespace App\Listeners\TripTickets;

use App\Enums\FormTypeEnum;
use App\Events\TripTickets\UpdateRelatedItems;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\Models\Forms\TechForm;
use App\Models\TripTicket;
use Carbon\Carbon;

class UpdateRelatedItemsAction
{
    public function __construct()
    {
    }

    public function handle(UpdateRelatedItems $event)
    {
        $updatedItem = $event->getUpdatedItem();

        if ($updatedItem instanceof TripTicket) {
            $date = $updatedItem->start_date ?: null;
            $period = $updatedItem->period_pl;
            $driverId = $updatedItem->driver_id;
            $carId = $updatedItem->car_id;
            $medicForm = $updatedItem->medicForm;
            $techForm = $updatedItem->techForm;

            if ($medicForm) {
                $this->updateMedicForm($medicForm, $date, $period, $driverId);
            }

            if ($techForm) {
                $this->updateTechForm($techForm, $date, $period, $driverId, $carId);
            }
        }

        if ($updatedItem instanceof Form) {
            $details = $updatedItem->details;
            $date = $updatedItem->date;
            $period = $details->period_pl;
            $driverId = $updatedItem->driver_id;

            if ($updatedItem->type_anketa === FormTypeEnum::MEDIC) {
                $carId = null;

                $tripTicket = $updatedItem->tripTicketMedic;
                $techForm = $tripTicket ? $tripTicket->techForm : null;

                if ($techForm) {
                    $this->updateTechForm($techForm, $date, $period, $driverId, $carId);
                }
            } else {
                $carId = $details->car_id;

                $tripTicket = $updatedItem->tripTicketTech;
                $medicForm = $tripTicket ? $tripTicket->medicForm : null;

                if ($medicForm) {
                    $this->updateMedicForm($medicForm, $date, $period, $driverId);
                }
            }

            if ($tripTicket) {
                $this->updateTripTicket($tripTicket, $date, $period, $driverId, $carId);
            }
        }
    }

    private function updateMedicForm(Form $form, ?string $date, ?string $period, ?string $driverId)
    {
        /** @var MedicForm $details */
        $details = $form->details;
        $dataToUpdate = [];
        if ($date && $this->isDateChange($form->date, $date)) {
            $dataToUpdate['date'] = $date;
        }

        if ($period && $this->isPeriodChange($details->period_pl, $period)) {
            $dataToUpdate['period_pl'] = $period;
        }

        if ($driverId && $this->isIdChange($form->driver_id, $driverId)) {
            $dataToUpdate['driver_id'] = $driverId;
        }

        if (! empty($dataToUpdate)) {
            $form->update($dataToUpdate);
            $form->details->update($dataToUpdate);
        }
    }

    private function updateTechForm(Form $form, ?string $date, ?string $period, ?string $driverId, ?string $carId)
    {
        /** @var TechForm $details */
        $details = $form->details;
        $dataToUpdate = [];
        if ($date && $this->isDateChange($form->date, $date)) {
            $dataToUpdate['date'] = $date;
        }

        if ($period && $this->isPeriodChange($details->period_pl, $period)) {
            $dataToUpdate['period_pl'] = $period;
        }

        if ($driverId && $this->isIdChange($form->driver_id, $driverId)) {
            $dataToUpdate['driver_id'] = $driverId;
        }

        if ($carId && $this->isIdChange($details->car_id, $carId)) {
            $dataToUpdate['car_id'] = $carId;
        }

        if (! empty($dataToUpdate)) {
            $form->update($dataToUpdate);
            $form->details->update($dataToUpdate);
        }
    }

    private function updateTripTicket(TripTicket $tripTicket, ?string $date, ?string $period, ?string $driverId, ?string $carId)
    {
        $dataToUpdate = [];
        if ($date && $this->isDateChange($tripTicket->start_date, $date)) {
            $dataToUpdate['date'] = $date;
        }

        if ($period && $this->isPeriodChange($tripTicket->period_pl, $period)) {
            $dataToUpdate['period_pl'] = $period;
        }

        if ($driverId && $this->isIdChange($tripTicket->driver_id, $driverId)) {
            $dataToUpdate['driver_id'] = $driverId;
        }

        if ($carId && $this->isIdChange($tripTicket->car_id, $carId)) {
            $dataToUpdate['car_id'] = $carId;
        }

        if (! empty($dataToUpdate)) {
            $tripTicket->update($dataToUpdate);
        }
    }

    private function isDateChange($oldDate, $newDate): bool
    {
        if ($oldDate === null && $newDate) {
            return true;
        }

        if ($oldDate && Carbon::parse($oldDate)->format('Y-m-d') !== Carbon::parse($newDate)->format('Y-m-d')) {
            return true;
        }

        return false;
    }

    private function isPeriodChange($oldPeriod, $newPeriod): bool
    {
        if ($oldPeriod === null && $newPeriod) {
            return true;
        }

        if ($oldPeriod && Carbon::parse($oldPeriod)->format('Y-m') !== Carbon::parse($newPeriod)->format('Y-m')) {
            return true;
        }

        return false;
    }

    private function isIdChange($oldId, $newId): bool
    {
        if ($oldId === null && $newId) {
            return true;
        }

        if ($oldId && $oldId !== $newId) {
            return true;
        }

        return false;
    }
}
