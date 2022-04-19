<table id="reports-table-1" class="table table-striped table-sm">
    <thead>
    <tr>
        <th>Отчет по дате осмотра</th>
    </tr>
    <tr>
        <th width="100">ID</th>
        <th width="250">Водители</th>
        <th width="150">Предрейсовые</th>
        <th width="150">Послерейсовые</th>

        <th width="150">Предсменные</th>
        <th width="150">Послесменные</th>

        <th width="150">БДД</th>
        <th width="150">Отчёты с карт</th>
    </tr>
    </thead>
    <tbody>
    @if(count($reports) > 0)
        @foreach($reports->unique('driver_id') as $report)
            @php $predr = \App\Anketa::where('type_view', 'Предрейсовый')
->where('company_id', $company_id)
->where('in_cart', 0)
->where('type_anketa', 'medic')
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $posler = \App\Anketa::where('type_view', 'Послерейсовый')
->where('company_id', $company_id)
->where('in_cart', 0)
->where('type_anketa', 'medic')
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $predsmenniy = \App\Anketa::where('type_view', 'Предсменный')
->where('in_cart', 0)
->where('company_id', $company_id)
->where('type_anketa', 'medic')
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp

            @php $poslesmenniy = \App\Anketa::where('type_view', 'Послесменный')
->where('in_cart', 0)
->where('company_id', $company_id)
->where('type_anketa', 'medic')
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp

            @php $bdd = \App\Anketa::where('type_anketa', 'bdd')
->where('company_id', $company_id)
->where('in_cart', 0)
->where('driver_id', $report->driver_id)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $report_cart = \App\Anketa::where('type_anketa', 'report_cart')
->where('company_id', $company_id)
->where('driver_id', $report->driver_id)
->where('in_cart', 0)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $syncFieldsDrivers = \App\Driver::getAutoSyncFields($report->driver_id); @endphp

            <tr>
                <td width="100">
                    {{ $report->driver_id }}
                </td>
                <td width="250">
                    {{ $report->driver_fio }}

                    <div>
                        @if($syncFieldsDrivers)
                            @if(count($syncFieldsDrivers) > 0)
                                <span class="text-bold text-success"><i class="fa fa-refresh"></i></span>
                            @endif
                        @endif

{{--                        @foreach(\App\Driver::getAutoSyncFields($report->driver_id) as $aSyncField)--}}
{{--                            <span class="text-bold text-success"><i class="fa fa-refresh"></i> {{ __($aSyncField) }}</span>--}}
{{--                        @endforeach--}}
                    </div>
                </td>
                <td width="150">
                    {{ $predr }}

                    @if(request()->get('is_finance') && $predr > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'medic', 'Предрейсовый', $predr) !!}
                    @endif
                </td>
                <td width="150">
                    {{ $posler }}

                    @if(request()->get('is_finance') && $posler > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'medic', 'Послерейсовый', $posler) !!}
                    @endif
                </td>

                <td width="150">
                    {{ $predsmenniy }}

                    @if(request()->get('is_finance') && $predsmenniy > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'medic', 'Предсменный', $predsmenniy) !!}
                    @endif
                </td>

                <td width="150">
                    {{ $poslesmenniy }}

                    @if(request()->get('is_finance') && $poslesmenniy > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'medic', 'Послесменный', $poslesmenniy) !!}
                    @endif
                </td>

                <td width="150">
                    {{ $bdd }}

                    @if(request()->get('is_finance') && $bdd > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'bdd', 'БДД', $bdd) !!}
                    @endif
                </td>
                <td width="150">
                    {{ $report_cart }}

                    @if(request()->get('is_finance') && $report_cart > 0)
                        {!! \App\Driver::calcServices($report->driver_id, 'report_cart', 'Отчеты с карт', $report_cart) !!}
                    @endif
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

<table id="reports-table-2" class="table table-striped table-sm">
    <thead>
    <tr>
        <th width="100">ID</th>
        <th width="250">Автомобили</th>
        <th width="150">Предрейсовые</th>
        <th width="150">Послерейсовые</th>

        <th width="150">Предсменные</th>
        <th width="150">Послесменные</th>

        <th width="150">БДД</th>
        <th width="150">Отчёты с карт</th>
    </tr>
    </thead>
    <tbody>
    @if(count($reports2) > 0)
        @foreach($reports2 as $report)
            @php $predr = \App\Anketa::where('type_view', 'Предрейсовый')
->where('in_cart', 0)
->where('company_id', $company_id)
->where('type_anketa', 'tech')
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp

            @php $posler = \App\Anketa::where('type_view', 'Послерейсовый')
->where('in_cart', 0)->where('company_id', $company_id)
->where('type_anketa', 'tech')
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp

            @php $predsmenniy = \App\Anketa::where('type_view', 'Предсменный')
