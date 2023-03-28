<?php

namespace App\Listeners;

use App\Events\InspectionFailed;
use App\Services\Contracts\SmsServiceInterface;
use App\Services\SmsRuService;

class SmsNotification
{
    /**
     * @var SmsRuService
     */
    private SmsServiceInterface $smsService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event instanceof InspectionFailed) {
           $this->smsService->multi[$event->payloadData->company->where_call] = $event->payloadData->smsMessage;
        }
    }
}
