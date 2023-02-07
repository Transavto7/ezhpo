@extends('layouts.app')

@section('title', 'Сотрудники')
@section('sidebar', 1)
@php
    $points = \App\Town::with(['pvs'])->get();
    $roles = \App\Role::whereNull('deleted_at')->get();
    $all_permissions = \Spatie\Permission\Models\Permission::orderBy('guard_name')->get();

    $current_user_permissions = [
            'permission_to_edit' => user()->access('employee_update'),
            'permission_to_view' => user()->access('employee_read'),
            'permission_to_create' => user()->access('employee_create'),
            'permission_to_delete' => user()->access('employee_delete'),
            'permission_to_trash' => user()->access('employee_trash'),
        ];
    $permissionToTrashView  = user()->access('employee_trash');
@endphp
@section('content')
    <admin-users-index
            :roles='@json($roles)'
            :deleted='{{ request()->get('deleted', 0) }}'
            :current_user_permissions='@json($current_user_permissions)'
            :all_permissions='@json($all_permissions)'
            :points='@json($points->map( function ($q) {
                $res['label'] = $q->name;
                foreach ($q->pvs as $pv){
                    $res['options'][] = ['value' => $pv['id'], 'text' => $pv['name']];
                }
                return $res;
            }))'
            :fields='@json($fields)'
    >
        <div class="card mb-3">
            <div class="card-body">
                @if($current_user_permissions['permission_to_view'])
                    <form action="" class="row" method="GET">
                        @isset($_GET['deleted'])
                            <input type="hidden" value="1" name="deleted">
                        @endif
                        <div class="col-md-2 form-group">
                            @php
                                $selectedValue = request()->get('hash_id');
                                $usersList = app("App\\User")::select(
                                                    DB::raw('concat("[", hash_id, "] ", name) as name'),
                                                    'hash_id')
                                                    ->where(function ($query) {
                                                        $query->whereDoesntHave('roles')
                                                              ->orWhereHas('roles', function ($q) {
                                                                  $q->whereNotIn('roles.id', [3, 6, 9]);
                                                              });
                                                    })->get();
                            @endphp
                            <input list="users_hash_id" name="hash_id" class="form-control" placeholder="Введите имя или ID сотрудника" @if(!empty($selectedValue)) value="{{ $selectedValue }}" @endif>
                            <datalist id="users_hash_id">
                                @foreach ($usersList as $userRecord)
                                    <option value="{{ $userRecord->hash_id }}" @if ($userRecord->hash_id == $selectedValue) selected @endif>{{ $userRecord->name }}</option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-md-2 form-group">
                            <input type="text" name="email" value="{{ request()->get('email') }}" placeholder="E-mail"
                                   class="form-control">
                        </div>
                        <div class="col-md-2 form-group">
                            <select class="form-control" name="role" style="color: gray;">
                                <option value="" selected>Роль</option>
                                @foreach(\App\Role::get() as $role)
                                    @if($role->name == 'driver')
                                        @continue
                                    @endif
                                    <option value="{{$role->id}}" <?= $role->id == request()->get('role') ? 'selected' : '' ?>>{{$role->guard_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md form-group">
                            @include('profile.ankets.components.pvs', [
                                'defaultShowPvs' => 1,
                                'classesPvs' => 'form-control',
                                'points' => $points->toArray(),
                                'roles' => $roles
                            ])
                        </div>

                        <div class="col-md form-group">
                            <input type="submit" class="btn btn-success btn-sm" value="Поиск">
                            <a href="{{ route('users') }}" class="btn btn-danger btn-sm">Сбросить</a>
                        </div>

                    </form>
                @endif
            </div>
        </div>
    </admin-users-index>
@endsection
