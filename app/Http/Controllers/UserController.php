<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Получает данные о пользователе по id
     * */
    public function fetchUserData(Request $request)
    {
        return response([
            User::with(['roles', 'pv', 'company'])
                ->find($request->get('user_id')),
        ]);
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
//            throw new \Exception($userId);
            $user           = User::with(['roles', 'permissions', 'pv'])
                                  ->find($userId);
            $user->name     = $request->get('name', null);
            $user->login    = $request->get('login', null);
            $user->email    = $request->get('email', null);
            $user->eds      = $request->get('eds', null);
            $user->timezone = $request->get('timezone', null);
            $user->blocked  = $request->get('blocked', null);

            $user->syncRoles($request->get('roles', null));
            $user->syncPermissions($request->get('permissions', null));

            $user->pv()->associate($request->get('pv'));

            $user->save();
        } else {
            $user = User::create([
                'name'     => $request->get('name', null),
                'login'    => $request->get('login', null),
                'email'    => $request->get('email', null),
                'eds'      => $request->get('eds', null),
                'timezone' => $request->get('timezone', null),
                'blocked'  => $request->get('blocked', null),
            ]);

//            $user->syncRoles($request->get('roles', []));
//            $user->syncPermissions($request->get('permissions', []));

            $user->pv()->associate($request->get('pv'));

            $user->save();
        }

        return response([
            'status'    => true,
            'user_info' => $user,
        ]);
    }


    public function index(Request $request)
    {
        $users = User::with(['roles', 'pv', 'company']);
//dd(\App\Town::with(['pv'])->get()->toArray());
        if ($name = $request->get('name')) {
            $users->where('name', 'like', "%$name%");
        }
        if ($email = $request->get('name')) {
            $users->where('name', 'like', "%$email%");
        }
        if ($pv_id = $request->get('pv_id')) {
            $users->where('pv_id', $pv_id);
        }

//dd($users->limit(2)->get()->toArray());
        return view('admin.users_v2.index')
            ->with([
                'users' => $users->paginate(),
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        //
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
        //
    }
}
