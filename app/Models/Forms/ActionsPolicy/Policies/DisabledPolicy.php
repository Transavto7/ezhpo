<?php

namespace App\Models\Forms\ActionsPolicy\Policies;

use App\Models\Forms\ActionsPolicy\Contracts\PolicyInterface;

class DisabledPolicy implements PolicyInterface
{
    public function getDisabledAttributesMap(): array
    {
        return [];
    }

    public function getHiddenAttributesMap(): array
    {
        return [];
    }

    public function isAttributeDisabled(string $attribute): bool
    {
        return false;
    }

    public function isAttributeHidden(string $attribute): bool
    {
        return false;
    }
}
