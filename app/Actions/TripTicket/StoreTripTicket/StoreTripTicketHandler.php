<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Models\TripTicket;
use Carbon\Carbon;

final class StoreTripTicketHandler
{
    public function handle(StoreTripTicketAction $action): array
    {
        $dates = $action->getAdditionalDates() ?: [];

        if (! in_array($action->getStartDate(), $dates)) {
            $dates[] = $action->getStartDate();
        }

        $number = $this->getNumber($action);
        $tripTickets = [];
        foreach ($dates as $date) {
            $nextNumber = $this->nextNumber($number, $date);

            $tripTicket = TripTicket::create([
                'ticket_number' => $nextNumber,
                'company_id' => $action->getCompanyId(),
                'start_date' => $date,
                'validity_period' => $action->getValidityPeriod(),
                'driver_id' => $action->getDriverId(),
                'car_id' => $action->getCarId(),
                'logistics_method' => $action->getLogisticsMethod(),
                'transportation_type' => $action->getTransportationType(),
                'template_code' => $action->getTemplateCode(),
            ]);

            $tripTickets[] = $tripTicket;
        }

        return ['created' => $tripTickets];
    }

    private function findSimilar(string $number): bool
    {
        $similar = TripTicket::query()
            ->where( 'ticket_number', '=', $number)
            ->whereNull('deleted_at')
            ->first();

        return $similar !== null;
    }

    private function getNumber(StoreTripTicketAction $action): string
    {
        if ($action->getTicketNumber() !== null) {
            return $action->getTicketNumber();
        }

        if ($action->getCarId() !== null) {
            return $action->getCarId();
        }

        return $action->getCompanyId();
    }

    private function nextNumber(string $number, string $date): string
    {
        $date = Carbon::parse($date)->format('d.m.Y');
        $nextNumber = $number.'-'.$date;

        $ctr = 2;
        while ($this->findSimilar($nextNumber)) {
            $nextNumber = $number.'-'.$date.'/'.$ctr;
            $ctr++;
        }

        return $nextNumber;
    }
}
