<?php

declare(strict_types=1);

namespace Src\Verification;

final class CodeGenerator
{
    public function generate(int $length = 5): string
    {
        return str_pad(
            (string) rand(0, pow(10, $length) - 1),
            $length,
            '0',
            STR_PAD_LEFT
        );
    }
}
