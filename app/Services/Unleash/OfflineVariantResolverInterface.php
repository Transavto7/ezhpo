<?php

namespace App\Services\Unleash;

use Unleash\Client\Configuration\Context;
use Unleash\Client\DTO\Variant;

interface OfflineVariantResolverInterface
{
    public function getVariant(?Context $context = null): Variant;
}
