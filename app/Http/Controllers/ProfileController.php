<?php

namespace App\Http\Controllers;

use App\Point;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Сниппеты
     */
    public static function getUserRole ($text = true, $user_id = 0) {
        $user = Auth::user();
        $role = '';

        if($user_id) $user = User::find($user_id);

        switch($user->role) {
            case 12: $role = 'Клиент'; break;
            case 4: $role = 'Оператор СДПО'; break;
            case 1: $role = 'Контролёр ТС'; break;
            case 2: $role = 'Медицинский сотрудник'; break;
            case 3: $role = 'Водитель'; break;
            case 11: $role = 'Менеджер'; break;
            case 13: $role = 'Инженер БДД'; break;
            case 777: $role = 'Администратор'; break;
            case 778: $role = 'Терминал'; break;
        }

        if($text) {
            return $role;
        }

        return $user->role;
    }

    public function DeleteAvatar ()
    {
        $user = Auth::user();

        if($user->photo) {
            Storage::disk('public')->delete($user->photo);
            $user->photo = null;

            $user->save();

        }

        return back();
    }

    /**
     * Получение аватарки
     */
    public static function getAvatar ($user_id = 0) {
        $user = Auth::user();
        $user_id = $user_id === 0 ? $user ? $user->id : $user_id : $user_id;
        $avatar = $user ? $user->photo : '';

        if(Storage::disk('public')->exists($avatar)) {
            $avatar = Storage::url( $avatar );
        } else {
            $avatar = asset('images/avatar_default.jpg');
        }

        return $avatar;
    }

    /**
     * GET-запросы
     */
    public function RenderIndex ()
    {
//        $user = Auth::user();
        $user = User::with(['company'])->find(Auth::user()->id);

//        if($user->hasRole('client')) {
//            return redirect( route('home') );
//        }

        // Получение пункта выпуска
        $point = Point::getPointText($user->pv_id);

        return view('profile.index', compact('user', 'point'));
    }

    /**
     * POST-запросы
     */
    public function UpdateData (Request $request)
    {
        $data = $request->all();
        $user = Auth::user();

        if ($request->photo_base64) {
            $base64_image = substr($request->photo_base64, strpos($request->photo_base64, ',') + 1);

            $photo = $user->photo;
            Storage::disk('public')->delete($photo);
            $path = 'elements/' . md5($request->photo_base64) . '.png';
            Storage::disk('public')->put($path, base64_decode($base64_image));
            $user->photo = $path;
        }
        // Если пользователь сохранился
        if($user->save()) {
            return redirect( route('profile') );
        } else abort(500, 'Вы неверно ввели значения формы');
    }
}
