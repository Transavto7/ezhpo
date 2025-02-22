<?php

namespace Src\Users\Management\Commands\RestoreUser;

final class RestoreUserHandler
{
    public function handle(RestoreUserCommand $command)
    {
        $command->getUser()->restore();
    }
}