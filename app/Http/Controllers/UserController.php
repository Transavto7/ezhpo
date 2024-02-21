<?php

namespace App\Http\Controllers;

use App\Company;
use App\FieldPrompt;
use App\GenerateHashIdTrait;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use GenerateHashIdTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $users = User::with(['roles' => function ($q) use ($request) {
        }, 'pv', 'company'])
                     ->where(function ($query) use ($request) {
                         $query->whereDoesntHave('roles')
                               ->orWhereHas('roles', function ($q) use ($request) {
                                   $q->whereNotIn('roles.id', [3, 6, 9]);
                               });
                     });

        if ($request->get('deleted')) {
            $users->with(['deleted_user'])->onlyTrashed();
        }
        if ($id = $request->get('hash_id')) {
            $users->whereIn('hash_id', $id);
        }
        if ($email = $request->get('email')) {
            $users->where('email', 'like', "%$email%");
        }
        if ($pv_id = $request->get('pv_id')) {
            $users->where('pv_id', $pv_id);
        }

        if ($sortBy = $request->get('sortBy', 'id')) {
            if($sortBy == 'roles'){
                $users->join('model_has_roles', 'users.id', 'model_has_roles.model_id')
                      ->join('roles', function ($join) use($request) {
                          $join->on('model_has_roles.role_id', '=', 'roles.id')
                               ->orderBy('roles.guard_name', $request->get('sortDesc') == 'true' ? 'DESC' : 'ASC')
                          ;
                      })
                      ->orderBy('roles.guard_name', $request->get('sortDesc') == 'true' ? 'DESC' : 'ASC')//;
                      ->select('users.*', 'guard_name')
                      ->groupBy('users.id');
            }else{
                $users->orderBy($sortBy, $request->get('sortDesc') == 'true' ? 'DESC' : 'ASC');
            }
        }
        if ($role = $request->get('role')) {
            $users->whereHas('roles', function ($q) use ($role){
                $q->where('id', $role);
            });
        }

        if ($request->get('api')) {
            $res = $users->paginate();
            $secondRes = $users->get()->sortBy;

            return response([
                'total_rows'   => $res->total(),
                'current_page' => $res->currentPage(),
                'items'        => $res->getCollection(),
            ]);
        }

        $fields = FieldPrompt::where('type', 'users')->get();

        return view('admin.users_v2.index')
            ->with([
                       'users' => $users->paginate(),
                       'fields' => $fields
                   ]);
    }

    /**
     * Получает данные о пользователе по id
     * */
    public function fetchUserData(Request $request): JsonResponse
    {
        $result = User::query()
            ->with([
                'roles',
                'roles.permissions',
                'permissions',
                'pv',
                'company',
                'points'
            ])
            ->find($request->get('user_id'));

        $result->disable = $result->roles
            ->reduce(function ($carry, $role) {
                $carry->merge($role->permissions);

                return $carry;
            }, collect())
            ->unique('id')
            ->pluck('id')
            ->values();

        $result->permission_user = $result->permissions
            ->pluck('id')
            ->values();

        $result->pvs = $result->points
            ->pluck('id')
            ->values();

        return response()->json($result);
    }

    /**
     * Создаёт/обновляет данные пользователя
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function saveUser(Request $request): JsonResponse
    {
        $userIsClient = array_search(6, $request->get('roles', []));
        if ($userIsClient) {
            $pv = null;
            $company = $request->get('company');
        } else {
            $company = null;
            $pv = $request->get('pv');
        }

        $userId = $request->get('user_id');
        if (empty($userId)) {
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', 'min:1', 'max:255'],
                'email'    => ['required', 'string', 'min:1', 'max:255'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors(),
                ]);
            }

            $user = new User();

            $validator = function (int $hashId) {
                if (User::where('hash_id', $hashId)->first()) {
                    return false;
                }

                return true;
            };

            $user->hash_id = $this->generateHashId(
                $validator,
                config('app.hash_generator.user.min'),
                config('app.hash_generator.user.max'),
                config('app.hash_generator.user.tries')
            );
        } else {
            $user = User::find($userId);
        }

        if ($password = $request->get('password')) {
            $password  = Hash::make($password);
            $api_token = Hash::make(date('H:i:s').sha1($password));

            $user->password  = $password;
            $user->api_token = $api_token;
        }

        $user->name = $request->get('name');
        $user->login = $request->get('login');
        $user->email = $request->get('email');
        $user->eds = $request->get('eds');
        $user->timezone = $request->get('timezone');
        $user->blocked = $request->get('blocked', 0);
        $user->validity_eds_start = $request->get('validity_eds_start');
        $user->validity_eds_end = $request->get('validity_eds_end');
        $user->login = $request->get('login', $user->email);

        $user->save();

        $user->roles()->sync($request->get('roles', []));
        $user->permissions()->sync($request->get('permissions', []));
        $user->points()->sync($request->get('pvs', []));
        $user->company()->associate($company);
        $user->pv()->associate($pv);
        $user->save();

        $user = User::query()
            ->with([
                'roles',
                'permissions',
                'pv'
            ])
            ->find($user->id);

        return response()->json([
            'status'    => true,
            'user_info' => $user,
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
     */
    public function destroy(Request $request)
    {
        return response([
                            'status' => User::find($request->post('id'))->delete(),
                        ]);
    }

    public function returnTrash(Request $request)
    {
        return response([
                            'status' => User::withTrashed()->find($request->post('id'))->restore(),
                        ]);
    }

    public function fetchRoleData(Request $request)
    {
        $permissions = collect();
        Role::with(['permissions'])
            ->whereIn('id', $request->get('role_ids', [])) //
            ->get()
            ->map(function ($q) use (&$permissions) {
                $permissions = $permissions->merge($q->permissions);
            });

        $permissions = $permissions
            ->unique('id')
            ->pluck('id')
            ->values();

        return response($permissions);
    }

    public function fetchCompanies(Request $request)
    {
        $search = $request->get("query", "");

        $companies = Company::whereRaw("LOWER(name) LIKE '%{$search}%'")
                            ->limit(50)
                            ->get();

        return response($companies);
    }
}
