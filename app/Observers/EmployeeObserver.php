<?php

namespace App\Observers;

use App\Employee;
use App\Enums\LogActionTypesEnum;
use App\Log;
use Illuminate\Support\Facades\Auth;

class EmployeeObserver
{
    /**
     * Handle the car "updating" event.
     *
     * @param Employee $employee
     * @return void
     */
    public function created(Employee $employee)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::CREATING,
        ]);

        $log->model()->associate($employee);
        $log->save();
    }

    /**
     * Handle the user "updating" event.
     *
     * @param Employee $employee
     * @return void
     */
    public function updating(Employee $employee)
    {
        $logData = [];

        foreach ($employee->getDirty() as $attribute => $newValue) {
            if (empty($newValue) && empty($employee->getOriginal($attribute))) {
                continue;
            }

            $data = [
                'name' => $attribute,
                'oldValue' => $employee->getOriginal($attribute),
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
        $log->model()->associate($employee);

        $log->save();
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param Employee $employee
     * @return void
     */
    public function deleted(Employee $employee)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DELETING,
        ]);

        $log->model()->associate($employee);
        $log->save();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param Employee $employee
     * @return void
     */
    public function restored(Employee $employee)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::RESTORING,
        ]);

        $log->model()->associate($employee);
        $log->save();
    }
}
