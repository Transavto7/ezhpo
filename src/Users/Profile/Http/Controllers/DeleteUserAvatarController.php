<?php

namespace Src\Users\Profile\Http\Controllers;

use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

final class DeleteUserAvatarController
{
    public function __invoke(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
            $user->photo = null;

            $user->save();
        }

        return back();
    }
}