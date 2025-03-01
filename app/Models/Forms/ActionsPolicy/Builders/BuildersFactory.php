<?php

namespace App\Models\Forms\ActionsPolicy\Builders;

use App\Enums\FeaturesEnum;
use App\Models\Forms\ActionsPolicy\Contracts\BuilderInterface;
use Unleash\Client\Configuration\UnleashContext;
use Unleash\Client\Unleash;

final class BuildersFactory
{
    public function make(): BuilderInterface
    {
        $unleash = resolve(Unleash::class);

        $context = (new UnleashContext())
            ->setHostname(config('unleash.hostname'));

        if ($unleash->isEnabled(FeaturesEnum::FORMS_BLOCK_ATTRIBUTES_UPDATING, $context)) {
            return new ByStateBuilder();
        }

        return new DisabledBuilder();
    }
}
