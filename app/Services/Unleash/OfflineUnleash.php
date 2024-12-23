<?php

namespace App\Services\Unleash;

use Unleash\Client\Configuration\Context;
use Unleash\Client\DTO\DefaultVariant;
use Unleash\Client\DTO\Variant;
use Unleash\Client\Unleash;

class OfflineUnleash implements Unleash
{
    public function isEnabled(string $featureName, ?Context $context = null, bool $default = false): bool
    {
        $features = config('features') ?? [];

        $feature = $features[$featureName] ?? null;

        return $feature ?? $default;
    }

    /**
     * @throws \Exception
     */
    public function getVariant(string $featureName, ?Context $context = null, ?Variant $fallbackVariant = null): Variant
    {
        return new DefaultVariant(
            'default',
            $this->isEnabled($featureName, $context)
        );
    }

    public function register(): bool
    {
        return true;
    }
}
