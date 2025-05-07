<?php

namespace App\Observers;

use App\Terminal;
use App\Enums\LogActionTypesEnum;
use App\Log;
use Illuminate\Support\Facades\Auth;

class TerminalObserver
{
    /**
     * Handle the car "updating" event.
     *
     * @param Terminal $terminal
     * @return void
     */
    public function created(Terminal $terminal)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::CREATING,
        ]);

        $log->model()->associate($terminal);
        $log->save();
    }

    /**
     * Handle the user "updating" event.
     *
     * @param Terminal $terminal
     * @return void
     */
    public function updating(Terminal $terminal)
    {
        $skipAttributeChanges = [
            'last_connection_at',
        ];

        $logData = [];

        foreach ($terminal->getDirty() as $attribute => $newValue) {
            if (empty($newValue) && empty($terminal->getOriginal($attribute))) {
                continue;
            }

            if (in_array($attribute, $skipAttributeChanges)) {
                continue;
            }

            $data = [
                'name' => $attribute,
                'oldValue' => $terminal->getOriginal($attribute),
                'newValue' => $newValue,
            ];

            $logData[] = $data;
        }

        if (count($logData) === 0) {
            return;
        }

        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::UPDATING,
        ]);

        $log->setAttribute('data', $logData);
        $log->model()->associate($terminal);

        $log->save();
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param Terminal $terminal
     * @return void
     */
    public function deleted(Terminal $terminal)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DELETING,
        ]);

        $log->model()->associate($terminal);
        $log->save();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param Terminal $terminal
     * @return void
     */
    public function restored(Terminal $terminal)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::RESTORING,
        ]);

        $log->model()->associate($terminal);
        $log->save();
    }
}
