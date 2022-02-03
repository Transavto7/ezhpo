<?php

namespace App\Http\Controllers;

use App\Point;
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
            case 1: $role = 'Контролёр ТС'; break;
            case 2: $role = 'Медицинский сотрудник'; break;
            case 3: $role = 'Водитель'; break;
            case 11: $role = 'Менеджер'; break;
            case 777: $role = 'Администратор'; break;
            case 778: $role = 'Терминал'; break;
        }

        if($text) {
            return $role;
        }

        return $user->role;
    }

    /**
     * Получение аватарки
     */
    public static function getAvatar ($user_id = 0) {
        $user = Auth::user();
        $user_id = $user_id === 0 ? $user->id : $user_id;
        $avatar = User::find($user_id)->photo;

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
        $user = Auth::user();

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
        $new_password = trim(
            (isset($data['password_new'])) ? $data['password_new'] : ''
        );
        $password = trim(
            (isset($data['password'])) ? $data['password'] : ''
        );

        // Обновлляем пароль, если надо
        if(Hash::check($password, $user->password) && $new_password != '' && strlen($new_password) >= 5) {
            $new_password_hash = Hash::make($new_password);

            $user->password = $new_password_hash;
        }

        // Удаляем поля которые не пойдут в повторное обновление
        unset($data['password']);
        unset($data['password_new']);

        // Обновляем все данные что нашли, кроме пароля
        foreach ($data as $k => $v) {
            if($user[$k]) $user[$k] = $v;
        }

        // Если пользователь сохранился
        if($user->save()) {
            return redirect( route('profile') );
        } else abort(500, 'Вы неверно ввели значения формы');
    }
}
