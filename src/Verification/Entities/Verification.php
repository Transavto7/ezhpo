<?php

declare(strict_types=1);

namespace Src\Verification\Entities;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final class Verification
{
    /** @var UuidInterface */
    private $id;

    /** @var string */
    private $subject;

    /** @var string */
    private $code;

    /** @var int */
    private $attempts;

    /** @var DateTimeImmutable */
    private $expired_at;

    /** @var DateTimeImmutable */
    private $next_attempt_at;

    /**
     * @param UuidInterface $id
     * @param string $subject
     * @param string $code
     * @param int $attempts
     * @param DateTimeImmutable $expired_at
     * @param DateTimeImmutable $next_attempt_at
     */
    public function __construct(
        UuidInterface $id,
        string $subject,
        string $code,
        int $attempts,
        DateTimeImmutable $expired_at,
        DateTimeImmutable $next_attempt_at
    ) {
        $this->id = $id;
        $this->subject = $subject;
        $this->code = $code;
        $this->attempts = $attempts;
        $this->expired_at = $expired_at;
        $this->next_attempt_at = $next_attempt_at;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expired_at;
    }

    public function getNextAttemptAt(): DateTimeImmutable
    {
        return $this->next_attempt_at;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function addAttempt()
    {
        $this->attempts++;
        $this->next_attempt_at = new DateTimeImmutable('+1 minute');
    }
}
