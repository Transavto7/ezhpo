<report-journal
    date_to="{{ $date_to }}"
    date_from="{{ $date_from }}"
    company_id="{{ $company_id }}"
></report-journal>


{{--


@isset($data['months'])
    @if(count($data['months']) && (count($data['months']) !== $hiddenMonths))
        <table id="reports-table-4" class="table table-responsive">
            <thead>
            <tr>
                @foreach($data['months'] as $month)
                    @if(!$month['hidden'])
                        <th style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="3">
                            {{ $month['name'] }} {{ $month['year'] }}
                        </th>
                    @endif
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($data['months'] as $month)
                @if(!$month['hidden'])
                    <td colspan="3" class="p-0">
                        <table class="w-100 table">
                            <thead>
                                <th>Водитель</th>
                                <th>Предрейсовые</th>
                                <th>Послерейсовые</th>

                                <th>Предсменные</th>
                                <th>Послесменные</th>

                                <th>БДД</th>
                                <th>Отчёты с карт</th>
                            </thead>

                            <tbody>
                            @foreach($month['reports'] as $driver_fio => $driver_id)
                                <tr>

                                    <td>{{ $driver_fio }} / {{ $driver_id }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Предрейсовый')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послерейсовый')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_view', 'Предсменный')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послесменный')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_anketa', 'bdd')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_anketa', 'report_cart')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $driver_id)
        ->whereMonth('date', $month['month'])->count() }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                @endif
            @endforeach
            </tbody>
        </table>
    @else
        <p>Осмотры за другие месяцы не создавались (МО)</p>
    @endif
@else
    <p>Осмотры за другие месяцы не создавались (МО)</p>
@endisset

{{&#45;&#45;ТЕХОСМОТРЫ&#45;&#45;}}
@isset($data['monthsTech'])
    @if(count($data['monthsTech']) && (count($data['monthsTech']) !== $hiddenMonthsTech))
        <table id="reports-table-4" class="table table-responsive">
            <thead>
            <tr>
                @foreach($data['monthsTech'] as $month)
                    @if(!$month['hidden'])
                        <th style="border-left: 1px solid #e9e9e9;background: #e9e9e9;" class="text-center" colspan="3">
                            {{ $month['name'] }} {{ $month['year'] }}
                        </th>
                    @endif
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($data['monthsTech'] as $month)
                @if(!$month['hidden'])
                    <td colspan="3" class="p-0">
                        <table class="w-100 table">
                            <thead>
                                <th>Автомобиль</th>
                                <th>Предрейсовые</th>
                                <th>Послерейсовые</th>

                                <th>Предсменные</th>
                                <th>Послесменные</th>

                                <th>БДД</th>
                                <th>Отчёты с карт</th>
                            </thead>

                            <tbody>
                            @foreach($month['reports'] as $car_gos_number => $car_id)
                                <tr>

                                    <td>{{ $car_gos_number }} / {{ $car_id }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Предрейсовый')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $car_id)
        ->whereMonth('date', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послерейсовый')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $car_id)
        ->whereMonth('date', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_view', 'Предсменный')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $car_id)
        ->whereMonth('date', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послесменный')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $car_id)
        ->whereMonth('date', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_anketa', 'bdd')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $car_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_anketa', 'report_cart')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $car_id)
        ->whereMonth('date', $month['month'])->count() }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </td>
                @endif
            @endforeach
            </tbody>
        </table>
    @else
        <p>Осмотры за другие месяцы не создавались (ТО)</p>
    @endif
@else
    <p>Осмотры за другие месяцы не создавались (ТО)</p>
@endisset

--}}
