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
            'uuid' => $event->getUuid(),
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::ATTACHING
        ]);

        $log->data = [
            [
                'name' => LogModelTypesEnum::label($event->getRelatedType()),
                'oldValue' => '',
                'newValue' => implode(",", $event->getRelated())
            ]
        ];

        $log->model()->associate($event->getParent());

        $log->save();

        $related = app($event->getRelatedType());

        foreach ($event->getRelated() as $relatedId) {
            $log = Log::create([
                'uuid' => $event->getUuid(),
                'user_id' => Auth::id(),
                'type' => LogActionTypesEnum::ATTACHING
            ]);

            $log->data = [
                [
                    'name' => LogModelTypesEnum::label(get_class($event->getParent())),
                    'oldValue' => '',
                    'newValue' => $event->getParent()->id
                ]
            ];

            $log->model()->associate($related::withTrashed()->find($relatedId));

            $log->save();
        }
    }
}
