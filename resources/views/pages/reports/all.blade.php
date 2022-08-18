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

                        @php
                            $pre_month = (date('m')-1);
                            $date_from_field = date('Y') . '-' . ($pre_month <= 9 ? '0' . $pre_month : $pre_month) . '-' . '01';
                            $date_to_field = date('Y') . '-' . ($pre_month <= 9 ? '0' . $pre_month : $pre_month) . '-' . cal_days_in_month(CAL_GREGORIAN, $pre_month, date('Y'));
                        @endphp

                        <div class="col-md-2">
                            <label>
                                С:
                                <input type="date" required value="{{ request()->get('date_from') ? request()->get('date_from') : $date_from_field }}" class="form-control" name="date_from">
                            </label>
                        </div>

                        <div class="col-md-2">
                            <label>
                                ПО:
                                <input type="date" required value="{{ request()->get('date_to') ? request()->get('date_to') : $date_to_field }}" class="form-control" name="date_to">
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label>
                                Тип осмотра:
                                <select name="type_anketa"  class="form-control" >
                                    <option value="medic" @if(request()->get('type_anketa') == 'medic') selected @endif>Медицинский</option>
                                    <option value="tech" @if(request()->get('type_anketa') == 'tech') selected @endif>Техничекий</option>
                                </select>
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
