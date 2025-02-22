<?php

namespace Src\Users\Management\Commands\UpdateUserAccess;

use App\User;

final class UpdateUserAccessCommand
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var int[]
     */
    private $rolesIds;
    /**
     * @var int[]
     */
    private $permissionsIds;

    /**
     * @param User $user
     * @param int[] $rolesIds
     * @param int[] $permissionsIds
     */
    public function __construct(User $user, array $rolesIds, array $permissionsIds)
    {
        $this->user = $user;
        $this->rolesIds = $rolesIds;
        $this->permissionsIds = $permissionsIds;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRolesIds(): array
    {
        return $this->rolesIds;
    }

    public function getPermissionsIds(): array
    {
        return $this->permissionsIds;
    }


}