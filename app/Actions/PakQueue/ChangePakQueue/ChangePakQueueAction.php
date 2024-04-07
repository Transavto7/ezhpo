<?php

namespace App\Actions\PakQueue\ChangePakQueue;

use App\User;

class ChangePakQueueAction
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $admitted;

    /**
     * @var User
     */
    private $medic;

    public function __construct(int $id, string $admitted, User $medic) {
        $this->id = $id;
        $this->admitted = $admitted;
        $this->medic = $medic;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAdmitted(): string
    {
        return $this->admitted;
    }

    /**
     * @return User
     */
    public function getMedic(): User
    {
        return $this->medic;
    }
}
