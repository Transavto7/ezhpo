<?php

namespace App\Listeners\Forms;

use App\Events\Forms\DriverDismissed;
use App\Models\Forms\MedicForm;
use App\Services\Notifier\TelegramNotifierService;
use App\ValueObjects\NotifyTelegramMessage;
use Illuminate\Support\Carbon;

class NotifyDismissingByAlkoTG
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
        $chatId = config('telegram.chats.dismissed');
        if (empty($chatId)) {
            return;
        }

        $form = $event->getForm();
        /** @var MedicForm|null $formDetails */
        $formDetails = $form->details;
        if ($formDetails === null) {
            return;
        }

        if (!($formDetails instanceof MedicForm)) {
            return;
        }

        if ($form->proba_alko !== 'Положительно') {
            return;
        }

        $driver = $form->driver;
        if ($driver === null) {
            return;
        }

        $company = $driver->company;
        if ($company === null) {
            return;
        }

        $point = $form->point;
        if ($point === null) {
            return;
        }

        $medic = $form->user;
        if ($medic === null) {
            return;
        }

        $notifyTelegramMessage = new NotifyTelegramMessage(
            $form->id,
            $company->hash_id,
            $company->name,
            $driver->fio,
            Carbon::parse($form->date)->toDateTimeImmutable(),
            $point->name,
            $medic->name,
            route('docs.get.pdf', ['type' => 'protokol', 'anketa_id' => $form->id]),
            route('docs.get.pdf', ['type' => 'closing', 'anketa_id' => $form->id])
        );

        (new TelegramNotifierService())->notify($chatId, strval($notifyTelegramMessage));
    }
}