->where('in_cart', 0)->where('company_id', $company_id)
->where('type_anketa', 'tech')
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp

            @php $poslesmenniy = \App\Anketa::where('type_view', 'Послесменный')
->where('in_cart', 0)
->where('company_id', $company_id)
->where('type_anketa', 'tech')
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                        $date_from." 00:00:00",
                        $date_to." 23:59:59"
                    ])->count(); @endphp

            @php $bdd = \App\Anketa::where('type_anketa', 'bdd')
->where('company_id', $company_id)
->where('in_cart', 0)
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $report_cart = \App\Anketa::where('type_anketa', 'report_cart')
->where('company_id', $company_id)
->where('in_cart', 0)
->where('car_gos_number', $report->car_gos_number)
->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                            $date_from." 00:00:00",
                            $date_to." 23:59:59"
                        ])->count(); @endphp

            @php $syncCarFields = \App\Car::getAutoSyncFields($report->car_id); @endphp

            <tr>
                <td width="100">
                    {{ $report->car_id }}
                </td>
                <td width="250">
                    {{ $report->car_gos_number }}

                    <div>
                        @if($syncCarFields)
                            @if(count($syncCarFields) > 0)
                                <span class="text-bold text-success"><i class="fa fa-refresh"></i></span>
                            @endif
                        @endif

{{--                        @foreach($syncCarFields as $aSyncField)--}}
{{--                            <span class="text-bold text-success"><i class="fa fa-refresh"></i> {{ __($aSyncField) }}</span>--}}
{{--                        @endforeach--}}
                    </div>
                </td>
                <td width="150">
                    {{ $predr }}

                    @if(request()->get('is_finance') && $predr > 0)
                        {!! \App\Car::calcServices($report->car_id, 'tech', 'Предрейсовый', $predr) !!}
                    @endif
                </td>
                <td>
                    {{ $posler }}

                    @if(request()->get('is_finance') && $posler > 0)
                        {!! \App\Car::calcServices($report->car_id, 'tech', 'Послерейсовый', $posler) !!}
                    @endif
                </td>

                <td>
                    {{ $predsmenniy }}

                    @if(request()->get('is_finance') && $predsmenniy > 0)
                        {!! \App\Car::calcServices($report->car_id, 'tech', 'Предсменный', $predsmenniy) !!}
                    @endif
                </td>

                <td>
                    {{ $poslesmenniy }}

                    @if(request()->get('is_finance') && $poslesmenniy > 0)
                        {!! \App\Car::calcServices($report->car_id, 'tech', 'Послесменный', $poslesmenniy) !!}
                    @endif
                </td>

                <td width="150">
                    {{ $bdd }}

                    @if(request()->get('is_finance') && $bdd > 0)
                        {!! \App\Driver::calcServices($report->car_id, 'bdd', 'БДД', $bdd) !!}
                    @endif
                </td>

                <td width="150">
                    {{ $report_cart }}

                    @if(request()->get('is_finance') && $report_cart > 0)
                        {!! \App\Driver::calcServices($report->car_id, 'report_cart', 'Отчеты с карт', $report_cart) !!}
                    @endif
                </td>

            </tr>
        @endforeach
    @endif
    </tbody>
</table>

<table id="reports-table-3" class="table table-striped table-sm">
    <thead>
    <tr>
        <th>Печать ПЛ</th>
    </tr>
    </thead>
    <tbody>
    <td>{{ \App\Anketa::where('type_anketa', 'pechat_pl')
            ->where('in_cart', 0)
            ->where('company_name', \App\Company::where('hash_id', $company_id)->first()->name)
            ->whereRaw("($date_field >= ? AND $date_field <= ?)", [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->count() }}</td>
    </tbody>
</table>

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
                            @foreach($month['reports'] as $report)
                                <tr>

                                    <td>{{ $report->driver_fio }} / {{ $report->driver_id }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Предрейсовый')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послерейсовый')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_view', 'Предсменный')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послесменный')
        ->where('type_anketa', 'medic')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_anketa', 'bdd')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_anketa', 'report_cart')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('driver_id', $report->driver_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>

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

{{--ТЕХОСМОТРЫ--}}
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
                            @foreach($month['reports'] as $report)
                                <tr>

                                    <td>{{ $report->car_gos_number }} / {{ $report->car_id }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Предрейсовый')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $report->car_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послерейсовый')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $report->car_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_view', 'Предсменный')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $report->car_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_view', 'Послесменный')
        ->where('type_anketa', 'tech')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $report->car_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>

                                    <td>{{ \App\Anketa::where('type_anketa', 'bdd')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $report->car_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>
                                    <td>{{ \App\Anketa::where('type_anketa', 'report_cart')
        ->where('in_cart', 0)
        ->where('company_id', $company_id)
        ->where('car_id', $report->car_id)
        ->whereMonth('created_at', $month['month'])->count() }}</td>

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
