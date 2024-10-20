<?php

namespace App\Actions\Anketa\GetAnketaVerificationDetails;

use App\ValueObjects\ClientHash;

final class GetAnketaVerificationDetailsParams
{
    /**
     * @var string
     */
    private $anketaUuid;
    /**
     * @var ClientHash
     */
    private $clientHash;
    /**
     * @var int|null
     */
    private $userId;

    /**
     * @param string $anketaUuid
     * @param ClientHash $clientHash
     * @param int|null $userId
     */
    public function __construct(string $anketaUuid, ClientHash $clientHash, ?int $userId)
    {
        $this->anketaUuid = $anketaUuid;
        $this->clientHash = $clientHash;
        $this->userId = $userId;
    }

    public function getAnketaUuid(): string
    {
        return $this->anketaUuid;
    }

    public function getClientHash(): ClientHash
    {
        return $this->clientHash;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

}
