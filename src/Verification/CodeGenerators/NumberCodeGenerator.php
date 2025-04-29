<?php

declare(strict_types=1);

namespace Src\Verification\CodeGenerators;

final class NumberCodeGenerator implements CodeGenerator
{
    /** @var int */
    private $length = 5;

    public function generate(): string
    {
        return str_pad(
            (string) rand(0, pow(10, $this->length) - 1),
            $this->length,
            '0',
            STR_PAD_LEFT
        );
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }
}
