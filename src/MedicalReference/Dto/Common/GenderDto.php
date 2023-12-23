<?php

namespace Src\MedicalReference\Dto\Common;

class GenderDto
{
    /**
     * @var int
     */
    private $code;
    /**
     * @var string
     */
    private $displayName;

    /**
     * @param int $code
     * @param string $displayName
     */
    public function __construct(int $code, string $displayName)
    {
        $this->code = $code;
        $this->displayName = $displayName;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}
