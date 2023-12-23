<?php

namespace Src\ExternalSystem\Dto\Common;

use Src\ExternalSystem\Exceptions\HumanNameException;

final class HumanNameDto
{
    /**
     * @var string
     */
    private $familyName;
    /**
     * @var string
     */
    private $givenName;
    /**
     * @var ?string
     */
    private $middleName;

    /**
     * @param string $familyName
     * @param string $givenName
     * @param string|null $middleName
     */
    private function __construct(string $familyName, string $givenName, ?string $middleName)
    {
        $this->familyName = $familyName;
        $this->givenName = $givenName;
        $this->middleName = $middleName;
    }

    /**
     * @throws HumanNameException
     */
    public static function fromString(string $fio): self
    {
        $fioSplit = explode(' ', preg_replace('/\s+/', ' ', trim($fio)));

        if (count($fioSplit) < 2) {
            throw new HumanNameException();
        }

        $familyName = $fioSplit[0];
        $givenName = $fioSplit[1];
        $middleName = null;

        if (count($fioSplit) === 3) {
            $middleName = $fioSplit[2];
        }

        return new self(
            $familyName,
            $givenName,
            $middleName
        );
    }

    public static function fromValues(string $familyName, string $givenName, string $middleName): self
    {
        return new self($familyName, $givenName, $middleName);
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }
}
