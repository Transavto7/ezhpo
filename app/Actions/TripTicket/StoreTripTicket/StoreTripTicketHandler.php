<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Actions\TripTicket\TripTicketNumberGenerator;
use App\Models\TripTicket;
use App\ValueObjects\EntityId;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

final class StoreTripTicketHandler extends TripTicketNumberGenerator
{
    /**
     * @throws Exception
     */
    public function handle(StoreTripTicketAction $action): array
    {
        $user = Auth::user();
        $tripTickets = [];

        foreach ($action->getItems() as $item) {
            if ($item->getTicketNumber() && $this->findSimilar($item->getTicketNumber(), $action->getCompanyId())) {
                throw new Exception("Путевой лист с номером {$item->getTicketNumber()} уже существует");
            }
            $id = EntityId::next()->getId();

            $tripTickets[] = TripTicket::create([
                'uuid' => $id,
                'ticket_number' => $item->getTicketNumber() ?: $this->getTicketNumber($id),
                'company_id' => $action->getCompanyId(),
                'start_date' => $item->getStartDate(),
                'validity_period' => $item->getValidityPeriod(),
                'driver_id' => $action->getDriverId(),
                'car_id' => $action->getCarId(),
                'logistics_method' => $item->getLogisticsMethod(),
                'transportation_type' => $item->getTransportationType(),
                'template_code' => $item->getTemplateCode(),
                'user_id' => $user->id,
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