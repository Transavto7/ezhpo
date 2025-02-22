<?php

namespace App\Actions\Anketa\GetFormVerificationDetails;

final class GetFormVerificationDetailsParams
{
    /**
     * @var string
     */
    private $formUuid;

    /**
     * @param string $formUuid
     */
    public function __construct(string $formUuid)
    {
        $this->formUuid = $formUuid;
    }

    public function getFormUuid(): string
    {
        return $this->formUuid;
    }
}
