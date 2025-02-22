<?php

namespace Src\Users\Management\Commands\CreateUser;

use App\User;
use Illuminate\Support\Facades\Hash;

final class CreateUserHandler
{
    public function handle(CreateUserCommand $command): User
    {
        return User::create([
            'entity_type' => $command->getEntityType(),
            'email' => $command->getEmail(),
            'login' => $command->getLogin(),
            'password' => Hash::make($command->getPassword()),
            'api_token' => $command->getApiToken(),
            'role' => $command->getRole() ?? 0,
        ]);
    }
}