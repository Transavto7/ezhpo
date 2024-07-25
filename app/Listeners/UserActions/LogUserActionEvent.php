<?php

namespace App\Listeners\UserActions;

use App\Events\UserActions\UserActionEventInterface;
use App\UserActions;

class LogUserActionEvent
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
     * @param UserActionEventInterface $event
     * @return void
     */
    public function handle(UserActionEventInterface $event)
    {
        $user = $event->getUser();

        if (!$user->hasRole('client')) return;

        UserActions::create([
            'type' => $event->getType(),
            'user_id' => $user->getAttribute('id')
        ]);
    }
}
