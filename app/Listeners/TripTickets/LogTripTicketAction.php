<?php

namespace App\Listeners\TripTickets;

use App\Enums\TripTicket\TripTicketActionType;
use App\Events\TripTickets\LogTripTicket;
use App\Models\TripTicketLog;

class LogTripTicketAction
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LogTripTicket $event
     * @return void
     */
    public function handle(LogTripTicket $event)
    {
        $logData = $this->logModel($event->getTripTicket());

        if (count($logData) === 0 && ! $event->getType()->equal(TripTicketActionType::changeStatus())) {
            return;
        }

        TripTicketLog::create([
            'trip_ticket_id' => $event->getTripTicket()->uuid,
            'user_id' => $event->getUser()->id,
            'type' => $event->getType(),
            'payload' => $logData,
        ]);
    }

    private function logModel($tripTicket): array
    {
        $logData = [];

        foreach ($tripTicket->getDirty() as $attribute => $newValue) {
            if (empty($newValue) && empty($tripTicket->getOriginal($attribute))) {
                continue;
            }

            $logData[] = [
                'name' => $attribute,
                'oldValue' => $tripTicket->getOriginal($attribute),
                'newValue' => $newValue
            ];
        }

        return $logData;
    }
}
