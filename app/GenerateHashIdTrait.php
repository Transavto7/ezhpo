<?php

namespace App;

use App\Services\HashIdGenerator\HashIdGenerator;
use Exception;

trait GenerateHashIdTrait
{
    /**
     * @throws Exception
     */
    protected function generateHashId(
        callable $validator,
        int      $min = 0,
        int      $max = 999999,
        int      $maxTries = 2
    ): int
    {
        return HashIdGenerator::generateWithSettings($validator, $min, $max, $maxTries);
    }
}
