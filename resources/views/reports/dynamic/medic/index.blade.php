@extends('layouts.app')

@section('title', 'Отчет по количеству медицинских отчетов')
@section('sidebar', 1)
@section('content')
    <div class="card mb-4" style="overflow-x: inherit">
        <h5 class="card-header">Выбор информации</h5>
        <div class="card-body">
            <form action="{{ route('report.dynamic.medic') }}" method="GET"
                  onsubmit="document.querySelector('#page-preloader').classList.remove('hide')">
                <div class="row">
                    <div class="form-group col-lg-3">
                        <label class="mb-1" for="company">Город</label>
                        @include('templates.elements_field', [
                            'v' => [
                                    'type'       => 'select',
                                    'values'     => 'Town',
                                    'getFieldKey' => 'id',
                                    'getField' => 'name'
                                ],
                            'k' => 'town_id',
                            'is_required' => 'no',
                            'model' => 'Town',
                            'default_value' => request()->get('town_id')
                        ])
                    </div>

                    <div class="form-group col-lg-3">
                        <label class="mb-1" for="company">Пункт выпуска</label>
                        @include('templates.elements_field', [
                            'v' => ['type' => 'select', 'values' => 'Point', 'getFieldKey' => 'id', 'getField' => 'name'],
                            'k' => 'pv_id',
                            'is_required' => 'no',
                            'model' => 'Point',
                            'default_value' => request()->get('pv_id') ? [request()->get('pv_id')] : []
                        ])
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-12">
                        <button class="btn btn-info">
                            Сформировать отчет
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($companies)
        <div class="card">
            <h5 class="card-header">Результат отчета</h5>
            <div class="card-body">
                <table id="reports-table-1" class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th>Компания</th>
                        @foreach($months as $month)
                            <th width="250" class="text-center">{{ __('date.months.' . $month) }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($companies as $company_id => $info)
                        <tr>
                            <td width="100">{{ $company_id }}</td>
                            <td width="250">{{ $info['name'] }}</td>
                            @foreach($months as $month)
                                <td width="250" class="text-center">{{ $info[$month] }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <td width="100"></td>
                        <td width="250">Всего</td>
                        @foreach($months as $month)
                            <td width="250" class="text-center">{{ $total[$month] }}</td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @elseif (request()->get('pv_id') || request()->get('town_id'))
        <div class="alert alert-secondary" role="alert">
            Медосмотры не найдены
        </div>
    @endif
@endsection
