<?php

namespace App\Listeners;

use App\Services\SmsService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SmsNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SmsService $smsService)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if(isset($Driver)) {
            $sms->sms($Company->where_call, Settings::setting('sms_text_driver') . " $Driver->fio. $phone_to_call");
        } else if (isset($Car)) {
            $sms->sms($Company->where_call, Settings::setting('sms_text_car') . " $Car->gos_number. $phone_to_call");
        } else {
            $sms->sms($Company->where_call, Settings::setting('sms_text_default') . ' ' . new Anketa($anketa) . '.' . ' ' . $phone_to_call);
        }
    }
}
