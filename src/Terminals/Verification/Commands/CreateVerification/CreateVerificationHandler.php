<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Commands\CreateVerification;

use Src\Verification\Verifiers\DefaultVerifier;

final class CreateVerificationHandler
{
    /** @var DefaultVerifier */
    private $verifier;

    public function __construct(DefaultVerifier $verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * @throws \Exception
     */
    public function handle(CreateVerificationCommand $command): CreateVerificationResponse
    {
        if ($command->getDriver()->phone === null) {
            throw new \InvalidArgumentException('Phone is not set');
        }

        $verification = $this->verifier->create($command->getDriver()->phone);
        $this->verifier->send($verification);

        return new CreateVerificationResponse(
            $verification->getId(),
            $verification->getCode(),
            $verification->getNextAttemptAt(),
            $verification->getAttempts(),
        );
    }
}
