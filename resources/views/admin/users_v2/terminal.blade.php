@extends('layouts.app')

@section('title', 'Терминалы')
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
    <admin-terminals-index
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

                        <div class="col-lg-3">
                            <select multiple
                                    name="hash_id[]"
                                    data-label="hash_id"
                                    data-field="Terminal_hash_id"
                                    data-allow-clear="true"
                                    class="filled-select2 filled-select select2-hidden-accessible"
                                    aria-hidden="true">
                                <option value="" data-select2-id="8">Не установлено</option>

                                @php
                                    $default = request()->get('hash_id') ?? [];
                                    $terminals = app("App\\User")::with('roles')
                                        ->whereNotIn('hash_id', $default)->whereHas('roles', function ($q) {
                                        $q->where('roles.id', 9);
                                    })->get();

                                    $default = app("App\\User")::with('roles')
                                        ->whereIn('hash_id', $default)->whereHas('roles', function ($q) {
                                        $q->where('roles.id', 9);
                                        })->get();
                                @endphp
                                @foreach($default as $option)
                                    <option selected value="{{ $option['hash_id'] }}">
                                        [{{ $option['hash_id'] }}] {{ $option['name'] }}
                                    </option>
                                @endforeach
                                @foreach($terminals as $option)
                                    <option value="{{ $option['hash_id'] }}">
                                        [{{ $option['hash_id'] }}] {{ $option['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 form-group">
                            @include('profile.ankets.components.pvs', [
                                'defaultShowPvs' => 1,
                                'classesPvs' => 'form-control',
                                'points' => $points->toArray(),
                                'roles' => $roles
                            ])
                        </div>

                        <div class="col-lg-2 form-group">
                            <input type="submit" class="btn btn-success btn-sm" value="Поиск">
                            <a href="{{ route('users') }}" class="btn btn-danger btn-sm">Сбросить</a>
                        </div>

                    </form>
                @endif
            </div>
        </div>
    </admin-terminals-index>
@endsection
