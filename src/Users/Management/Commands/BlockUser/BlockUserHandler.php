<?php

namespace Src\Users\Management\Commands\BlockUser;

final class BlockUserHandler
{
    public function handle(BlockUserCommand $command)
    {
        $user = $command->getUser();
        $user->blocked = 1;

        $user->save();
    }
}