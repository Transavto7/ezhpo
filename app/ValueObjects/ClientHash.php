<?php

namespace App\ValueObjects;

final class ClientHash
{
    /**
     * @var string
     */
    private $ipAddress;
    /**
     * @var string
     */
    private $userAgent;
    /**
     * @var string
     */
    private $value;

    private function __construct(string $ipAddress, string $userAgent)
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;

        $identifierString = $userAgent . '|' . $ipAddress;
        $this->value = hash('sha256', $identifierString);
    }

    public static function from(string $ipAddress, string $userAgent): self
    {
        return new self($ipAddress, $userAgent);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
