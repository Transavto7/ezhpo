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
     * @var string
     */
    protected $itemType;

    /**
     * @param User $user
     * @param string $itemType
     */
    public function __construct(User $user, string $itemType)
    {
        $this->user = $user;
        $this->itemType = $itemType;
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
        return $this->itemType;
    }
}
