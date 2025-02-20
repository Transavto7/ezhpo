<?php

namespace App\Actions\TripTicket\UpdateTripTicket;


use App\Models\TripTicket;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

final class UpdateTripTicketHandler
{
    public function handle(UpdateTripTicketAction $action): TripTicket
    {
        if ($action->getExternalNumber() && $this->findSimilar($action->getExternalNumber(), $action->getTripTicket())) {
            throw new Exception("Путевой лист с номером {$action->getExternalNumber()} уже существует");
        }

        $action->getTripTicket()->update([
            'start_date' => $action->getStartDate(),
            'validity_period' => $action->getValidityPeriod(),
            'external_number' => $action->getExternalNumber(),
            'driver_id' => $action->getTripTicket()->driver_id ?: $action->getDriverId(),
            'car_id' => $action->getTripTicket()->car_id ?: $action->getCarId(),
            'logistics_method' => $action->getLogisticsMethod(),
            'transportation_type' => $action->getTransportationType(),
            'template_code' => $action->getTemplateCode(),
        ]);

        return $action->getTripTicket();
    }

    private function findSimilar(string $number, TripTicket $tripTicket): bool
    {
        $similar = TripTicket::withTrashed()
            ->where(function (Builder $query) use ($number) {
                $query->where('ticket_number', '=', $number)
                    ->orWhere('external_number', '=', $number);
            })
            ->where('company_id', '=', $tripTicket->company_id)
            ->where('id', '!=', $tripTicket->id)
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->first();

        return $similar !== null;
    }
}
