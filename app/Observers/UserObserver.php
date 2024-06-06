<?php

namespace App\Observers;

use App\Enums\LogActionTypesEnum;
use App\Log;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the car "updating" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::CREATING
        ]);

        $log->model()->associate($user);
        $log->save();
    }

    /**
     * Handle the user "updating" event.
     *
     * @param User $user
     * @return void
     */
    public function updating(User $user)
    {
        $hideAttributeChanges = [
            'password'
        ];

        $logData = [];

        foreach ($user->getDirty() as $attribute => $newValue) {
            $data = [
                'name' => $attribute,
                'oldValue' => $user->getOriginal($attribute),
                'newValue' => $newValue
            ];

            if (in_array($attribute, $hideAttributeChanges)) {
                $data['oldValue'] = '***';
                $data['newValue'] = '***';
            }

            $logData[] = $data;
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
        $log->model()->associate($user);

        $log->save();
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::DELETING
        ]);

        $log->model()->associate($user);
        $log->save();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param User $user
     * @return void
     */
    public function restored(User $user)
    {
        /** @var Log $log */
        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => LogActionTypesEnum::RESTORING
        ]);

        $log->model()->associate($user);
        $log->save();
    }
}
