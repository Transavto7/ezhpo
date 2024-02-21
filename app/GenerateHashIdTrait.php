<?php

namespace App;

use Exception;

trait GenerateHashIdTrait
{
    /**
     * @throws Exception
     */
    protected function generateHashId(
        callable $validator,
        int $min = 0,
        int $max = 999999,
        int $maxTries = 2
    ): int
    {
        $tries = 0;

        do {
            $value = mt_rand($min, $max);

            if ($validator($value)) {
                return $value;
            }

            $tries++;

            if ($tries > $maxTries) {
                throw new Exception('Превышен лимит попыток генерации HASH_ID');
            }
        } while (true);
    }
}
