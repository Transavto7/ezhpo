<?php

namespace App\Repositories\AnketaVerifications\Entities;

use App\Enums\AnketaVerificationStatus;
use Carbon\Carbon;

final class AnketaVerification
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $anketaUuid;
    /**
     * @var Carbon
     */
    private $verificationDate;
    /**
     * @var string
     */
    private $clientHash;
    /**
     * @var AnketaVerificationStatus
     */
    private $verificationStatus;

    /**
     * @param int $id
     * @param string $anketaUuid
     * @param Carbon $verificationDate
     * @param string $clientHash
     * @param AnketaVerificationStatus $verificationStatus
     */
    public function __construct(
        int $id,
        string $anketaUuid,
        Carbon $verificationDate,
        string $clientHash,
        AnketaVerificationStatus $verificationStatus
    )
    {
        $this->id = $id;
        $this->anketaUuid = $anketaUuid;
        $this->verificationDate = $verificationDate;
        $this->clientHash = $clientHash;
        $this->verificationStatus = $verificationStatus;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAnketaUuid(): string
    {
        return $this->anketaUuid;
    }

    public function getVerificationDate(): Carbon
    {
        return $this->verificationDate;
    }

    public function getClientHash(): string
    {
        return $this->clientHash;
    }

    public function getVerificationStatus(): AnketaVerificationStatus
    {
        return $this->verificationStatus;
    }
}
