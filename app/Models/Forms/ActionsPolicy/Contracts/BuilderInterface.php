<?php

namespace App\Models\Forms\ActionsPolicy\Contracts;

use App\Models\Forms\Form;
use App\User;

interface BuilderInterface
{
    public static function make(Form $form, ?User $user): PolicyInterface;
}
