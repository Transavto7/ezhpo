<?php

namespace App\Actions\Anketa\GetAnketaVerificationDetails;

final class GetAnketaVerificationDetailsParams
{
    /**
     * @var string
     */
    private $anketaUuid;
    /**
     * @var string
     */
    private $clientHash;
    /**
     * @var int|null
     */
    private $userId;

    /**
     * @param string $anketaUuid
     * @param string $clientHash
     * @param int|null $userId
     */
    public function __construct(string $anketaUuid, string $clientHash, ?int $userId)
    {
        $this->anketaUuid = $anketaUuid;
        $this->clientHash = $clientHash;
        $this->userId = $userId;
    }

    public function getAnketaUuid(): string
    {
        return $this->anketaUuid;
    }

    public function getClientHash(): string
    {
        return $this->clientHash;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

}
