<?php

namespace App\Actions\Anketa\StoreAnketaVerification;

final class StoreAnketaVerificationCommand
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
     * @var bool
     */
    private $isAuthorized;

    /**
     * @param string $anketaUuid
     * @param string $clientHash
     * @param bool $isAuthorized
     */
    public function __construct(string $anketaUuid, string $clientHash, bool $isAuthorized)
    {
        $this->anketaUuid = $anketaUuid;
        $this->clientHash = $clientHash;
        $this->isAuthorized = $isAuthorized;
    }

    public function getAnketaUuid(): string
    {
        return $this->anketaUuid;
    }

    public function getClientHash(): string
    {
        return $this->clientHash;
    }

    public function isAuthorized(): bool
    {
        return $this->isAuthorized;
    }
}
