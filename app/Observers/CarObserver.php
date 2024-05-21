<?php

namespace App\Observers;

use App\Car;
use App\Enums\LogActionTypesEnum;
use App\Log;
use Illuminate\Support\Facades\Auth;

class CarObserver
{
    /**
     * Handle the car "updating" event.
     *
     * @param Car $car
     * @return void
     */
    public function created(Car $car)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::CREATING
        ]);

        $log->model()->associate($car);
        $log->save();
    }

    /**
     * Handle the car "updating" event.
     *
     * @param Car $car
     * @return void
     */
    public function updating(Car $car)
    {
        $logData = [];

        foreach ($car->getDirty() as $attribute => $newValue) {
            $logData[] = [
                'name' => $attribute,
                'oldValue' => $car->getOriginal($attribute),
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
        $log->model()->associate($car);

        $log->save();
    }

    /**
     * Handle the car "deleted" event.
     *
     * @param Car $car
     * @return void
     */
    public function deleted(Car $car)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DELETING
        ]);

        $log->model()->associate($car);
        $log->save();
    }

    /**
     * Handle the car "restored" event.
     *
     * @param Car $car
     * @return void
     */
    public function restored(Car $car)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::RESTORING
        ]);

        $log->model()->associate($car);
        $log->save();
    }
}
