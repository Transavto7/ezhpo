<?php

namespace Src\Users\Profile\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

final class UpdateUserAvatarController
{
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->photo_base64) {
            $base64_image = substr($request->photo_base64, strpos($request->photo_base64, ',') + 1);

            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = 'elements/user_avatar_' . $user->id . '.png';
            Storage::disk('public')->put($path, base64_decode($base64_image));

            $user->photo = $path;

            $user->save();
        }

        return redirect(route('users.profile.index'));
    }
}