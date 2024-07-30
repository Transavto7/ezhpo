<?php

namespace App\Observers;

use App\Driver;
use App\Enums\LogActionTypesEnum;
use App\Log;
use Illuminate\Support\Facades\Auth;

class DriverObserver
{
    /**
     * Handle the driver "updating" event.
     *
     * @param Driver $driver
     * @return void
     */
    public function created(Driver $driver)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::CREATING
        ]);

        $log->model()->associate($driver);
        $log->save();
    }

    /**
     * Handle the driver "updating" event.
     *
     * @param Driver $driver
     * @return void
     */
    public function updating(Driver $driver)
    {
        $logData = [];

        foreach ($driver->getDirty() as $attribute => $newValue) {
            if (empty($newValue) && empty($driver->getOriginal($attribute))) {
                continue;
            }

            $logData[] = [
                'name' => $attribute,
                'oldValue' => $driver->getOriginal($attribute),
                'newValue' => $newValue
            ];
        }

        if (count($logData) === 0) {
            return;
        }

        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::UPDATING
        ]);

        $log->setAttribute('data', $logData);
        $log->model()->associate($driver);

        $log->save();
    }

    /**
     * Handle the driver "deleted" event.
     *
     * @param Driver $driver
     * @return void
     */
    public function deleted(Driver $driver)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DELETING
        ]);

        $log->model()->associate($driver);
        $log->save();
    }

    /**
     * Handle the driver "restored" event.
     *
     * @param Driver $driver
     * @return void
     */
    public function restored(Driver $driver)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::RESTORING
        ]);

        $log->model()->associate($driver);
        $log->save();
    }
}
