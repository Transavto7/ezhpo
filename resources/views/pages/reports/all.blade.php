@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@section('content')

    <div class="col-md-12 bg-light p-2 mb-3">
        <form action="" method="GET" class="elements-form-filter">
            <input type="hidden" name="filter" value="1">
            <div class="row p-2">
                @if($type_report === 'journal')
                    <div class="col-md-3 form-group">
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
                @endif

                @if($type_report === 'graph_pv')
                    <div class="col-md-3 form-group">
                        <label>Пункт выпуска</label>

                        @include('templates.elements_field', [
                            'v' => $pv_fields,
                            'k' => 'pv_id',
                            'is_required' => 'required',
                            'model' => 'Point',
                            'default_value' => request()->input('pv_id')
                        ])
                    </div>
                @endif

                @php
                    $pre_month = \Illuminate\Support\Carbon::now()->subMonths();
                    $date_from_field = $pre_month->startOfMonth()->format('Y-m-d');
                    $date_to_field = $pre_month->endOfMonth()->format('Y-m-d');
                @endphp

                <div class="col-md-3 form-group">
                    <label>
                        С:
                    </label>
                    <input type="date" required value="{{ request()->input('date_from', $date_from_field) }}" class="form-control" name="date_from">
                </div>

                <div class="col-md-3 form-group">
                    <label>
                        ПО:
                    </label>
                    <input type="date" required value="{{ request()->input('date_to', $date_to_field) }}" class="form-control" name="date_to">
                </div>

                <div class="col-md-3 form-group">
                    <label>
                        Тип осмотра:
                    </label>
                    <select name="type_anketa"  class="form-control" >
                        <option value="medic" @if(request()->input('type_anketa') == 'medic') selected @endif>Медицинский</option>
                        <option value="tech" @if(request()->input('type_anketa') == 'tech') selected @endif>Техничекий</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-info">Сформировать отчет</button>
                    <a href="?" class="btn btn-danger">Сбросить</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        @if(request()->input('filter'))
            @include('pages.reports.' . $type_report)
        @endif
    </div>

@endsection
