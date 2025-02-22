<?php

namespace Src\Users\Management\Commands\UnblockUser;

final class UnblockUserHandler
{
    public function handle(UnblockUserCommand $command)
    {
        $user = $command->getUser();
        $user->blocked = 0;

        $user->save();
    }
}