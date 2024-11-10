<?php

namespace App\Repositories\FormVerifications\Entities;

use App\Enums\FormVerificationStatus;
use Carbon\Carbon;

final class FormVerification
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $formUuid;
    /**
     * @var Carbon
     */
    private $verificationDate;
    /**
     * @var string
     */
    private $clientHash;
    /**
     * @var FormVerificationStatus
     */
    private $verificationStatus;

    /**
     * @param int $id
     * @param string $formUuid
     * @param Carbon $verificationDate
     * @param string $clientHash
     * @param FormVerificationStatus $verificationStatus
     */
    public function __construct(
        int                    $id,
        string                 $formUuid,
        Carbon                 $verificationDate,
        string                 $clientHash,
        FormVerificationStatus $verificationStatus
    )
    {
        $this->id = $id;
        $this->formUuid = $formUuid;
        $this->verificationDate = $verificationDate;
        $this->clientHash = $clientHash;
        $this->verificationStatus = $verificationStatus;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFormUuid(): string
    {
        return $this->formUuid;
    }

    public function getVerificationDate(): Carbon
    {
        return $this->verificationDate;
    }

    public function getClientHash(): string
    {
        return $this->clientHash;
    }

    public function getVerificationStatus(): FormVerificationStatus
    {
        return $this->verificationStatus;
    }
}
