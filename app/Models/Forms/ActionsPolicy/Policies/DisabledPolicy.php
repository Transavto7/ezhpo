<?php

namespace App\Models\Forms\ActionsPolicy\Policies;

use App\Models\Forms\Form;
use App\User;
use App\Models\Forms\ActionsPolicy\Contracts\BuilderInterface;
use App\Models\Forms\ActionsPolicy\Contracts\PolicyInterface;

class DisabledPolicy implements PolicyInterface, BuilderInterface
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

    public static function make(Form $form, ?User $user): PolicyInterface
    {
        return new self();
    }
}
