<?php

declare(strict_types=1);

namespace Src\Verification\CodeGenerators;

interface CodeGenerator
{
    public function generate(): string;
}
