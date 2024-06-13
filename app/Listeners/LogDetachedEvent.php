<?php

namespace App\Listeners;

use App\Enums\LogActionTypesEnum;
use App\Enums\LogModelTypesEnum;
use App\Events\Relations\Detached;
use App\Log;
use Illuminate\Support\Facades\Auth;

class LogDetachedEvent
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
     * @param Detached $event
     * @return void
     */
    public function handle(Detached $event)
    {
        if (count($event->getRelated()) === 0) return;

        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DETACHING
        ]);

        $log->data = [
            [
                'name' => LogModelTypesEnum::label($event->getRelatedType()),
                'oldValue' => implode(",", $event->getRelated()),
                'newValue' => ''
            ]
        ];

        $log->model()->associate($event->getParent());

        $log->save();

        $related = app($event->getRelatedType());

        foreach ($event->getRelated() as $relatedId) {
            $log = Log::create([
                'uuid' => $event->getUuid(),
                'user_id' => Auth::id(),
                'type' => LogActionTypesEnum::DETACHING
            ]);

            $log->data = [
                [
                    'name' => LogModelTypesEnum::label(get_class($event->getParent())),
                    'oldValue' => $event->getParent()->id,
                    'newValue' => ''
                ]
            ];

            $log->model()->associate($related::withTrashed()->find($relatedId));

            $log->save();
        }
    }
}
