<?php

namespace App\Events\UserActions;

use App\User;
use Illuminate\Queue\SerializesModels;

class ClientAddRecord implements UserActionEventInterface
{
    use SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $requestType;

    /**
     * @param User $user
     * @param string $requestType
     */
    public function __construct(User $user, string $requestType)
    {
        $this->user = $user;
        $this->requestType = $requestType;
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
        return $this->requestType;
    }
}
