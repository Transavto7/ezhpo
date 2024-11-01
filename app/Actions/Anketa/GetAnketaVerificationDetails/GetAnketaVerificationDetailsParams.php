<?php

namespace App\Actions\Anketa\GetAnketaVerificationDetails;

final class GetAnketaVerificationDetailsParams
{
    /**
     * @var string
     */
    private $anketaUuid;
    /**
     * @var int|null
     */
    private $userId;

    /**
     * @param string $anketaUuid
     * @param int|null $userId
     */
    public function __construct(string $anketaUuid, ?int $userId)
    {
        $this->anketaUuid = $anketaUuid;
        $this->userId = $userId;
    }

    public function getAnketaUuid(): string
    {
        return $this->anketaUuid;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

}
