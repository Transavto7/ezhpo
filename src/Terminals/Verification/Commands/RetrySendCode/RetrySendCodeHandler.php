<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Commands\RetrySendCode;

use Src\Verification\Exceptions\VerificationSendFailed;
use Src\Verification\Exceptions\VerificationsNotFound;
use Src\Verification\Verifiers\DefaultVerifier;

final class RetrySendCodeHandler
{
    /** @var DefaultVerifier */
    private $verifier;

    public function __construct(DefaultVerifier $verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * @throws VerificationsNotFound
     * @throws VerificationSendFailed
     */
    public function handle(RetrySendCodeCommand $command): void
    {
        $this->verifier->retry($command->getVerificationId());
    }
}
