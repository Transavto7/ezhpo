<?php

namespace Src\Users\Management\Commands\UpdateUser;

use Illuminate\Support\Facades\Hash;

final class UpdateUserHandler
{
    public function handle(UpdateUserCommand $command)
    {
        $user = $command->getUser();

        $user->login = $command->getLogin();
        $user->email = $command->getEmail();

        if ($command->getPassword()) {
            $user->password = Hash::make($command->getPassword());
        }

        $user->save();

        return $user;
    }
}