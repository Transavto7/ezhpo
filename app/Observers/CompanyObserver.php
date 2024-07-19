<?php

namespace App\Observers;

use App\Company;
use App\Enums\LogActionTypesEnum;
use App\Log;
use Illuminate\Support\Facades\Auth;

class CompanyObserver
{
    /**
     * Handle the company "updating" event.
     *
     * @param Company $company
     * @return void
     */
    public function created(Company $company)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::CREATING
        ]);

        $log->model()->associate($company);
        $log->save();
    }

    /**
     * Handle the company "updating" event.
     *
     * @param Company $company
     * @return void
     */
    public function updating(Company $company)
    {
        $logData = [];

        foreach ($company->getDirty() as $attribute => $newValue) {
            $logData[] = [
                'name' => $attribute,
                'oldValue' => $company->getOriginal($attribute),
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
        $log->model()->associate($company);

        $log->save();
    }

    /**
     * Handle the company "deleted" event.
     *
     * @param Company $company
     * @return void
     */
    public function deleted(Company $company)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DELETING
        ]);

        $log->model()->associate($company);
        $log->save();
    }

    /**
     * Handle the company "restored" event.
     *
     * @param Company $company
     * @return void
     */
    public function restored(Company $company)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::RESTORING
        ]);

        $log->model()->associate($company);
        $log->save();
    }
}
