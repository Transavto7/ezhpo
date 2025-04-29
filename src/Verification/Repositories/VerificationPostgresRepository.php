<?php

declare(strict_types=1);

namespace Src\Verification\Repositories;

use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Src\Verification\Entities\Verification;

final class VerificationPostgresRepository implements VerificationRepository
{
    private $table = 'verifications';

    public function add(Verification $verification): void
    {
        DB::table($this->table)->insert([
            'id' => $verification->getId()->toString(),
            'subject' => $verification->getSubject(),
            'code' => $verification->getCode(),
            'attempts' => $verification->getAttempts(),
            'expired_at' => $verification->getExpiredAt(),
            'next_attempt_at' => $verification->getNextAttemptAt(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @param $subject
     * @return array<Verification>
     */
    public function getAvailableVerificationsBySubject($subject): array
    {
        $verifications = DB::table($this->table)
            ->where('subject', '=', $subject)
            ->where('is_used', '=', false)
            ->where('expired_at', '>', now())
            ->get()
            ->toArray();

        return array_map([$this, 'deserialize'], $verifications);
    }

    /**
     * @param array<string> $verificationIds
     */
    public function useVerificationsById(array $verificationIds): void
    {
        DB::table($this->table)
            ->whereIn('id', $verificationIds)
            ->update([
                'is_used' => true,
            ]);
    }

    public function save(Verification $verification): void
    {
        DB::table($this->table)
            ->where('id', '=', $verification->getId()->toString())
            ->update([
                'subject' => $verification->getSubject(),
                'code' => $verification->getCode(),
                'attempts' => $verification->getAttempts(),
                'expired_at' => $verification->getExpiredAt(),
                'next_attempt_at' => $verification->getNextAttemptAt(),
                'updated_at' => now(),
            ]);
    }

    public function getVerificationById(UuidInterface $verificationId): ?Verification
    {
        $rawVerification = DB::table($this->table)
            ->where('id', '=', $verificationId->toString())
            ->first();

        return $rawVerification === null ? null : $this->deserialize($rawVerification);
    }

    private function deserialize(object $rawVerification): Verification
    {
        return new Verification(
            Uuid::fromString($rawVerification->id),
            $rawVerification->subject,
            $rawVerification->code,
            $rawVerification->attempts,
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rawVerification->expired_at),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rawVerification->next_attempt_at),
        );
    }
}
