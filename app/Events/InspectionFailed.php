<?php

namespace App\Events;

use App\Dtos\InspectionFailedData;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class InspectionFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public InspectionFailedData $payloadData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(InspectionFailedData $payloadData)
    {
        $this->payloadData = $payloadData;
    }
}
