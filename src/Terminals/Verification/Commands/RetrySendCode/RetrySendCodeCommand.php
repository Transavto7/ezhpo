<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Commands\RetrySendCode;

use Ramsey\Uuid\UuidInterface;

final class RetrySendCodeCommand
{
    /** @var UuidInterface */
    private $verificationId;

    /**
     * @param UuidInterface $verificationId
     */
    public function __construct(UuidInterface $verificationId)
    {
        $this->verificationId = $verificationId;
    }

    public function getVerificationId(): UuidInterface
    {
        return $this->verificationId;
    }
}
