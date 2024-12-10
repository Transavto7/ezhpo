<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Actions\TripTicket\TripTicketNumberGenerator;
use App\Models\TripTicket;
use Exception;

final class StoreTripTicketHandler extends TripTicketNumberGenerator
{
    /**
     * @throws Exception
     */
    public function handle(StoreTripTicketAction $action): array
    {
        $dates = $action->getAdditionalDates() ?: [];

        if (! in_array($action->getStartDate(), $dates)) {
            $dates[] = $action->getStartDate();
        }

        if ($action->getTicketNumber() && $this->findSimilar($action->getTicketNumber())) {
            throw new Exception("Путевой лист с номером {$action->getTicketNumber()} уже существует");
        }

        $first = false;
        $tripTickets = [];
        foreach ($dates as $date) {
            $tripTicket = TripTicket::create([
                'ticket_number' => $action->getTicketNumber() && ! $first
                    ? $action->getTicketNumber()
                    : $this->nextTicketNumber(),
                'company_id' => $action->getCompanyId(),
                'start_date' => $date,
                'validity_period' => $action->getValidityPeriod(),
                'driver_id' => $action->getDriverId(),
                'car_id' => $action->getCarId(),
                'logistics_method' => $action->getLogisticsMethod(),
                'transportation_type' => $action->getTransportationType(),
                'template_code' => $action->getTemplateCode(),
            ]);

            $first = true;
            $tripTickets[] = $tripTicket;
        }

        return ['created' => $tripTickets];
    }

    private function findSimilar(string $number): bool
    {
        $similar = TripTicket::withTrashed()
            ->where( 'ticket_number', '=', $number)
            ->first();

        return $similar !== null;
    }
}
