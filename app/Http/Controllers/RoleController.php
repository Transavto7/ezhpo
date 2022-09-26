<?php

namespace App\Http\Controllers;

use App\FieldPrompt;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Role;
//use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        if(request()->get('deleted')){
            $roles = Role::onlyTrashed()->get();
        }else{
            $roles = Role::whereNull('deleted_at')->get();
        }

        $fields = FieldPrompt::where('type', 'roles')->get();

        return view('admin.groups.index')
            ->with([
                'roles' => $roles,
                'all_permissions' => \Spatie\Permission\Models\Permission::orderBy('guard_name')->get(),
                'fields' => $fields,
            ]);
    }


    /**
     * Создать
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => ['required', 'string', 'min:1', 'max:255'],
            'guard_name' => ['required', 'string', 'min:1', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
                'status'  => false,
            ]);
        }

        $data = $validator->validated();

        $role = Role::create(Arr::only($data, ['name', 'guard_name']));

        $role->permissions()->sync($request->permissions);

        return response([
            'status' => $role->save(),
        ]);
    }

    /**
     * show one elem
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        return response(Role::with(['permissions'])->find($id));
    }

    /**
     * Редактирование
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->params;

        $role = Role::find($id);
        $role->name = $data['name'];
        $role->guard_name = $data['guard_name'];

        $role->permissions()->sync($data['permissions']);

        return response([
            'status' => $role->save()
        ]);
    }

    /**
     * delete
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $role = Role::with(['users', 'permissions'])
                    ->find($id);

        $role->deleted_id = user()->id;
        $role->deleted_at = now();

//        dd($role->users());
//        $role->users()->pivot->deleted = 1;
//        $role->permissions()->pivot->deleted = 1;
//        dd($role);
        $role->users()
             ->get()
             ->each(function($option) {
                $option->pivot->deleted = 1;
                $option->pivot->save();
            });
        $role->permissions()
             ->get()
             ->each(function($option) {
                $option->pivot->deleted = 1;
                $option->pivot->save();
            });
//        $role->users()->save();
//        $role->permissions()->save();

//        $role->users()->updateExistingPivot($id, ['deleted' => 1]);
//        $role->permissions()->updateExistingPivot($id, ['deleted' => 1]);

        $role->save();

        return response([
            'status' => 1
        ]);
    }

    public function returnTrash(Request $request)
    {
        $role = Role::onlyTrashed()->find($request->post('id'));

        $role->deleted_id = null;
        $role->deleted_at = null;

//        $role->users()->updateExistingPivot($request->post('id'), ['deleted' => 1]);
//        $role->permissions()->updateExistingPivot($request->post('id'), ['deleted' => 1]);

        $role->users(true)
             ->get()
             ->each(function($option) {
                 $option->pivot->deleted = 0;
                 $option->pivot->save();
             });

        $role->permissions(true)
             ->get()
             ->each(function($option) {
                 $option->pivot->deleted = 0;
                 $option->pivot->save();
             });

        $role->save();

        return response([
            'status' => 1,
        ]);
    }
}
