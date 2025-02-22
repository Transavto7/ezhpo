<?php

namespace Src\Users\Profile\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;

final class ProfilePageController
{
    public function __invoke()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->load(['roles']);

        $pvName = null;
        $entity = $user->entity;
        if ($entity && ! $user->isDriver()) {
            $pv = $entity->point;
            $pvName = $pv ? $pv->name : null;
        }

        $eds = null;
        $timezone = null;
        if ($user->isTerminal() || $user->isEmployee()) {
            $eds = $entity->eds;
            $timezone = $entity->timezone;
        }

        return view('UsersProfile::index', [
            'user' => $user,
            'pvName' => $pvName,
            'hashId' => $entity->hash_id,
            'timezone' => $timezone,
            'eds' => $eds,
        ]);
    }
}
