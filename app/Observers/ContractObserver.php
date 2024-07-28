<?php

namespace App\Observers;

use App\Enums\LogActionTypesEnum;
use App\Log;
use App\Models\Contract;
use Illuminate\Support\Facades\Auth;

class ContractObserver
{
    /**
     * Handle the contract "updating" event.
     *
     * @param Contract $contract
     * @return void
     */
    public function created(Contract $contract)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::CREATING
        ]);

        $log->model()->associate($contract);
        $log->save();
    }

    /**
     * Handle the contract "updating" event.
     *
     * @param Contract $contract
     * @return void
     */
    public function updating(Contract $contract)
    {
        $logData = [];

        foreach ($contract->getDirty() as $attribute => $newValue) {
            if (empty($newValue) && empty($contract->getOriginal($attribute))) {
                continue;
            }

            $logData[] = [
                'name' => $attribute,
                'oldValue' => $contract->getOriginal($attribute),
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
        $log->model()->associate($contract);

        $log->save();
    }

    /**
     * Handle the contract "deleted" event.
     *
     * @param Contract $contract
     * @return void
     */
    public function deleted(Contract $contract)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DELETING
        ]);

        $log->model()->associate($contract);
        $log->save();
    }

    /**
     * Handle the contract "restored" event.
     *
     * @param Contract $contract
     * @return void
     */
    public function restored(Contract $contract)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::RESTORING
        ]);

        $log->model()->associate($contract);
        $log->save();
    }
}
