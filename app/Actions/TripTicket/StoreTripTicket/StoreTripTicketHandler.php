<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Actions\TripTicket\TripTicketNumberGenerator;
use App\Models\TripTicket;
use Carbon\Carbon;
use Exception;

final class StoreTripTicketHandler extends TripTicketNumberGenerator
{
    /**
     * @throws Exception
     */
    public function handle(StoreTripTicketAction $action): array
    {
        $tripTickets = [];

        foreach ($action->getItems() as $item) {
            if ($item->getTicketNumber() && $this->findSimilar($item->getTicketNumber(), $action->getCompanyId())) {
                throw new Exception("Путевой лист с номером {$item->getTicketNumber()} уже существует");
            }

            $tripTickets[] = TripTicket::create([
                'ticket_number' => $item->getTicketNumber() ?: $this->nextTicketNumber($action->getCompanyId()),
                'company_id' => $action->getCompanyId(),
                'start_date' => $item->getStartDate(),
                'validity_period' => $item->getValidityPeriod(),
                'driver_id' => $action->getDriverId(),
                'car_id' => $action->getCarId(),
                'logistics_method' => $item->getLogisticsMethod(),
                'transportation_type' => $item->getTransportationType(),
                'template_code' => $item->getTemplateCode(),
            ]);
        }

        return $tripTickets;
    }

    private function findSimilar(string $number, string $companyId): bool
    {
        $similar = TripTicket::withTrashed()
            ->where('ticket_number', '=', $number)
            ->where('company_id', '=', $companyId)
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->first();

        return $similar !== null;
    }
}
