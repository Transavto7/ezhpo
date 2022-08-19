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

//        if($user->hasRole('client', '==')) {
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

            if(preg_match('/^data:image\/(\w+);base64,/', $v)) {
                $k = str_replace('_base64', '', $k);

                $base64_image = substr($v, strpos($v, ',') + 1);
                $base64_image = base64_decode($base64_image);

                $hash = sha1(time());
                $path = "elements/$hash.png";

                $base64_image = Storage::disk('public')->put($path, $base64_image);

                $user->$k = $path;
            } else {
                if($user[$k]) $user[$k] = $v;
            }
        }

        /*foreach($request->allFiles() as $file_key => $file) {
            if($file_key === 'photo') {
                Storage::disk('public')->delete($user->$file_key);

                $file_path = Storage::disk('public')->putFile('avatars', $file);

                $user[$file_key] = $file_path;
            }
        }*/

        // Если пользователь сохранился
        if($user->save()) {
            return redirect( route('profile') );
        } else abort(500, 'Вы неверно ввели значения формы');
    }
}
