<?php
declare(strict_types=1);

namespace Src\Terminals\Verification\Commands\CheckVerificationCode;

use Ramsey\Uuid\UuidInterface;

final class CheckVerificationCodeCommand
{
    /** @var UuidInterface */
    private $verificationId;

    /** @var string */
    private $code;

    /**
     * @param UuidInterface $verificationId
     * @param string $code
     */
    public function __construct(UuidInterface $verificationId, string $code)
    {
        $this->verificationId = $verificationId;
        $this->code = $code;
    }

    public function getVerificationId(): UuidInterface
    {
        return $this->verificationId;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
