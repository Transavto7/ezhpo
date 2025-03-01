<?php

namespace App\Actions\Anketa\GetFormVerificationDetails;

final class GetFormVerificationDetailsParams
{
    /**
     * @var string
     */
    private $formUuid;
    /**
     * @var int|null
     */
    private $userId;

    /**
     * @param string $formUuid
     * @param int|null $userId
     */
    public function __construct(string $formUuid, ?int $userId)
    {
        $this->formUuid = $formUuid;
        $this->userId = $userId;
    }

    public function getFormUuid(): string
    {
        return $this->formUuid;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

}
