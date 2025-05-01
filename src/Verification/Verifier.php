<?php

declare(strict_types=1);

namespace Src\Verification;

use Ramsey\Uuid\UuidInterface;
use Src\Notifications\Notifier;
use Src\Verification\Entities\Verification;

interface Verifier
{
    public function setNotifier(Notifier $notifier): void;

    public function setMessage(string $message): void;

    public function create($subject): Verification;

    public function send(Verification $verification): void;

    public function verify(string $code, $subject): bool;

    public function verifyById(string $code, UuidInterface $verificationId): bool;

    public function retry(UuidInterface $verificationId): void;
}
