<?php
declare(strict_types=1);

namespace App\Services\HashIdGenerator;

use Exception;

final class HashIdGeneratorService
{
    /**
     * @param callable $validator
     * @param HashedType|null $hashedType
     * @return int
     * @throws Exception
     */
    public function generateWithType(callable $validator, ?HashedType $hashedType = null): int
    {
        if ($hashedType === null) {
            $hashedType = HashedType::default();
        }

        $min = config(sprintf('app.hash_generator.%s.min', $hashedType));
        $max = config(sprintf('app.hash_generator.%s.max', $hashedType));
        $maxTries = config(sprintf('app.hash_generator.%s.tries', $hashedType));

        return $this->generateWithSettings($validator, $min, $max, $maxTries);
    }

    /**
     * @param callable $validator
     * @param int $min
     * @param int $max
     * @param int $maxTries
     * @return int
     * @throws Exception
     */
    public function generateWithSettings(
        callable $validator,
        int      $min = 0,
        int      $max = 999999,
        int      $maxTries = 2
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
