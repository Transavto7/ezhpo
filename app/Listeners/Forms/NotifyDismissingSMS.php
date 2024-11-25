<?php

namespace App\Listeners\Forms;

use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Events\Forms\DriverDismissed;
use App\Models\Forms\MedicForm;
use App\Services\Notifier\SMSNotifierService;
use App\Settings;

class NotifyDismissingSMS
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
     * @param DriverDismissed $event
     * @return void
     */
    public function handle(DriverDismissed $event)
    {
        $form = $event->getForm();

        /** @var Driver|null $driver */
        $driver = $form->driver;
        if ($driver === null) {
            return;
        }

        /** @var Company|null $company */
        $company = $driver->company;
        if ($company === null) {
            return;
        }

        /** @var string|mixed $whereCall */
        $whereCall = $company->where_call;
        if (empty($whereCall)) {
            return;
        }

        $phoneToCall = Settings::setting('sms_text_phone');
        if ($form->type_anketa === FormTypeEnum::MEDIC) {
            $message = Settings::setting('sms_text_driver') . " $driver->fio. $phoneToCall";
        } else if ($form->type_anketa === FormTypeEnum::TECH) {
            $details = $form->details;
            if ($details === null) {
                return;
            }

            $car = $details->car;
            if ($car === null) {
                return;
            }

            $message = Settings::setting('sms_text_car') . " $car->gos_number. $phoneToCall";
        } else {
            return;
        }

        (new SMSNotifierService())->notify($whereCall, $message);
    }
}
