<?php

namespace App\Listeners\Forms;

use App\Events\Forms\DriverDismissed;
use App\Models\Forms\MedicForm;
use App\Models\Forms\TechForm;
use App\Services\Notifier\TelegramNotifierService;
use App\ValueObjects\NotifyTelegramMessages\MedicMessage;
use App\ValueObjects\NotifyTelegramMessages\MessageInterface;
use App\ValueObjects\NotifyTelegramMessages\TechMessage;
use Illuminate\Support\Carbon;

class NotifyDismissingTG
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

        if (!($formDetails instanceof MedicForm || $formDetails instanceof TechForm)) {
            return;
        }

        $dismissedReason = $formDetails->dismissedReason;

        if (! count($dismissedReason)) {
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

        $responsiblePerson = $company->responsible
            ? $company->responsible->name
            : 'не указан';

        if ($formDetails instanceof MedicForm) {
            $message = new MedicMessage(
                $responsiblePerson,
                $dismissedReason,
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

            $this->notify($chatId, $message);

            return;
        }

        $carNumber = $formDetails->car
            ? $formDetails->car->gos_number
            : null;

        $message = new TechMessage(
            $responsiblePerson,
            $dismissedReason,
            $form->id,
            $company->hash_id,
            $company->name,
            $driver->fio,
            $carNumber,
            Carbon::parse($form->date)->toDateTimeImmutable(),
            $point->name,
            $medic->name,
        );

        $this->notify($chatId, $message);
    }

    private function notify(string $chatId, MessageInterface $message)
    {
        (new TelegramNotifierService())->notify($chatId, strval($message));
    }
}
