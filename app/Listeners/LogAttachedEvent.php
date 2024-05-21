<?php

namespace App\Listeners;

use App\Enums\LogActionTypesEnum;
use App\Enums\LogModelTypesEnum;
use App\Events\Relations\Attached;
use App\Log;
use Illuminate\Support\Facades\Auth;

class LogAttachedEvent
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
     * @param Attached $event
     * @return void
     */
    public function handle(Attached $event)
    {
        if (count($event->getRelated()) === 0) return;

        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::ATTACHING
        ]);

        $log->setAttribute('data', [
            [
                'name' => LogModelTypesEnum::label($event->getRelatedType()),
                'oldValue' => '',
                'newValue' => implode(",", $event->getRelated())
            ]
        ]);

        $log->model()->associate($event->getParent());

        $log->save();
    }
}
