<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Commands\CreateVerification;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final class CreateVerificationResponse
{
    /** @var UuidInterface */
    private $id;

    /** @var string */
    private $code;

    /** @var DateTimeImmutable */
    private $next_try_at;

    /** @var int */
    private $attempts;

    /**
     * @param UuidInterface $id
     * @param string $code
     * @param DateTimeImmutable $next_try_at
     * @param int $attempts
     */
    public function __construct(
        UuidInterface $id,
        string $code,
        DateTimeImmutable $next_try_at,
        int $attempts
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->next_try_at = $next_try_at;
        $this->attempts = $attempts;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'code' => $this->code,
            'next_try_at' => $this->next_try_at->format('Y-m-d H:i:s'),
            'attempts' => $this->attempts,
        ];
    }
}
