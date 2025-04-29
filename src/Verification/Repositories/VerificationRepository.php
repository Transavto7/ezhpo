<?php
declare(strict_types=1);

namespace Src\Verification\Repositories;

use Ramsey\Uuid\UuidInterface;
use Src\Verification\Entities\Verification;

interface VerificationRepository
{
    public function add(Verification $verification): void;

    /**
     * @param $subject
     * @return array<Verification>
     */
    public function getAvailableVerificationsBySubject($subject): array;

    /**
     * @param array<string> $verificationIds
     */
    public function useVerificationsById(array $verificationIds): void;

    public function save(Verification $verification): void;

    public function getVerificationById(UuidInterface $verificationId): ?Verification;
}
