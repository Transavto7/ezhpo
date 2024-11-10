<?php

namespace App\Actions\Anketa\GetFormVerificationHistory;

final class GetFormVerificationHistoryParams
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
     * @param string $formUuid
     * @param string $clientHash
     */
    public function __construct(string $formUuid, string $clientHash)
    {
        $this->formUuid = $formUuid;
        $this->clientHash = $clientHash;
    }

    public function getFormUuid(): string
    {
        return $this->formUuid;
    }

    public function getClientHash(): string
    {
        return $this->clientHash;
    }


}
