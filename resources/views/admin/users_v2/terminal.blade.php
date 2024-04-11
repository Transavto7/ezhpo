@extends('layouts.app')

@section('title', 'Терминалы')
@section('sidebar', 1)

@php
    $selectedTerminals = request()->get('hash_id') ?? [];
    $selectedTowns = request()->get('town_id') ?? [];
    $selectedCompanies = request()->get('company_id') ?? [];
    $selectedPoints = request()->get('point_id') ?? []
@endphp

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            @if($current_user_permissions['permission_to_view'])
                <form action="" method="GET">
                    @if(request()->get('deleted'))
                        <input type="hidden" value="1" name="deleted">
                    @endif

                    <div class="row">
                        <div class="col-lg-3">
                            <label for="hash_id">Терминал (ID/AnyDesk/SN):</label>
                            <select multiple
                                    name="hash_id[]"
                                    data-label="hash_id"
                                    data-field="Terminal_hash_id"
                                    data-allow-clear="true"
                                    class="filled-select2 filled-select select2-hidden-accessible"
                                    aria-hidden="true">
                                <option value="" data-select2-id="8">Не установлено</option>

                                @foreach($terminals as $option)
                                    <option
                                        @if(in_array($option['id'], $selectedTerminals)) selected @endif
                                        value="{{ $option['id'] }}">
                                        {{ $option['text'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label for="pv_id">ПВ:</label>
                            <select multiple
                                    name="point_id[]"
                                    data-label="id"
                                    data-field="Points_id"
                                    data-allow-clear="true"
                                    class="filled-select2 filled-select select2-hidden-accessible"
                                    aria-hidden="true">

                                @foreach($points ?? [] as $option)
                                    <option
                                        @if(in_array($option['id'], $selectedPoints)) selected @endif
                                    value="{{ $option['id'] }}">
                                        {{ $option['text'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label for="town_id">Город:</label>
                            <select multiple
                                    name="town_id[]"
                                    data-label="hash_id"
                                    data-field="Town_hash_id"
                                    data-allow-clear="true"
                                    class="filled-select2 filled-select select2-hidden-accessible"
                                    aria-hidden="true">

                                @foreach($towns ?? [] as $option)
                                    <option
                                        @if(in_array($option['id'], $selectedTowns)) selected @endif
                                        value="{{ $option['id'] }}">
                                        {{ $option['text'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 form-group">
                            <label for="town_id">Компания:</label>
                            <select multiple
                                    name="company_id[]"
                                    data-label="hash_id"
                                    data-field="Company_hash_id"
                                    data-allow-clear="true"
                                    class="filled-select2 filled-select select2-hidden-accessible"
                                    aria-hidden="true">

                                @foreach($companies ?? [] as $option)
                                    <option
                                        @if(in_array($option['id'], $selectedCompanies)) selected @endif
                                    value="{{ $option['id'] }}">
                                        {{ $option['text'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 form-group">
                            <input type="submit" class="btn btn-success btn-sm" value="Поиск">
                            <a href="{{ route('terminals.index') }}" class="btn btn-danger btn-sm">Сбросить</a>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <admin-terminals-index
        :roles='@json($roles)'
        :deleted='{{ request()->get('deleted', 0) }}'
        :current_user_permissions='@json($current_user_permissions)'
        :all_permissions='@json($all_permissions)'
        :points='@json($pointsToTable)'
        :fields='@json($fields)'
        :devices-options='@json($devicesOptions)'
    ></admin-terminals-index>
@endsection
