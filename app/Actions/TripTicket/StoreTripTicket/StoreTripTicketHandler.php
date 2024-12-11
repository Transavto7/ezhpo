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
        if ($action->getTicketNumber() && $this->findSimilar($action)) {
            throw new Exception("Путевой лист с номером {$action->getTicketNumber()} уже существует");
        }

        $tripTicket = TripTicket::create([
            'ticket_number' => $action->getTicketNumber() ?: $this->nextTicketNumber($action->getCompanyId()),
            'company_id' => $action->getCompanyId(),
            'start_date' => $action->getStartDate(),
            'validity_period' => $action->getValidityPeriod(),
            'driver_id' => $action->getDriverId(),
            'car_id' => $action->getCarId(),
            'logistics_method' => $action->getLogisticsMethod(),
            'transportation_type' => $action->getTransportationType(),
            'template_code' => $action->getTemplateCode(),
        ]);

        return [$tripTicket];
    }

    private function findSimilar(StoreTripTicketAction $action): bool
    {
        $similar = TripTicket::withTrashed()
            ->where('ticket_number', '=', $action->getTicketNumber())
            ->where('company_id', '=', $action->getCompanyId())
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->first();

        return $similar !== null;
    }
}
