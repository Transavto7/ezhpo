<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function DeleteAvatar(): RedirectResponse
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

    /**
     * GET-запросы
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->load(['company', 'pv', 'roles']);

        return view('profile.index', compact('user'));
    }

    /**
     * POST-запросы
     */
    public function updateAvatar(Request $request)
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

        return redirect(route('profile.index'));
    }
}
