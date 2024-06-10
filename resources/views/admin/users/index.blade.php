@extends('layouts.app')

@section('title', 'Сотрудники')
@section('sidebar', 1)

@php
    $selectedTerminals = request()->get('hash_id') ?? [];
    $selectedPoints = request()->get('point_id') ?? [];
    $selectedRole = request()->get('role') ?? null;
@endphp

@section('content')
    <admin-users-index
        :roles='@json($roles)'
        :deleted='{{ request()->get('deleted', 0) }}'
        :current_user_permissions='@json($current_user_permissions)'
        :all_permissions='@json($all_permissions)'
        :points='@json($points_to_table)'
        :fields='@json($fields)'
    >
        <div class="card mb-3">
            <div class="card-body">
                @if($current_user_permissions['permission_to_view'])
                <form action="" method="GET">
                    @if(request()->get('deleted'))
                        <input type="hidden" value="1" name="deleted">
                    @endif

                        <div class="row">
                            <div class="col-lg-3 form-group">
                                <select class="form-control" name="role" style="color: gray;">
                                    <option value="" selected>Роль</option>
                                    @foreach($roles_to_filter ?? [] as $option)
                                        <option
                                            @if($option['id'] == $selectedRole) selected @endif
                                        value="{{ $option['id'] }}">
                                            {{ $option['text'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3 form-group">
                                <input type="text" name="email" value="{{ request()->get('email') }}" placeholder="E-mail"
                                       class="form-control">
                            </div>
                        </div>

                    <div class="row">
                        <div class="col-lg-6">
                            @include('templates.elements_field', [
                                'v' => [
                                    'type' => 'select',
                                    'values' => 'User',
                                    'getField' => 'name',
                                    'getFieldKey' => 'hash_id',
                                    'multiple' => 1,
                                    'concatField' => 'hash_id'
                                ],
                                'model' => 'User',
                                'k' => 'hash_id',
                                'is_required' => '',
                                'default_value' => request()->get('hash_id')
                            ])
                        </div>

                        <div class="col-lg-6 form-group">
                            <select multiple
                                    name="point_id[]"
                                    data-label="id"
                                    data-field="Points_id"
                                    data-allow-clear="true"
                                    data-placeholder="ПВ"
                                    class="filled-select2 filled-select select2-hidden-accessible"
                                    aria-hidden="true">
                                <option></option>
                                @foreach($points ?? [] as $option)
                                    <option
                                        @if(in_array($option['id'], $selectedPoints)) selected @endif
                                    value="{{ $option['id'] }}">
                                        {{ $option['text'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 form-group">
                            <input type="submit" class="btn btn-success btn-sm" value="Поиск">
                            <a href="{{ route('users') }}" class="btn btn-danger btn-sm">Сбросить</a>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </admin-users-index>
@endsection

@push('setup-scripts')
    <script>
        window.PAGE_SETUP = {
            LOGS_MODAL: {
                tableDataUrl: '{{ route('logs.list-model') }}',
                mapDataUrl: '{{ route('logs.list-model-map') }}',
                model: '{{ 'users' }}',
            },
            MODEL_SEARCHER: {
                tableDataUrl: '{{ route('searchElement') }}',
            }
        };
    </script>
@endpush
