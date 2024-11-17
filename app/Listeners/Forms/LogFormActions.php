<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormAction;
use App\FormLog;
use App\User;

class LogFormActions
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
     * @param FormAction $event
     * @return void
     */
    public function handle(FormAction $event)
    {
        $logData = $this->logModel($event->getForm());
        $logData = array_merge($logData, $this->logModel($event->getForm()->details));

        if (count($logData) === 0) {
            return;
        }

        FormLog::create([
            'user_id' => $event->getUser()->id,
            'type' => $event->getType(),
            'data' => $logData,
            'model_id' => $event->getForm()->id,
            'model_type' => get_class($event->getForm()->details),
        ]);
    }

    private function logModel($form): array
    {
        $logData = [];

        foreach ($form->getDirty() as $attribute => $newValue) {
            if (empty($newValue) && empty($form->getOriginal($attribute))) {
                continue;
            }

            $logData[] = [
                'name' => $attribute,
                'oldValue' => $form->getOriginal($attribute),
                'newValue' => $newValue
            ];
        }

        return $logData;
    }
}
