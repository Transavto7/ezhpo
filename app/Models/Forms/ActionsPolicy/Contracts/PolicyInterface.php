<?php

namespace App\Models\Forms\ActionsPolicy\Contracts;

interface PolicyInterface
{
    public function getDisabledAttributesMap(): array;

    public function getHiddenAttributesMap(): array;

    public function isAttributeDisabled(string $attribute): bool;

    public function isAttributeHidden(string $attribute): bool;
}
