<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class InspectionFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected array $payloadData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $payloadData)
    {
        $a = new class() {};
        $this->payloadData = $payloadData;
    }
}
