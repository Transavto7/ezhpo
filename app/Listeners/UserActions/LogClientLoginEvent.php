<?php

namespace App\Listeners\UserActions;

use App\Enums\UserActionTypesEnum;
use App\Models\UserActions;
use App\User;
use Illuminate\Auth\Events\Login;

class LogClientLoginEvent
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
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        /** @var User $user */
        $user = $event->user;

        if (!$user->hasRole('client')) return;

        UserActions::create([
            'type' => UserActionTypesEnum::CLIENT_LOGIN,
            'user_id' => $user->getAttribute('id')
        ]);
    }
}
