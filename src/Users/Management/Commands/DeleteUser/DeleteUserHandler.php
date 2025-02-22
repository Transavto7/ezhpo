<?php

namespace Src\Users\Management\Commands\DeleteUser;

final class DeleteUserHandler
{
    public function handle(DeleteUserCommand $command)
    {
        $command->getUser()->delete();
    }
}