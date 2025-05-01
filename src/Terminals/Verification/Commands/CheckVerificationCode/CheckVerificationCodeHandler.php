<?php
declare(strict_types=1);

namespace Src\Terminals\Verification\Commands\CheckVerificationCode;

use Src\Verification\Exceptions\VerificationsNotFound;
use Src\Verification\Verifiers\DefaultVerifier;

final class CheckVerificationCodeHandler
{
    /** @var DefaultVerifier */
    private $verifier;

    public function __construct(DefaultVerifier $verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * @throws VerificationsNotFound
     */
    public function handle(CheckVerificationCodeCommand $command): bool
    {
        return $this->verifier->verifyById($command->getCode(), $command->getVerificationId());
    }
}
