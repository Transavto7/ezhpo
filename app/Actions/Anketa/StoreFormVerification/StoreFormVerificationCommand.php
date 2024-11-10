<?php

namespace App\Actions\Anketa\StoreFormVerification;

use Carbon\Carbon;

final class StoreFormVerificationCommand
{
    /**
     * @var string
     */
    private $formUuid;
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
     * @param string $formUuid
     * @param string $clientHash
     * @param bool $isAuthorized
     * @param Carbon $date
     */
    public function __construct(string $formUuid, string $clientHash, bool $isAuthorized, Carbon $date)
    {
        $this->formUuid = $formUuid;
        $this->clientHash = $clientHash;
        $this->isAuthorized = $isAuthorized;
        $this->date = $date;
    }

    public function getFormUuid(): string
    {
        return $this->formUuid;
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
