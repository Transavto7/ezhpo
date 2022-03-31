@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@section('content')

    <div class="col-md-12">
        <div class="row bg-light p-2">
            <div class="col-md-10">
                <form action="" method="GET" class="elements-form-filter">
                    <input type="hidden" name="filter" value="1">
                    <div class="row">

                        @switch($type_report)
                            @case('journal')
                                <div class="col-md-2">
                                    <label>Компания</label>

                                    @include('templates.elements_field', [
                                        'v' => $company_fields,
                                        'k' => 'company_id',
                                        'is_required' => 'required',
                                        'model' => 'Company',
                                        'default_value' => request()->get('company_id')
                                    ])
                                </div>

                                <input type="hidden" name="is_finance" value="1">
                                {{--<div class="col-md-2">
                                    <label>Финансовая информация</label>

                                    @include('templates.elements_field', [
                                        'v' => [
                                            'type' => 'select',
                                            'label' => 'Финансовая информация',
                                            'values' => [
                                                0 => 'Нет',
                                                1 => 'Да'
                                            ]
                                        ],
                                        'k' => 'is_finance',
                                        'is_required' => 0,
                                        'model' => '',
                                        'default_value' => request()->get('is_finance', 0)
                                    ])
                                </div>--}}
                            @break

                            @case('graph_pv')
                                <div class="col-md-2">
                                    <label>Пункт выпуска</label>

                                    @include('templates.elements_field', [
                                        'v' => $pv_fields,
                                        'k' => 'pv_id',
                                        'is_required' => 'required',
                                        'model' => 'Point',
                                        'default_value' => request()->get('pv_id')
                                    ])
                                </div>
                            @break
                        @endswitch

                        <div class="col-md-2">
                            <label>
                                С:
                                <input type="date" required value="{{ request()->get('date_from') }}" class="form-control" name="date_from">
                            </label>
                        </div>

                        <div class="col-md-2">
                            <label>
                                ПО:
                                <input type="date" required value="{{ request()->get('date_to') ? request()->get('date_to') : date('Y-m-d') }}" class="form-control" name="date_to">
                            </label>
                        </div>

                        <div class="col-md-4">
                            <button type="submit" class="btn btn-info">Сформировать отчет</button>
                            <a href="?" class="btn btn-danger">Сбросить</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        @if(isset($_GET['filter']))
            @include('pages.reports.' . $type_report)
        @endif
    </div>

@endsection
