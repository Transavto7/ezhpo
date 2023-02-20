@extends('layouts.app')

@section('title', 'Отчет по количеству осмотров')
@section('sidebar', 1)
@section('content')
    <report-dynamic-index
            :towns='@json($towns)'
            :points='@json($points)'
            :companies='@json($company_id)'
            :sel-towns=`@json(request()->get('town_id'))`
            :sel-points=`@json(request()->get('pv_id'))`
            :sel-companies=`@json(request()->get('company_id'))`
            order="{{ request()->get('order_by') ?? 'execute' }}"
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
                                    <td width="250" class="text-center">{{ $info[$month] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach

                        <tr>
                            <td width="50"></td>
                            <td width="100"></td>
                            <td width="250">Всего</td>
                            @foreach($months as $month)
                                <td width="250" class="text-center">{{ $total[$month] ?? 0 }}</td>
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

    <canvas id="chart"></canvas>
@endsection

@section('custom-scripts')
    <script>
        const monthName = {
            'February': 'Февраль',
            'January': 'Январь',
            'December': 'Декабрь',
            'November': 'Ноябрь',
            'October': 'Октябрь',
            'September': 'Сентябрь',
            'August': 'Август',
            'July': 'Июль',
            'June': 'Июнь',
            'May': 'Май',
            'April': 'Апрель',
            'March': 'Март'
        };

        let months = @json($months);
        const data = @json($total);
        const result = [];
        for (key in data) {
            result.push(data[key]);
        }

        months = months.map(item => {
            if (monthName[item]) {
                return monthName[item];
            }
            return item;
        });

        console.log(months);

        const ctx = document.getElementById('chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months.map((month) => month.charAt(0).toUpperCase() + month.slice(1)),
                datasets: [{
                    label: 'Количество проведённых осмотров',
                    backgroundColor: 'rgb(196, 219, 231)',
                    borderColor: 'rgb(23,66,231)',
                    minBarLength: 1,
                    borderWidth: 1,
                    data: result
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            },
        })
    </script>
@endsection
