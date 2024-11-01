<?php

namespace App\Actions\Anketa\StoreAnketaVerification;

use Carbon\Carbon;

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
     * @var Carbon
     */
    private $date;

    /**
     * @param string $anketaUuid
     * @param string $clientHash
     * @param bool $isAuthorized
     * @param Carbon $date
     */
    public function __construct(string $anketaUuid, string $clientHash, bool $isAuthorized, Carbon $date)
    {
        $this->anketaUuid = $anketaUuid;
        $this->clientHash = $clientHash;
        $this->isAuthorized = $isAuthorized;
        $this->date = $date;
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

    public function getDate(): Carbon
    {
        return $this->date;
    }


}
