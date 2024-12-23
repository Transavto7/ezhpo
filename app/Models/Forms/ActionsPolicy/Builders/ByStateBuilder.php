<?php

namespace App\Models\Forms\ActionsPolicy\Builders;

use App\Models\Forms\Form;
use App\User;
use App\Models\Forms\ActionsPolicy\Contracts\BuilderInterface;
use App\Models\Forms\ActionsPolicy\Contracts\PolicyInterface;
use App\Models\Forms\ActionsPolicy\Policies\ByStatePolicy;

class ByStateBuilder implements BuilderInterface
{
    public static function build(Form $form, ?User $user): PolicyInterface
    {
        return new ByStatePolicy($form, $user);
    }
}
