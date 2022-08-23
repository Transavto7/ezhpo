<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $users = User::with(['roles', 'pv', 'company']);

        if ($name = $request->get('name')) {
            $users->where('name', 'like', "%$name%");
        }
        if ($email = $request->get('email')) {
            $users->where('email', 'like', "%$email%");
        }
        if ($pv_id = $request->get('pv_id')) {
            $users->where('pv_id', $pv_id);
        }

        return view('admin.users_v2.index')
            ->with([
                'users' => $users->paginate(),
            ]);
    }

    /**
     * Получает данные о пользователе по id
     * */
    public function fetchUserData(Request $request)
    {
        $result = User::with(['roles', 'pv', 'company'])
                      ->find($request->get('user_id'));

        $disablePermissions = collect();
        $result->roles()
               ->with(['permissions'])
               ->get()
               ->map(function ($q) use (&$disablePermissions) {
                   $disablePermissions = $disablePermissions->merge($q->permissions);
               });

        $result->disable = $disablePermissions
                    ->unique('id')
                    ->pluck('id')
                    ->values();

        return response($result);
    }

    /**
     * Создаёт/обновляет данные пользователя
     *
     * @param Request $request
     *
     * @return Application|ResponseFactory|Response
     */
    public function saveUser(Request $request)
    {

        if ($userId = $request->get('user_id')) {
            $user           = User::find($userId);
            $user->name     = $request->get('name', null);
            $user->login    = $request->get('login', null);
            $user->email    = $request->get('email', null);
            $user->eds      = $request->get('eds', null);
            $user->timezone = $request->get('timezone', null);
            $user->blocked  = $request->get('blocked', null);

        } else {
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', 'min:1', 'max:255'],
                'email'    => ['required', 'string', 'min:1', 'max:255'],
            ]);

            if ($validator->fails()) {
                return response([
                    'message' => $validator->errors(),
                    'status'  => false,
                ]);
            }

            $user = User::create([
                'name'     => $request->get('name', null),
                'email'    => $request->get('email', null),
                'eds'      => $request->get('eds', null),
                'timezone' => $request->get('timezone', null),
                'blocked'  => $request->get('blocked', null),
            ]);
        }

        if ($login = $request->get('login', null)) {
            $user->login = $login;
        } else {
            $user->login = $user->email;
        }

        if ($password = $request->get('password', null)) {
            $password  = Hash::make($password);
            $api_token = Hash::make(date('H:i:s').sha1($password));

            $user->password  = $password;
            $user->api_token = $api_token;
        }

        $user->roles()->sync($request->get('roles', []));
        $user->permissions()->sync($request->get('permissions', []));


        $user->pv()->associate($request->get('pv', null));
        $user->save();

        return response([
            'status'    => true,
            'user_info' => User::with(['roles', 'permissions', 'pv'])
                               ->find($user->id),
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        return response([
            'status' => User::find($id)->delete(),
        ]);
    }
}
