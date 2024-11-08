<?php

namespace App\Actions\Anketa\GetAnketaVerificationHistory;

final class GetAnketaVerificationHistoryParams
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
     * @param string $anketaUuid
     * @param string $clientHash
     */
    public function __construct(string $anketaUuid, string $clientHash)
    {
        $this->anketaUuid = $anketaUuid;
        $this->clientHash = $clientHash;
    }

    public function getAnketaUuid(): string
    {
        return $this->anketaUuid;
    }

    public function getClientHash(): string
    {
        return $this->clientHash;
    }


}
