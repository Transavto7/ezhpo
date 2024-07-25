<?php

namespace App\Events\UserActions;

use App\Enums\UserActionTypesEnum;
use App\User;
use Illuminate\Queue\SerializesModels;

class ClientDocImport implements UserActionEventInterface
{
    use SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): string
    {
        return UserActionTypesEnum::CLIENT_DOC_IMPORT;
    }
}
