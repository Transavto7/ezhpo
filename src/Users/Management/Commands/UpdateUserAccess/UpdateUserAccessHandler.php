<?php

namespace Src\Users\Management\Commands\UpdateUserAccess;

final class UpdateUserAccessHandler
{
    public function handle(UpdateUserAccessCommand $command)
    {
        $command->getUser()->roles()->sync($command->getRolesIds());
        $command->getUser()->permissions()->sync($command->getPermissionsIds());
    }
}