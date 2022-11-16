@extends('layouts.app')

@section('title', 'Отчет по количеству осмотров')
@section('sidebar', 1)
@section('content')
    <report-dynamic-index
        :towns='JSON.parse(`@json($towns)`)'
        :points='JSON.parse(`@json($points)`)'
        town="{{ request()->get('town_id') }}"
        point="{{ request()->get('pv_id') }}"
        type="{{ $journal }}"
    >
        @if($companies)
            <div class="card">
                <h5 class="card-header">Результат отчета</h5>
                <div class="card-body">
                    <table id="reports-table-1" class="table table-striped table-sm">
                        <thead>
                        <tr>
                            <th class="text-center" width="50">№</th>
                            <th class="text-center" width="100">ID</th>
                            <th>Компания</th>
                            @foreach($months as $month)
                                <th width="250" class="text-center">{{ __('date.months.' . $month) }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @php $number = 0 @endphp
                        @foreach($companies as $company_id => $info)
                            <tr>
                                <td class="text-center" width="50">{{ ++$number }}</td>
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
                Осмотры не найдены
            </div>
        @endif
    </report-dynamic-index>
@endsection
